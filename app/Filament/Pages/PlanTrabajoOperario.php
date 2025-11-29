<?php

namespace App\Filament\Pages;

use App\Jobs\EvaluarFotosTareaJob;
use App\Models\FotoTarea;
use App\Models\PlanTrabajoDiario;
use App\Models\PlanTrabajoZonaTarea;
use App\Services\ChatBotService;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class PlanTrabajoOperario extends Page
{
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static string $view = 'filament.pages.plan-trabajo-operario';
    protected static ?string $title = 'Mi Plan de Trabajo';
    protected static ?string $navigationLabel = 'Inicio';
    
    // Ocultar del menú de navegación (será la página principal)
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public $planTrabajoId = null;
    public $mensaje = '';
    public $conversaciones = [];
    public $planDelDia = null;
    public $tareasDelDia = [];
    public $enviando = false;

    protected $listeners = ['mensajeEnviado' => 'cargarConversaciones'];

    public function mount(): void
    {
        // Redirigir si no es Operador o Supervisor
        $user = Auth::user();
        if (!$user || !$user->rol_id) {
            $this->redirect('/admin');
            return;
        }

        if (!$user->relationLoaded('rol')) {
            $user->load('rol');
        }

        if (!$user->rol || !in_array($user->rol->nombre, ['Operador', 'Supervisor'])) {
            $this->redirect('/admin');
            return;
        }

        $this->cargarPlanDelDia();
    }

    protected function cargarPlanDelDia(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        // Cargar el rol del usuario si no está cargado
        if (!$user->relationLoaded('rol')) {
            $user->load('rol');
        }

        // Primero buscar el plan de trabajo del día actual para este usuario
        $plan = PlanTrabajoDiario::where('usuario_id', $user->id)
            ->whereDate('fecha', today())
            ->with(['predio', 'usuario.rol', 'tareas.zona', 'tareas.tareaOriginal', 'tareas.fotos', 'conversaciones'])
            ->first();

        // Si no hay plan de hoy, buscar el más reciente
        if (!$plan) {
            $plan = PlanTrabajoDiario::where('usuario_id', $user->id)
                ->with(['predio', 'usuario.rol', 'tareas.zona', 'tareas.tareaOriginal', 'tareas.fotos', 'conversaciones'])
                ->orderBy('fecha', 'desc')
                ->first();
        }

        // Validar que el plan asignado pertenezca a un usuario con rol Operador o Supervisor
        if ($plan && $plan->usuario && $plan->usuario->rol) {
            if (!in_array($plan->usuario->rol->nombre, ['Operador', 'Supervisor'])) {
                $plan = null;
            }
        }

        if ($plan) {
            $this->planDelDia = $plan;
            $this->planTrabajoId = $plan->id;
            $this->tareasDelDia = $plan->tareas->map(function ($tarea) {
                $fotoAntes = $tarea->fotos->where('tipo', 'antes')->first();
                $fotoDespues = $tarea->fotos->where('tipo', 'despues')->first();
                
                // Obtener evaluación completa si ambas fotos tienen evaluación
                $evaluacionCompleta = null;
                if ($fotoAntes && $fotoDespues && ($fotoAntes->metadata_gpt || $fotoDespues->metadata_gpt)) {
                    // La evaluación completa está en el metadata de cualquiera de las fotos (ambas tienen el mismo)
                    $metadata = $fotoAntes->metadata_gpt ?? $fotoDespues->metadata_gpt ?? [];
                    
                    // Si metadata es un array y tiene los datos, usarlo; si no, construir desde las evaluaciones individuales
                    if (is_array($metadata) && !empty($metadata)) {
                        $evaluacionCompleta = [
                            'calificacion_general' => $metadata['calificacion_general'] ?? null,
                            'evaluacion_antes' => $fotoAntes->evaluacion_gpt,
                            'evaluacion_despues' => $fotoDespues->evaluacion_gpt,
                            'comentarios' => $metadata['comentarios'] ?? null,
                            'mejoras_detectadas' => is_array($metadata['mejoras_detectadas'] ?? null) ? $metadata['mejoras_detectadas'] : [],
                            'problemas_detectados' => is_array($metadata['problemas_detectados'] ?? null) ? $metadata['problemas_detectados'] : [],
                            'recomendaciones' => is_array($metadata['recomendaciones'] ?? null) ? $metadata['recomendaciones'] : [],
                        ];
                    } elseif ($fotoAntes->evaluacion_gpt || $fotoDespues->evaluacion_gpt) {
                        // Si no hay metadata pero hay evaluaciones individuales, construir evaluación básica
                        $evaluacionCompleta = [
                            'calificacion_general' => null,
                            'evaluacion_antes' => $fotoAntes->evaluacion_gpt,
                            'evaluacion_despues' => $fotoDespues->evaluacion_gpt,
                            'comentarios' => null,
                            'mejoras_detectadas' => [],
                            'problemas_detectados' => [],
                            'recomendaciones' => [],
                        ];
                    }
                }
                
                return [
                    'id' => $tarea->id,
                    'zona' => $tarea->zona->nombre ?? 'N/A',
                    'descripcion' => $tarea->descripcion,
                    'estado' => $tarea->estado,
                    'comentarios' => $tarea->comentarios,
                    'foto_antes' => $fotoAntes ? [
                        'id' => $fotoAntes->id,
                        'ruta' => $fotoAntes->ruta,
                        'url' => Storage::url($fotoAntes->ruta),
                        'calificacion' => $fotoAntes->calificacion,
                        'evaluacion' => $fotoAntes->evaluacion_gpt,
                    ] : null,
                    'foto_despues' => $fotoDespues ? [
                        'id' => $fotoDespues->id,
                        'ruta' => $fotoDespues->ruta,
                        'url' => Storage::url($fotoDespues->ruta),
                        'calificacion' => $fotoDespues->calificacion,
                        'evaluacion' => $fotoDespues->evaluacion_gpt,
                    ] : null,
                    'evaluacion_completa' => $evaluacionCompleta,
                ];
            })->toArray();
            
            // Si hay resumen de la IA pero no hay conversaciones, crear el mensaje inicial
            if ($plan->resumen_ia && $plan->conversaciones()->count() === 0) {
                try {
                    \App\Models\PlanTrabajoConversacion::create([
                        'plan_trabajo_id' => $plan->id,
                        'usuario_id' => $user->id,
                        'rol' => 'assistant',
                        'mensaje' => $plan->resumen_ia,
                    ]);
                } catch (\Exception $e) {
                    // Si falla, continuar sin crear el mensaje
                }
            }
            
            $this->cargarConversaciones();
        } else {
            // Inicializar arrays vacíos si no hay plan
            $this->planDelDia = null;
            $this->planTrabajoId = null;
            $this->tareasDelDia = [];
            $this->conversaciones = [];
        }
    }

    public function cargarConversaciones(): void
    {
        if (!$this->planTrabajoId) {
            $this->conversaciones = [];
            return;
        }

        $plan = PlanTrabajoDiario::find($this->planTrabajoId);
        if (!$plan) {
            $this->conversaciones = [];
            return;
        }

        $this->conversaciones = $plan->conversaciones()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($conv) {
                return [
                    'id' => $conv->id,
                    'rol' => $conv->rol,
                    'mensaje' => $conv->mensaje,
                    'fecha' => $conv->created_at->format('d/m/Y H:i'),
                    'usuario' => $conv->usuario->name ?? 'Usuario',
                ];
            })
            ->toArray();
    }

    public function enviarMensaje(): void
    {
        if (!$this->planTrabajoId) {
            \Filament\Notifications\Notification::make()
                ->title('Sin plan de trabajo')
                ->body('No tienes un plan de trabajo asignado. Contacta a tu supervisor para que te asigne uno.')
                ->warning()
                ->send();
            return;
        }

        $this->validate([
            'planTrabajoId' => 'required|exists:plan_trabajo_diarios,id',
            'mensaje' => 'required|string|min:1|max:1000',
        ], [
            'mensaje.required' => 'Por favor escribe un mensaje',
            'mensaje.min' => 'El mensaje debe tener al menos 1 carácter',
        ]);

        if (empty(trim($this->mensaje))) {
            return;
        }

        $plan = PlanTrabajoDiario::find($this->planTrabajoId);
        if (!$plan) {
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body('Plan de trabajo no encontrado')
                ->danger()
                ->send();
            return;
        }

        // Optimistic update: agregar mensaje del usuario inmediatamente
        $mensajeUsuario = trim($this->mensaje);
        $user = Auth::user();
        
        $this->conversaciones[] = [
            'id' => 'temp-' . time(),
            'rol' => 'user',
            'mensaje' => $mensajeUsuario,
            'fecha' => now()->format('d/m/Y H:i'),
            'usuario' => $user->name ?? 'Usuario',
        ];

        // Limpiar el campo de mensaje inmediatamente
        $this->mensaje = '';
        $this->enviando = true;
        
        // Disparar evento para scroll
        $this->dispatch('mensajeEnviado');

        try {
            $chatBotService = app(ChatBotService::class);
            $respuesta = $chatBotService->enviarMensaje($plan, $user, $mensajeUsuario);

            // Recargar conversaciones para obtener la respuesta del asistente
            $this->cargarConversaciones();
            $this->enviando = false;
            
            // Disparar evento para actualizar la vista
            $this->dispatch('mensajeEnviado');
        } catch (\Exception $e) {
            $this->enviando = false;
            
            // Remover el mensaje temporal si hay error
            $this->conversaciones = array_filter($this->conversaciones, function($conv) {
                return !str_starts_with($conv['id'] ?? '', 'temp-');
            });
            $this->conversaciones = array_values($this->conversaciones);
            
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body('Hubo un error al enviar el mensaje: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user || !$user->rol_id) {
            return false;
        }

        if (!$user->relationLoaded('rol')) {
            $user->load('rol');
        }

        if (!$user->rol) {
            return false;
        }

        // Solo Operador y Supervisor pueden acceder
        return in_array($user->rol->nombre, ['Operador', 'Supervisor']);
    }

    // Propiedad para almacenar archivo temporal
    public $fotoTemporal = null;

    // Método que se llama después de que wire:upload completa la subida
    public function procesarFotoSubida($tareaId, $tipo)
    {
        \Log::info('[PlanTrabajoOperario] procesarFotoSubida() llamado', [
            'tareaId' => $tareaId,
            'tipo' => $tipo,
            'hasFoto' => $this->fotoTemporal !== null,
        ]);

        if (!$this->fotoTemporal) {
            \Log::error('[PlanTrabajoOperario] No hay foto para procesar');
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body('No se ha seleccionado una foto')
                ->danger()
                ->send();
            return;
        }

        try {
            // Validar el archivo
            $this->validate([
                'fotoTemporal' => 'required|image|max:10240', // Máximo 10MB
            ], [
                'fotoTemporal.required' => 'Por favor selecciona una foto',
                'fotoTemporal.image' => 'El archivo debe ser una imagen',
                'fotoTemporal.max' => 'La imagen no debe pesar más de 10MB',
            ]);

            // Procesar la foto
            $this->procesarFoto($tareaId, $tipo, $this->fotoTemporal);
            
            // Limpiar
            $this->fotoTemporal = null;
            
        } catch (\Exception $e) {
            \Log::error('[PlanTrabajoOperario] Error al procesar foto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body('Error al subir la foto: ' . $e->getMessage())
                ->danger()
                ->send();
            $this->fotoTemporal = null;
        }
    }

    protected function procesarFoto($tareaId, $tipo, $foto)
    {
        $tarea = PlanTrabajoZonaTarea::find($tareaId);
        if (!$tarea) {
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body('Tarea no encontrada')
                ->danger()
                ->send();
            return;
        }

        // Verificar que la tarea pertenezca al plan del usuario
        if ($tarea->plan_trabajo_id != $this->planTrabajoId) {
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body('No tienes permiso para modificar esta tarea')
                ->danger()
                ->send();
            return;
        }

        try {
            \Log::info('[PlanTrabajoOperario] Iniciando procesamiento de foto', [
                'tarea_id' => $tareaId,
                'tipo' => $tipo,
                'archivo_nombre' => $foto->getClientOriginalName(),
                'archivo_tamaño' => $foto->getSize(),
            ]);

            // Guardar la imagen
            $ruta = $foto->store('fotos-tareas/' . $tarea->id, 'public');
            
            \Log::info('[PlanTrabajoOperario] Foto guardada en almacenamiento', [
                'ruta' => $ruta,
                'ruta_completa' => Storage::disk('public')->path($ruta),
                'existe' => Storage::disk('public')->exists($ruta),
            ]);
            
            // Eliminar foto anterior del mismo tipo si existe
            $fotoAnterior = FotoTarea::where('plan_trabajo_zona_tarea_id', $tareaId)
                ->where('tipo', $tipo)
                ->first();
            
            if ($fotoAnterior) {
                \Log::info('[PlanTrabajoOperario] Eliminando foto anterior', [
                    'foto_anterior_id' => $fotoAnterior->id,
                    'ruta_anterior' => $fotoAnterior->ruta,
                ]);
                Storage::disk('public')->delete($fotoAnterior->ruta);
                $fotoAnterior->delete();
            }

            // Crear el registro de la foto
            $fotoTarea = FotoTarea::create([
                'plan_trabajo_zona_tarea_id' => $tareaId,
                'tipo' => $tipo,
                'ruta' => $ruta,
                'tomada_en' => now(),
            ]);

            \Log::info('[PlanTrabajoOperario] Registro de foto creado en BD', [
                'foto_tarea_id' => $fotoTarea->id,
                'ruta' => $fotoTarea->ruta,
            ]);

            // Recargar la tarea con sus fotos
            $tarea->refresh();
            $tarea->load('fotos');

            // Si ya hay ambas fotos (antes y después), evaluar con GPT
            $fotoAntes = $tarea->fotos()->where('tipo', 'antes')->first();
            $fotoDespues = $tarea->fotos()->where('tipo', 'despues')->first();

            \Log::info('[PlanTrabajoOperario] Verificando si hay ambas fotos', [
                'tiene_foto_antes' => $fotoAntes !== null,
                'tiene_foto_despues' => $fotoDespues !== null,
            ]);

            if ($fotoAntes && $fotoDespues) {
                // Cambiar estado a "en_progreso" si estaba pendiente
                if ($tarea->estado === 'pendiente') {
                    $tarea->update(['estado' => 'en_progreso']);
                }

                \Log::info('[PlanTrabajoOperario] ✅ Ambas fotos disponibles. Despachando job de evaluación GPT', [
                    'tarea_id' => $tarea->id,
                    'foto_antes_id' => $fotoAntes->id,
                    'foto_despues_id' => $fotoDespues->id,
                ]);

                // Despachar job para evaluar las fotos
                EvaluarFotosTareaJob::dispatch($tarea->fresh());
            }

            // Recargar las tareas
            $this->cargarPlanDelDia();

            \Filament\Notifications\Notification::make()
                ->title('Foto subida')
                ->body('La foto se ha subido correctamente' . ($fotoAntes && $fotoDespues ? '. Se está evaluando con GPT...' : ''))
                ->success()
                ->send();

        } catch (\Exception $e) {
            \Log::error('[PlanTrabajoOperario] ❌ Error al procesar foto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body('Error al subir la foto: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function eliminarFoto($fotoId)
    {
        $foto = FotoTarea::find($fotoId);
        if (!$foto) {
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body('Foto no encontrada')
                ->danger()
                ->send();
            return;
        }

        $tarea = $foto->tarea;
        
        // Verificar que la tarea pertenezca al plan del usuario
        if ($tarea->plan_trabajo_id != $this->planTrabajoId) {
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body('No tienes permiso para eliminar esta foto')
                ->danger()
                ->send();
            return;
        }

        try {
            // Eliminar el archivo
            Storage::disk('public')->delete($foto->ruta);
            
            // Eliminar el registro
            $foto->delete();

            // Recargar las tareas
            $this->cargarPlanDelDia();

            \Filament\Notifications\Notification::make()
                ->title('Foto eliminada')
                ->body('La foto se ha eliminado correctamente')
                ->success()
                ->send();

        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body('Error al eliminar la foto: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}

