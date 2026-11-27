<?php

use App\Models\Servicio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/** @property Servicio $servicio */
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

it('permite consultar una cita propia', function () {
    $user = User::factory()->create();
    $cita = $user->citas()->create(datosCita($this->servicio));

    $this->actingAs($user)
        ->get("/citas/{$cita->id}")
        ->assertOk();
});

it('no permite consultar una cita ajena', function () {
    $dueno = User::factory()->create();
    $otro = User::factory()->create();
    $cita = $dueno->citas()->create(datosCita($this->servicio));

    $this->actingAs($otro)
        ->get("/citas/{$cita->id}")
        ->assertForbidden();
});

it('permite abrir el formulario de edicion propio', function () {
    $user = User::factory()->create();
    $cita = $user->citas()->create(datosCita($this->servicio));

    $this->actingAs($user)
        ->get("/citas/{$cita->id}/edit")
        ->assertOk();
});

it('no permite abrir el formulario de edicion ajeno', function () {
    $dueno = User::factory()->create();
    $otro = User::factory()->create();
    $cita = $dueno->citas()->create(datosCita($this->servicio));

    $this->actingAs($otro)
        ->get("/citas/{$cita->id}/edit")
        ->assertForbidden();
});

it('permite actualizar una cita propia', function () {
    $user = User::factory()->create();
    $cita = $user->citas()->create(datosCita($this->servicio));

    $datos = datosCita($this->servicio, 11);
    $datos['notas'] = 'Cita actualizada';

    $this->actingAs($user)
        ->put("/citas/{$cita->id}", $datos)
        ->assertRedirect('/citas');

    $this->assertDatabaseHas('citas', [
        'id' => $cita->id,
        'fecha_hora' => $datos['fecha_hora'],
        'notas' => 'Cita actualizada',
    ]);
});

it('no permite actualizar una cita ajena', function () {
    $dueno = User::factory()->create();
    $otro = User::factory()->create();
    $cita = $dueno->citas()->create(datosCita($this->servicio));

    $this->actingAs($otro)
        ->put("/citas/{$cita->id}", datosCita($this->servicio, 11))
        ->assertForbidden();

    $this->assertDatabaseHas('citas', [
        'id' => $cita->id,
        'fecha_hora' => datosCita($this->servicio)['fecha_hora'],
    ]);
});

it('permite editar conservando el horario original', function () {
    $user = User::factory()->create();
    $cita = $user->citas()->create(datosCita($this->servicio));

    $datos = datosCita($this->servicio);
    $datos['notas'] = 'Nuevas notas';

    $this->actingAs($user)
        ->put("/citas/{$cita->id}", $datos)
        ->assertRedirect('/citas');

    $this->assertDatabaseHas('citas', [
        'id' => $cita->id,
        'notas' => 'Nuevas notas',
    ]);
});

it('rechaza actualizar una cita a un horario ocupado', function () {
    $primero = User::factory()->create();
    $segundo = User::factory()->create();

    $primero->citas()->create(datosCita($this->servicio, 10));
    $cita = $segundo->citas()->create(datosCita($this->servicio, 11));

    $this->actingAs($segundo)
        ->put("/citas/{$cita->id}", datosCita($this->servicio, 10))
        ->assertSessionHasErrors('fecha_hora');
});

it('rechaza actualizar una cita a una fecha pasada', function () {
    $user = User::factory()->create();
    $cita = $user->citas()->create(datosCita($this->servicio));

    $datos = datosCita($this->servicio);

    $datos['fecha_hora'] = now()
        ->subDay()
        ->setTime(10, 0)
        ->format('Y-m-d H:i:s');

    $this->actingAs($user)
        ->put("/citas/{$cita->id}", $datos)
        ->assertSessionHasErrors('fecha_hora');
});

it('rechaza actualizar una cita fuera de horario', function () {
    $user = User::factory()->create();
    $cita = $user->citas()->create(datosCita($this->servicio));

    $this->actingAs($user)
        ->put("/citas/{$cita->id}", datosCita($this->servicio, 20))
        ->assertSessionHasErrors('fecha_hora');
});
