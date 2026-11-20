<?php

use App\Models\Servicio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/** @property \App\Models\Servicio $servicio */
beforeEach(function () {
    $this->servicio = Servicio::create([
        'nombre' => 'Consulta general',
        'duracion_minutos' => 30,
        'precio' => 400,
    ]);
});

function datosCita($servicio, $hora = 10): array
{
    return [
        'servicio_id' => $servicio->id,
        'fecha_hora' => now()->addDay()->setTime($hora, 0)->format('Y-m-d H:i:s'),
    ];
}

it('manda al login a un visitante', function () {
    $this->get('/citas')->assertRedirect('/login');
});

it('agenda una cita válida', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/citas', datosCita($this->servicio))
        ->assertRedirect('/citas');

    expect($user->citas()->count())->toBe(1);
});

it('rechaza una cita fuera de horario', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/citas', datosCita($this->servicio, 20))
        ->assertSessionHasErrors('fecha_hora');
});

it('rechaza una cita en el pasado', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/citas', [
        'servicio_id' => $this->servicio->id,
        'fecha_hora' => now()->subDay()->setTime(10, 0)->format('Y-m-d H:i:s'),
    ])->assertSessionHasErrors('fecha_hora');
});

it('rechaza dos citas a la misma hora', function () {
    $primero = User::factory()->create();
    $segundo = User::factory()->create();

    $this->actingAs($primero)->post('/citas', datosCita($this->servicio));

    $this->actingAs($segundo)->post('/citas', datosCita($this->servicio))
        ->assertSessionHasErrors('fecha_hora');
});

it('no deja cancelar la cita de otra persona', function () {
    $dueno = User::factory()->create();
    $otro = User::factory()->create();
    $cita = $dueno->citas()->create(datosCita($this->servicio));

    $this->actingAs($otro)->delete("/citas/{$cita->id}")->assertForbidden();
});

it('deja cancelar la cita propia', function () {
    $user = User::factory()->create();
    $cita = $user->citas()->create(datosCita($this->servicio));

    $this->actingAs($user)->delete("/citas/{$cita->id}");

    expect($user->citas()->count())->toBe(0);
});