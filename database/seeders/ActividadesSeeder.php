<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ActividadesSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener IDs de usuarios de forma segura
        $adminUser = DB::table('users')->where('email', 'admin@sistema.com')->first();
        $supervisorUser = DB::table('users')->where('email', 'supervisor@sistema.com')->first();
        $operarioUser = DB::table('users')->where('email', 'operario@sistema.com')->first();

        // Actividades para Animal
        if ($adminUser) {
            DB::table('actividades')->insert([
                'entidad_type' => 'App\Models\Animal',
                'entidad_id' => DB::table('animales')->where('matricula', 'VAC-001')->first()->id,
                'tipo_accion_id' => DB::table('tipos_accion')->where('nombre', 'Vacunación')->first()->id,
                'nombre_accion' => 'Vacuna contra la rabia',
                'descripcion' => 'Vacuna contra la rabia aplicada a la vaca Pinta',
                'fecha_realizada' => '2025-10-15',
                'hora_realizada' => '09:00',
                'usuario_id' => $operarioUser->id,
                'estado_id' => DB::table('estados_actividad')->where('nombre', 'Completada')->first()->id,
                'from_scheduled' => false
            ]);
        }

        // Actividades para Herramienta
        if ($supervisorUser) {
            DB::table('actividades')->insert([
                'entidad_type' => 'App\Models\Herramienta',
                'entidad_id' => DB::table('herramientas')->where('matricula', 'HER-001')->first()->id,
                'tipo_accion_id' => DB::table('tipos_accion')->where('nombre', 'Mantenimiento')->first()->id,
                'nombre_accion' => 'Mantenimiento programado',
                'descripcion' => 'Mantenimiento programado del tractor John Deere',
                'fecha_programada' => '2025-11-15',
                'hora_programada' => '08:00',
                'usuario_id' => $supervisorUser->id,
                'estado_id' => DB::table('estados_actividad')->where('nombre', 'Pendiente')->first()->id
            ]);
        }

        // Actividades para Cultivo
        if ($adminUser) {
            DB::table('actividades')->insert([
                'entidad_type' => 'App\Models\Cultivo',
                'entidad_id' => DB::table('cultivos')->where('matricula', 'CUL-001')->first()->id,
                'tipo_accion_id' => DB::table('tipos_accion')->where('nombre', 'Cosecha')->first()->id,
                'nombre_accion' => 'Cosecha de maíz',
                'descripcion' => 'Cosecha de maíz planificada',
                'fecha_programada' => '2025-12-01',
                'usuario_id' => $adminUser->id,
                'estado_id' => DB::table('estados_actividad')->where('nombre', 'Pendiente')->first()->id
            ]);
        }

        // Acciones programadas para Animal
        if ($operarioUser) {
            DB::table('acciones_programadas')->insert([
                'id' => 'sch-' . Str::uuid(),
                'entidad_type' => 'App\Models\Animal',
                'entidad_id' => DB::table('animales')->where('matricula', 'VAC-001')->first()->id,
                'tipo_accion_id' => DB::table('tipos_accion')->where('nombre', 'Vacunación')->first()->id,
                'nombre_accion' => 'Próxima vacuna rabia',
                'fecha_programada' => '2026-04-01',
                'hora_programada' => '09:00',
                'notas' => 'Recordar manejo de documentación',
                'usuario_id' => $operarioUser->id,
                'completed' => false
            ]);
        }
    }
}
