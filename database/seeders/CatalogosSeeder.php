<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogosSeeder extends Seeder
{
    public function run(): void
    {
        // Tipos de activo
        $tiposActivo = [
            ['nombre' => 'Animal', 'descripcion' => 'Animales y ganado'],
            ['nombre' => 'Cultivo/Árbol', 'descripcion' => 'Cultivos y árboles/frutales/parcelas'],
            ['nombre' => 'Herramienta', 'descripcion' => 'Herramientas y maquinaria']
        ];
        
        foreach ($tiposActivo as $tipo) {
            DB::table('tipos_activo')->insertOrIgnore($tipo);
        }

        // Estados de activo
        $estadosActivo = [
            // Para Animal
            ['nombre' => 'Activo', 'tipo_activo_id' => DB::table('tipos_activo')->where('nombre', 'Animal')->first()->id],
            ['nombre' => 'Vendido', 'tipo_activo_id' => DB::table('tipos_activo')->where('nombre', 'Animal')->first()->id],
            ['nombre' => 'Fallecido', 'tipo_activo_id' => DB::table('tipos_activo')->where('nombre', 'Animal')->first()->id],
            // Para Cultivo/Árbol
            ['nombre' => 'Saludable', 'tipo_activo_id' => DB::table('tipos_activo')->where('nombre', 'Cultivo/Árbol')->first()->id],
            ['nombre' => 'Cosechado', 'tipo_activo_id' => DB::table('tipos_activo')->where('nombre', 'Cultivo/Árbol')->first()->id],
            ['nombre' => 'Enfermo', 'tipo_activo_id' => DB::table('tipos_activo')->where('nombre', 'Cultivo/Árbol')->first()->id],
            // Para Herramienta
            ['nombre' => 'Operativa', 'tipo_activo_id' => DB::table('tipos_activo')->where('nombre', 'Herramienta')->first()->id],
            ['nombre' => 'En Reparación', 'tipo_activo_id' => DB::table('tipos_activo')->where('nombre', 'Herramienta')->first()->id],
            ['nombre' => 'Extraviada', 'tipo_activo_id' => DB::table('tipos_activo')->where('nombre', 'Herramienta')->first()->id],
            ['nombre' => 'Fuera de Servicio', 'tipo_activo_id' => DB::table('tipos_activo')->where('nombre', 'Herramienta')->first()->id]
        ];

        foreach ($estadosActivo as $estado) {
            DB::table('estados_activo')->insertOrIgnore($estado);
        }

        // Tipos de acción
        $tiposAccion = [
            'Vacunación', 'Medicamento', 'Revisión', 'Alimentación', 'Reproducción',
            'Fertilización', 'Riego', 'Fumigación', 'Poda', 'Cosecha',
            'Siembra', 'Mantenimiento', 'Reparación', 'Limpieza', 'Calibración',
            'Observación'
        ];

        foreach ($tiposAccion as $tipo) {
            DB::table('tipos_accion')->insertOrIgnore(['nombre' => $tipo]);
        }

        // Roles
        $roles = [
            'Administrador', 'Operador', 'Supervisor'
        ];

        foreach ($roles as $rol) {
            DB::table('roles')->insertOrIgnore(['nombre' => $rol]);
        }

        // Estados de actividad
        $estadosActividad = [
            'Pendiente', 'Completada', 'Cancelada'
        ];

        foreach ($estadosActividad as $estado) {
            DB::table('estados_actividad')->insertOrIgnore(['nombre' => $estado]);
        }
    }
}
