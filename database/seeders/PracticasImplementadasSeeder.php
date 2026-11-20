<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PracticaImplementada;

class PracticasImplementadasSeeder extends Seeder
{
    public function run(): void
    {
        $practicas = [
            ['clave' => 'DSR', 'nombre' => 'Diagnóstico de Sistema de Riego'],
            ['clave' => 'DSP', 'nombre' => 'Diagnóstico de Sistema Productivo'],
            ['clave' => 'AS',  'nombre' => 'Análisis de Suelo'],
            ['clave' => 'AA',  'nombre' => 'Análisis de Agua'],
            ['clave' => 'PR',  'nombre' => 'Programación de Riego'],
            ['clave' => 'PF',  'nombre' => 'Planeación de Fertilización'],
            ['clave' => 'IMO', 'nombre' => 'Incorporación de Materia Orgánica'],
            ['clave' => 'PS',  'nombre' => 'Planeación de Siembra'],
            ['clave' => 'DR',  'nombre' => 'Descanso regenerativo'],
            ['clave' => 'MD',  'nombre' => 'Monitoreo con Dron'],
        ];

        foreach ($practicas as $practica) {
            PracticaImplementada::updateOrCreate(
                ['clave' => $practica['clave']],
                [
                    'nombre' => $practica['nombre'],
                    'activo' => true,
                ]
            );
        }
    }
}