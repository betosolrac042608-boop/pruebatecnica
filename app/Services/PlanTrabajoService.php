<?php

namespace App\Services;

use App\Models\EvaluacionPlanTrabajo;
use App\Models\PlanFotoZona;
use App\Models\PlanTrabajoDiario;
use App\Models\PlanTrabajoLog;
use App\Models\PlanTrabajoZonaTarea;
use App\Models\Zona;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PlanTrabajoService
{
    public function __construct(protected ChatGptService $chatGpt)
    {
    }

    public function generarPlan(PlanTrabajoDiario $plan): array
    {
        Log::info('[PlanTrabajoService] Iniciando generación de plan', [
            'plan_id' => $plan->id,
            'predio_id' => $plan->predio_id,
            'usuario_id' => $plan->usuario_id,
            'fecha' => $plan->fecha->format('Y-m-d'),
        ]);

        Log::info('[PlanTrabajoService] Cargando relaciones del plan');
        $plan->load(['predio.zonas.tareas', 'usuario']);

        Log::info('[PlanTrabajoService] Relaciones cargadas', [
            'predio_nombre' => $plan->predio->nombre ?? 'N/A',
            'usuario_nombre' => $plan->usuario->name ?? 'N/A',
            'num_zonas' => $plan->predio->zonas->count(),
            'total_tareas' => $plan->predio->zonas->sum(fn($z) => $z->tareas->count()),
        ]);

        Log::info('[PlanTrabajoService] Construyendo payload para GPT');
        $payload = [
            'plan_id' => $plan->id,
            'predio' => [
                'codigo' => $plan->predio->codigo,
                'nombre' => $plan->predio->nombre,
            ],
            'fecha' => $plan->fecha->format('Y-m-d'),
            'tareas_por_zona' => $plan->predio->zonas->map(fn (Zona $zona) => [
                'zona_codigo' => $zona->codigo,
                'zona_nombre' => $zona->nombre,
                'tareas' => $zona->tareas->map(fn ($tarea) => [
                    'clave' => $tarea->clave,
                    'nombre' => $tarea->nombre,
                    'descripcion' => $tarea->descripcion,
                    'objetivo' => $tarea->objetivo,
                    'frecuencia' => $tarea->frecuencia,
                    'tiempo_minutos' => $tarea->tiempo_minutos,
                    'id' => $tarea->id,
                ])->toArray(),
            ])->toArray(),
            'turnos' => [
                'inicio_turno' => $plan->turno_inicio,
                'fin_turno' => $plan->turno_fin,
                'inicio_comida' => $plan->comida_inicio,
                'fin_comida' => $plan->comida_fin,
            ],
            'encargado' => $plan->usuario?->name,
        ];

        Log::info('[PlanTrabajoService] Payload construido', [
            'predio' => $payload['predio']['nombre'],
            'fecha' => $payload['fecha'],
            'encargado' => $payload['encargado'],
            'num_zonas' => count($payload['tareas_por_zona']),
        ]);

        Log::info('[PlanTrabajoService] Creando log en base de datos');
        $log = PlanTrabajoLog::create([
            'plan_trabajo_id' => $plan->id,
            'request_payload' => $payload,
            'status' => 'pendiente',
        ]);

        Log::info('[PlanTrabajoService] Log creado', [
            'log_id' => $log->id,
        ]);

        try {
            Log::info('[PlanTrabajoService] Llamando a ChatGPT para generar plan');
            $respuesta = $this->chatGpt->planTrabajo($payload);
            
            Log::info('[PlanTrabajoService] Respuesta recibida de ChatGPT', [
                'has_estado' => isset($respuesta['estado']),
                'num_zonas' => count($respuesta['zonas'] ?? []),
            ]);

            Log::info('[PlanTrabajoService] Actualizando log con respuesta exitosa');
            $log->update([
                'response_payload' => $respuesta,
                'status' => 'completado',
            ]);

            Log::info('[PlanTrabajoService] Log actualizado exitosamente');
        } catch (\Throwable $exception) {
            Log::error('[PlanTrabajoService] Error al generar plan de trabajo con GPT', [
                'plan_id' => $plan->id,
                'error' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);
            
            // Usar el fallback cuando hay error
            Log::warning('[PlanTrabajoService] Usando fallback debido a excepción');
            $respuesta = $this->chatGpt->fallback($payload);
            
            Log::info('[PlanTrabajoService] Actualizando log con error y fallback');
            $log->update([
                'response_payload' => $respuesta,
                'error' => $exception->getMessage() . "\n\nTrace: " . $exception->getTraceAsString(),
                'status' => 'error',
            ]);
        }

        // Procesar la respuesta (ya sea de GPT o del fallback)
        Log::info('[PlanTrabajoService] Procesando respuesta y creando tareas');
        $this->procesarRespuesta($plan, $respuesta);

        Log::info('[PlanTrabajoService] Plan generado completamente', [
            'plan_id' => $plan->id,
        ]);

        return $respuesta;
    }

    public function procesarRespuesta(PlanTrabajoDiario $plan, array $respuesta): void
    {
        Log::info('[PlanTrabajoService] Iniciando procesamiento de respuesta', [
            'plan_id' => $plan->id,
        ]);

        $plan->load('predio.zonas');

        Log::info('[PlanTrabajoService] Actualizando plan con datos de GPT');
        
        // Extraer el resumen de la IA
        $resumenIa = null;
        if (isset($respuesta['resumen']['mensajes']) && is_array($respuesta['resumen']['mensajes'])) {
            $resumenIa = implode("\n\n", $respuesta['resumen']['mensajes']);
        } elseif (isset($respuesta['resumen']) && is_string($respuesta['resumen'])) {
            $resumenIa = $respuesta['resumen'];
        }
        
        $plan->update([
            'datos_gpt' => $respuesta,
            'resumen_ia' => $resumenIa,
            'estado' => $respuesta['estado'] ?? $plan->estado,
        ]);
        
        Log::info('[PlanTrabajoService] Resumen de IA guardado', [
            'resumen_length' => strlen($resumenIa ?? ''),
        ]);

        Log::info('[PlanTrabajoService] Eliminando tareas anteriores del plan');
        $tareasEliminadas = $plan->tareas()->count();
        $plan->tareas()->delete();
        Log::info('[PlanTrabajoService] Tareas anteriores eliminadas', [
            'tareas_eliminadas' => $tareasEliminadas,
        ]);

        $tareasCreadas = 0;
        $zonasProcesadas = 0;

        Log::info('[PlanTrabajoService] Creando nuevas tareas desde la respuesta');
        collect($respuesta['zonas'] ?? [])->each(function (array $zonaPayload) use ($plan, &$tareasCreadas, &$zonasProcesadas) {
            $zona = $plan->predio->zonas->firstWhere('codigo', $zonaPayload['codigo']);

            if (!$zona) {
                Log::warning('[PlanTrabajoService] Zona no encontrada', [
                    'codigo_zona' => $zonaPayload['codigo'] ?? 'N/A',
                ]);
                return;
            }

            $zonasProcesadas++;
            $tareasEnZona = 0;

            foreach ($zonaPayload['tareas'] ?? [] as $tareaPayload) {
                PlanTrabajoZonaTarea::create([
                    'plan_trabajo_id' => $plan->id,
                    'zona_id' => $zona->id,
                    'tarea_zona_id' => $tareaPayload['tarea_zona_id'] ?? null,
                    'descripcion' => $tareaPayload['descripcion'] ?? $tareaPayload['nombre'],
                    'estado' => $tareaPayload['estado'] ?? 'pendiente',
                    'comentarios' => $tareaPayload['comentarios'] ?? null,
                ]);
                $tareasCreadas++;
                $tareasEnZona++;
            }

            Log::debug('[PlanTrabajoService] Zona procesada', [
                'zona_codigo' => $zona->codigo,
                'zona_nombre' => $zona->nombre,
                'tareas_creadas' => $tareasEnZona,
            ]);
        });

        Log::info('[PlanTrabajoService] Procesamiento de respuesta completado', [
            'zonas_procesadas' => $zonasProcesadas,
            'tareas_creadas' => $tareasCreadas,
        ]);
    }

    public function registrarFoto(PlanTrabajoDiario $plan, Zona $zona, array $data): PlanFotoZona
    {
        return PlanFotoZona::create([
            'plan_trabajo_id' => $plan->id,
            'zona_id' => $zona->id,
            'tipo' => $data['tipo'],
            'ruta' => $data['ruta'],
            'metadata' => $data['metadata'] ?? [],
            'tomada_en' => $data['tomada_en'] ?? now(),
        ]);
    }

    public function registrarEvaluacion(PlanTrabajoDiario $plan, Zona $zona, array $payload): EvaluacionPlanTrabajo
    {
        return EvaluacionPlanTrabajo::create([
            'plan_trabajo_id' => $plan->id,
            'zona_id' => $zona->id,
            'tarea_zona_id' => $payload['tarea_zona_id'] ?? null,
            'resultados' => $payload['resultados'] ?? null,
            'calificacion' => $payload['calificacion'] ?? null,
            'comentarios' => $payload['comentarios'] ?? null,
        ]);
    }

    public function reemplazarTareas(PlanTrabajoDiario $plan): void
    {
        $plan->tareas()->delete();
    }
}

