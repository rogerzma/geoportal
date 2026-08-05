<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cultivo;

class CultivosSeeder extends Seeder
{
    public function run(): void
    {
        $cultivos = [

            [
                'nombre' => 'Alfalfa',
                'nombre_cientifico' => 'Medicago sativa',
                'categoria' => 'Forrajero',
                'color' => '#00692C',
            ],

            [
                'nombre' => 'Algodon',
                'nombre_cientifico' => 'Gossypium hirsutum',
                'categoria' => 'Industrial',
                'color' => '#EDEDED',
            ],

            [
                'nombre' => 'Ajo',
                'nombre_cientifico' => 'Allium sativum',
                'categoria' => 'Hortaliza',
                'color' => '#FFE880',
            ],

            [
                'nombre' => 'Avena',
                'nombre_cientifico' => 'Avena sativa',
                'categoria' => 'Cereal',
                'color' => '#B2BABB',
            ],

            [
                'nombre' => 'Cebada',
                'nombre_cientifico' => 'Hordeum vulgare',
                'categoria' => 'Cereal',
                'color' => '#A0522D',
            ],

            [
                'nombre' => 'Cebolla',
                'nombre_cientifico' => 'Allium cepa',
                'categoria' => 'Hortaliza',
                'color' => '#D7BDE2',
            ],

            [
                'nombre' => 'Chile',
                'nombre_cientifico' => 'Capsicum annuum',
                'categoria' => 'Hortaliza',
                'color' => '#1A6E0D',
            ],

            [
                'nombre' => 'Ciruela',
                'nombre_cientifico' => 'Prunus domestica',
                'categoria' => 'Frutal',
                'color' => '#7E57C2',
            ],

            [
                'nombre' => 'Durazno',
                'nombre_cientifico' => 'Prunus persica',
                'categoria' => 'Frutal',
                'color' => '#FFB74D',
            ],

            [
                'nombre' => 'En descanso',
                'nombre_cientifico' => null,
                'categoria' => 'Otra',
                'color' => '#7D7D7D',
            ],

            [
                'nombre' => 'Fresa',
                'nombre_cientifico' => 'Fragaria × ananassa',
                'categoria' => 'Frutal',
                'color' => '#FF0D3D',
            ],

            [
                'nombre' => 'Frijol',
                'nombre_cientifico' => 'Phaseolus vulgaris',
                'categoria' => 'Leguminosa',
                'color' => '#57352B',
            ],

            [
                'nombre' => 'Guayaba',
                'nombre_cientifico' => 'Psidium guajava',
                'categoria' => 'Frutal',
                'color' => '#E91E63',
            ],

            [
                'nombre' => 'Maiz',
                'nombre_cientifico' => 'Zea mays',
                'categoria' => 'Cereal',
                'color' => '#F9A825',
            ],

            [
                'nombre' => 'Manzana',
                'nombre_cientifico' => 'Malus domestica',
                'categoria' => 'Frutal',
                'color' => '#8BC34A',
            ],

            [
                'nombre' => 'Nogal',
                'nombre_cientifico' => 'Juglans regia',
                'categoria' => 'Frutal',
                'color' => '#8B4513',
            ],

            [
                'nombre' => 'Nopal',
                'nombre_cientifico' => 'Opuntia ficus-indica',
                'categoria' => 'Frutal',
                'color' => '#388E3C',
            ],

            [
                'nombre' => 'Pepino',
                'nombre_cientifico' => 'Cucumis sativus',
                'categoria' => 'Hortaliza',
                'color' => '#4CAF50',
            ],

            [
                'nombre' => 'Sorgo',
                'nombre_cientifico' => 'Sorghum bicolor',
                'categoria' => 'Cereal',
                'color' => '#8D5524',
            ],

            [
                'nombre' => 'Tomate',
                'nombre_cientifico' => 'Solanum lycopersicum',
                'categoria' => 'Hortaliza',
                'color' => '#E53935',
            ],

            [
                'nombre' => 'Tomatillo',
                'nombre_cientifico' => 'Physalis philadelphica',
                'categoria' => 'Hortaliza',
                'color' => '#32CD32',
            ],

            [
                'nombre' => 'Trigo',
                'nombre_cientifico' => 'Triticum aestivum',
                'categoria' => 'Cereal',
                'color' => '#F4D03F',
            ],

            [
                'nombre' => 'Uva',
                'nombre_cientifico' => 'Vitis vinifera',
                'categoria' => 'Frutal',
                'color' => '#360869',
            ],

            [
                'nombre' => 'Zanahoria',
                'nombre_cientifico' => 'Daucus carota',
                'categoria' => 'Hortaliza',
                'color' => '#FF9800',
            ],

        ];

        foreach ($cultivos as $cultivo) {

            Cultivo::updateOrCreate(

                [
                    'nombre' => $cultivo['nombre']
                ],

                [
                    'nombre_cientifico' => $cultivo['nombre_cientifico'],
                    'categoria' => $cultivo['categoria'],
                    'color' => strtoupper($cultivo['color']),
                    'activo' => true,
                    'created_by' => 12
                ]
            );

        }
    }
}