<?php

namespace Database\Seeders;

use App\Models\Predio;
use App\Models\Zona;
use Illuminate\Database\Seeder;

class ZonasSeeder extends Seeder
{
    public function run(): void
    {
        $predio = Predio::where('codigo', 'P-QUINTA')->first();
        if (! $predio) {
            return;
        }

        $zonas = [
            [
                'nombre' => 'General',
                'codigo' => 'Z-GEN',
                'descripcion' => 'Espacios de riego y acceso principal a la quinta.',
                'ubicacion' => 'Sector central y accesos externos',
            ],
            [
                'nombre' => 'Cabaña',
                'codigo' => 'Z-CAB',
                'descripcion' => 'Área destinada a hospedaje con cabañas y baños.',
                'ubicacion' => 'Lado oeste, junto al huerto.',
            ],
            [
                'nombre' => 'Palapa',
                'codigo' => 'Z-PAL',
                'descripcion' => 'Zona social con cocina al aire libre y espacios de convivencia.',
                'ubicacion' => 'Lado este, frente a la alberca.',
            ],
            [
                'nombre' => 'Jacuzie',
                'codigo' => 'Z-JAC',
                'descripcion' => 'Áreas del jacuzzi, spa y alberca techada.',
                'ubicacion' => 'Sector sur, contiguo a la palapa.',
            ],
            [
                'nombre' => 'Temazcal',
                'codigo' => 'Z-TEM',
                'descripcion' => 'Temazcal tradicional y áreas de descanso adyacentes.',
                'ubicacion' => 'Lado norte, detrás de la palapa.',
            ],
            [
                'nombre' => 'Fogatero',
                'codigo' => 'Z-FOG',
                'descripcion' => 'Fogatero exterior y sendero de acceso a jardines.',
                'ubicacion' => 'Camino lateral sur, cerca de áreas verdes.',
            ],
            [
                'nombre' => 'Exterior',
                'codigo' => 'Z-EXT',
                'descripcion' => 'Jardines, áreas de huertos, caminería y accesos exteriores.',
                'ubicacion' => 'Perímetro completo del predio.',
            ],
        ];

        foreach ($zonas as $zona) {
            Zona::updateOrCreate(
                [
                    'predio_id' => $predio->id,
                    'codigo' => $zona['codigo'],
                ],
                array_merge($zona, [
                    'predio_id' => $predio->id,
                    'activo' => true,
                ])
            );
        }
    }
}
