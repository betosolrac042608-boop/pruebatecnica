<?php

namespace App\Jobs;

use App\Models\FotoTarea;
use App\Models\PlanTrabajoZonaTarea;
use App\Services\ChatGptService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EvaluarFotosTareaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public PlanTrabajoZonaTarea $tarea
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ChatGptService $chatGptService): void
    {
        Log::info('[EvaluarFotosTareaJob] ========== INICIANDO EVALUACIÓN DE FOTOS ==========', [
            'tarea_id' => $this->tarea->id,
            'plan_trabajo_id' => $this->tarea->plan_trabajo_id,
            'descripcion' => $this->tarea->descripcion,
        ]);

        // Cargar las fotos de la tarea
        $this->tarea->load(['fotos', 'zona', 'tareaOriginal']);
        
        Log::info('[EvaluarFotosTareaJob] Fotos cargadas', [
            'total_fotos' => $this->tarea->fotos->count(),
            'fotos' => $this->tarea->fotos->map(fn($f) => ['id' => $f->id, 'tipo' => $f->tipo])->toArray(),
        ]);
        
        $fotoAntes = $this->tarea->fotos->where('tipo', 'antes')->first();
        $fotoDespues = $this->tarea->fotos->where('tipo', 'despues')->first();

        // Verificar que ambas fotos existan
        if (!$fotoAntes || !$fotoDespues) {
            Log::warning('[EvaluarFotosTareaJob] ❌ FALTAN FOTOS PARA EVALUAR', [
                'tarea_id' => $this->tarea->id,
                'tiene_antes' => $fotoAntes !== null,
                'tiene_despues' => $fotoDespues !== null,
                'foto_antes_id' => $fotoAntes?->id,
                'foto_despues_id' => $fotoDespues?->id,
            ]);
            return;
        }

        Log::info('[EvaluarFotosTareaJob] ✅ Ambas fotos encontradas', [
            'foto_antes_id' => $fotoAntes->id,
            'foto_antes_ruta' => $fotoAntes->ruta,
            'foto_despues_id' => $fotoDespues->id,
            'foto_despues_ruta' => $fotoDespues->ruta,
        ]);

        // Verificar que las fotos existan físicamente
        $disk = Storage::disk('public');
        $existeAntes = $disk->exists($fotoAntes->ruta);
        $existeDespues = $disk->exists($fotoDespues->ruta);
        
        Log::info('[EvaluarFotosTareaJob] Verificando existencia de fotos', [
            'ruta_antes' => $fotoAntes->ruta,
            'existe_antes' => $existeAntes,
            'path_completo_antes' => $existeAntes ? $disk->path($fotoAntes->ruta) : 'N/A',
            'ruta_despues' => $fotoDespues->ruta,
            'existe_despues' => $existeDespues,
            'path_completo_despues' => $existeDespues ? $disk->path($fotoDespues->ruta) : 'N/A',
        ]);
        
        if (!$existeAntes || !$existeDespues) {
            Log::error('[EvaluarFotosTareaJob] ❌ LAS FOTOS NO EXISTEN EN EL ALMACENAMIENTO', [
                'tarea_id' => $this->tarea->id,
                'ruta_antes' => $fotoAntes->ruta,
                'existe_antes' => $existeAntes,
                'ruta_despues' => $fotoDespues->ruta,
                'existe_despues' => $existeDespues,
            ]);
            return;
        }

        Log::info('[EvaluarFotosTareaJob] ✅ Fotos verificadas en almacenamiento');

        try {
            Log::info('[EvaluarFotosTareaJob] 📤 Enviando fotos a GPT para evaluación...');
            
            // Evaluar las fotos con GPT
            $evaluacion = $chatGptService->evaluarFotosTarea(
                $this->tarea,
                $fotoAntes,
                $fotoDespues
            );

            Log::info('[EvaluarFotosTareaJob] ✅ Respuesta recibida de GPT', [
                'calificacion_general' => $evaluacion['calificacion_general'] ?? 'N/A',
                'tiene_evaluacion_antes' => isset($evaluacion['evaluacion_antes']),
                'tiene_evaluacion_despues' => isset($evaluacion['evaluacion_despues']),
                'tiene_comentarios' => isset($evaluacion['comentarios']),
            ]);

            // Actualizar las fotos con la evaluación
            $fotoAntes->update([
                'evaluacion_gpt' => $evaluacion['evaluacion_antes'] ?? null,
                'metadata_gpt' => $evaluacion['metadata'] ?? [],
                'calificacion' => $evaluacion['calificacion_antes'] ?? null,
            ]);

            $fotoDespues->update([
                'evaluacion_gpt' => $evaluacion['evaluacion_despues'] ?? null,
                'metadata_gpt' => $evaluacion['metadata'] ?? [],
                'calificacion' => $evaluacion['calificacion_despues'] ?? null,
            ]);

            // Actualizar el estado de la tarea según la evaluación
            $calificacionGeneral = $evaluacion['calificacion_general'] ?? 'revisar';
            
            if ($calificacionGeneral === 'aprobado') {
                $this->tarea->update(['estado' => 'completado']);
            } elseif ($calificacionGeneral === 'rechazado') {
                $this->tarea->update(['estado' => 'en_progreso']);
            } else {
                $this->tarea->update(['estado' => 'en_progreso']);
            }

            // Agregar comentarios de la evaluación
            if (isset($evaluacion['comentarios'])) {
                $comentariosActuales = $this->tarea->comentarios ?? '';
                $nuevosComentarios = $comentariosActuales 
                    ? $comentariosActuales . "\n\n[Evaluación GPT]: " . $evaluacion['comentarios']
                    : "[Evaluación GPT]: " . $evaluacion['comentarios'];
                $this->tarea->update(['comentarios' => $nuevosComentarios]);
            }

            Log::info('[EvaluarFotosTareaJob] ✅ EVALUACIÓN COMPLETADA Y GUARDADA', [
                'tarea_id' => $this->tarea->id,
                'calificacion_general' => $calificacionGeneral,
                'nuevo_estado' => $this->tarea->estado,
                'foto_antes_actualizada' => true,
                'foto_despues_actualizada' => true,
                'comentarios_agregados' => isset($evaluacion['comentarios']),
            ]);

            Log::info('[EvaluarFotosTareaJob] ========== FIN DE EVALUACIÓN ==========');

        } catch (\Exception $e) {
            Log::error('[EvaluarFotosTareaJob] ❌ ERROR AL EVALUAR FOTOS', [
                'tarea_id' => $this->tarea->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
