<?php

namespace Database\Seeders;

use App\Models\Predio;
use App\Models\User;
use Illuminate\Database\Seeder;

class PrediosSeeder extends Seeder
{
    public function run(): void
    {
        $responsable = User::where('email', 'supervisor@sistema.com')->first();

        Predio::updateOrCreate(
            ['codigo' => 'P-QUINTA'],
            [
                'nombre' => 'Quinta San José',
                'direccion' => 'Carretera Federal 45 km 12, Valle Verde',
                'area_total' => 125.50,
                'descripcion' => 'Propiedad dedicada a hospedaje rural, gastronomía y horticultura de impacto',
                'responsable_id' => $responsable?->id,
                'activo' => true,
            ]
        );
    }
}
