<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ServicioSeeder::class);

        $usuario = User::create([
            'name' => 'Paciente de Prueba',
            'email' => 'paciente@example.com',
            'password' => 'Prueba12345',
            'email_verified_at' => now(),
        ]);

        $servicio = Servicio::first();

        foreach ([2 => 10, 3 => 11, 4 => 16] as $dias => $hora) {
            Cita::create([
                'user_id' => $usuario->id,
                'servicio_id' => $servicio->id,
                'fecha_hora' => now()->addDays($dias)->setTime($hora, 0),
                'estado' => 'pendiente',
                'notas' => 'Cita de ejemplo',
            ]);
        }
    }
}