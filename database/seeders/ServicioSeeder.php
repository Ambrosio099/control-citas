<?php

namespace Database\Seeders;

use App\Models\Servicio;
use Illuminate\Database\Seeder;

class ServicioSeeder extends Seeder
{
    public function run(): void
    {

        $servicios = [
            ['nombre' => 'Consulta general', 'duracion_minutos' => 30, 'precio' => 400],
            ['nombre' => 'Consulta de seguimiento', 'duracion_minutos' => 20, 'precio' => 300],
            ['nombre' => 'Revisión de resultados', 'duracion_minutos' => 15, 'precio' => 250],
            ['nombre' => 'Certificado médico', 'duracion_minutos' => 15, 'precio' => 200],
            ['nombre' => 'Toma de presión y glucosa', 'duracion_minutos' => 15, 'precio' => 100],
        ];

        foreach ($servicios as $servicio) {
            Servicio::create($servicio);
        }
    }
    //
}
