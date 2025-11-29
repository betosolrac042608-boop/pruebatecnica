<?php

namespace App\Services;

use App\Models\PlanTrabajoConversacion;
use App\Models\PlanTrabajoDiario;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ChatBotService
{
    public function __construct(protected ChatGptService $chatGpt)
    {
    }

    public function enviarMensaje(PlanTrabajoDiario $plan, User $usuario, string $mensaje): string
    {
        Log::info('[ChatBot] Enviando mensaje del usuario', [
            'plan_id' => $plan->id,
            'usuario_id' => $usuario->id,
            'mensaje_length' => strlen($mensaje),
        ]);

        // Guardar mensaje del usuario
        PlanTrabajoConversacion::create([
            'plan_trabajo_id' => $plan->id,
            'usuario_id' => $usuario->id,
            'rol' => 'user',
            'mensaje' => $mensaje,
        ]);

        // Construir contexto del plan (SIEMPRE incluir todas las tareas)
        $contextoPlan = $this->construirContextoPlan($plan);

        // Obtener historial de conversación
        $historial = $this->obtenerHistorial($plan);

        // Construir mensajes para GPT (siempre incluir el contexto completo)
        $messages = $this->construirMensajes($contextoPlan, $historial, $mensaje, $usuario);

        Log::info('[ChatBot] Mensajes construidos para GPT', [
            'num_messages' => count($messages),
        ]);

        // Enviar a GPT
        try {
            $respuesta = $this->chatGpt->conversacion($messages);
            
            Log::info('[ChatBot] Respuesta recibida de GPT', [
                'respuesta_length' => strlen($respuesta),
            ]);

            // Guardar respuesta de la IA
            PlanTrabajoConversacion::create([
                'plan_trabajo_id' => $plan->id,
                'usuario_id' => $usuario->id,
                'rol' => 'assistant',
                'mensaje' => $respuesta,
            ]);

            return $respuesta;
        } catch (\Throwable $exception) {
            Log::error('[ChatBot] Error al obtener respuesta de GPT', [
                'error' => $exception->getMessage(),
            ]);

            $mensajeError = 'Lo siento, hubo un error al procesar tu mensaje. Por favor, intenta de nuevo.';
            
            PlanTrabajoConversacion::create([
                'plan_trabajo_id' => $plan->id,
                'usuario_id' => $usuario->id,
                'rol' => 'assistant',
                'mensaje' => $mensajeError,
                'metadata' => ['error' => $exception->getMessage()],
            ]);

            return $mensajeError;
        }
    }

    protected function obtenerHistorial(PlanTrabajoDiario $plan, int $limite = 30): array
    {
        // Obtener todas las conversaciones ordenadas por fecha
        // Excluir el mensaje inicial del resumen_ia si existe (ya que lo incluiremos en el contexto)
        $conversaciones = $plan->conversaciones()
            ->orderBy('created_at', 'asc')
            ->limit($limite)
            ->get();

        // Filtrar conversaciones para excluir el mensaje inicial del resumen si coincide
        $historial = $conversaciones->filter(function ($conversacion) use ($plan) {
            // Si es el primer mensaje del asistente y coincide con el resumen_ia, excluirlo
            // porque lo incluiremos en el contexto del plan
            if ($conversacion->rol === 'assistant' && $plan->resumen_ia) {
                // Comparar si el mensaje es similar al resumen (puede haber pequeñas diferencias)
                $similitud = similar_text($conversacion->mensaje, $plan->resumen_ia);
                $longitudPromedio = (strlen($conversacion->mensaje) + strlen($plan->resumen_ia)) / 2;
                // Si la similitud es mayor al 80%, probablemente es el mensaje inicial
                if ($longitudPromedio > 0 && ($similitud / $longitudPromedio) > 0.8) {
                    return false;
                }
            }
            return true;
        })->map(function ($conversacion) {
            return [
                'role' => $conversacion->rol === 'user' ? 'user' : 'assistant',
                'content' => $conversacion->mensaje,
            ];
        })->toArray();

        return $historial;
    }

    protected function construirContextoPlan(PlanTrabajoDiario $plan): string
    {
        $plan->load(['predio', 'usuario', 'tareas.zona', 'tareas.tareaOriginal']);

        $contexto = "PLAN DE TRABAJO DIARIO - CONTEXTO COMPLETO\n";
        $contexto .= "==========================================\n\n";
        $contexto .= "INFORMACIÓN GENERAL:\n";
        $contexto .= "Predio: {$plan->predio->nombre} ({$plan->predio->codigo})\n";
        $contexto .= "Fecha: {$plan->fecha->format('d/m/Y')}\n";
        $contexto .= "Encargado: {$plan->usuario->name}\n";
        $contexto .= "Estado del Plan: {$plan->estado}\n";

        if ($plan->turno_inicio && $plan->turno_fin) {
            $contexto .= "Horario de trabajo: {$plan->turno_inicio} a {$plan->turno_fin}\n";
        }
        if ($plan->comida_inicio && $plan->comida_fin) {
            $contexto .= "Horario de comida: {$plan->comida_inicio} a {$plan->comida_fin}\n";
        }
        $contexto .= "\n";

        if ($plan->resumen_ia) {
            $contexto .= "MENSAJE INICIAL DE GUARDIANO:\n";
            $contexto .= "{$plan->resumen_ia}\n\n";
        }

        $contexto .= "TAREAS ASIGNADAS DEL DÍA (TODAS):\n";
        $contexto .= "==================================\n\n";

        if ($plan->tareas->count() > 0) {
            $tareasPorZona = $plan->tareas->groupBy('zona_id');
            $numeroTarea = 1;

            foreach ($tareasPorZona as $zonaId => $tareas) {
                $zona = $tareas->first()->zona;
                if ($zona) {
                    $contexto .= "ZONA: {$zona->nombre} ({$zona->codigo})\n";
                    $contexto .= str_repeat("-", 50) . "\n";
                    
                    foreach ($tareas as $tarea) {
                        $contexto .= "Tarea #{$numeroTarea}:\n";
                        $contexto .= "  Descripción: {$tarea->descripcion}\n";
                        $contexto .= "  Estado actual: {$tarea->estado}\n";
                        
                        if ($tarea->tareaOriginal) {
                            $tareaOrig = $tarea->tareaOriginal;
                            if ($tareaOrig->objetivo) {
                                $contexto .= "  Objetivo: {$tareaOrig->objetivo}\n";
                            }
                            if ($tareaOrig->tareas_sugeridas) {
                                $contexto .= "  Pasos sugeridos: {$tareaOrig->tareas_sugeridas}\n";
                            }
                            if ($tareaOrig->tiempo_minutos) {
                                $contexto .= "  Tiempo estimado: {$tareaOrig->tiempo_minutos} minutos\n";
                            }
                            if ($tareaOrig->frecuencia) {
                                $contexto .= "  Frecuencia: {$tareaOrig->frecuencia}\n";
                            }
                        }
                        
                        if ($tarea->comentarios) {
                            $contexto .= "  Comentarios adicionales: {$tarea->comentarios}\n";
                        }
                        $contexto .= "\n";
                        $numeroTarea++;
                    }
                }
            }
        } else {
            $contexto .= "No hay tareas asignadas actualmente.\n\n";
        }

        $contexto .= "INSTRUCCIONES PARA GUARDIANO:\n";
        $contexto .= "Puedes ayudar al usuario con:\n";
        $contexto .= "- Explicar cómo realizar cada tarea\n";
        $contexto .= "- Sugerir el orden de ejecución de las tareas\n";
        $contexto .= "- Responder preguntas sobre el proceso de las actividades\n";
        $contexto .= "- Dar seguimiento al progreso de las tareas\n";
        $contexto .= "- Resolver dudas sobre tiempos, materiales, o procedimientos\n";

        return $contexto;
    }

    protected function construirMensajes(string $contextoPlan, array $historial, string $mensajeUsuario, User $usuario): array
    {
        $systemPrompt = <<<PROMPT
Eres GuardIAno, el capataz digital de Grupo MiBe. Estás ayudando a {$usuario->name} a entender y trabajar con su plan de trabajo diario.

Tu personalidad:
- Eres amigable, profesional y motivador
- Respondes en español de manera clara y concisa
- Ayudas a resolver dudas sobre las tareas asignadas
- Proporcionas consejos prácticos cuando es necesario
- Mantienes un tono positivo y de apoyo
- Conoces todas las tareas del día y puedes ayudar con el proceso de cada actividad
- Puedes dar seguimiento al progreso de las tareas

IMPORTANTE: 
- Responde siempre de manera natural y conversacional, como si fueras un compañero de trabajo que está ayudando
- Tienes el contexto completo de todas las tareas del día
- Puedes ayudar con preguntas sobre cómo realizar las tareas, el orden recomendado, tiempos estimados, etc.
- Mantén conversaciones sobre el proceso de las actividades
PROMPT;

        // SIEMPRE incluir el mensaje del sistema y el contexto del plan
        // Esto asegura que el chatbot tenga el contexto completo en cada conversación
        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt,
            ],
            [
                'role' => 'user',
                'content' => "Aquí está el contexto completo de mi plan de trabajo con TODAS las tareas del día:\n\n{$contextoPlan}\n\nAhora puedes ayudarme con cualquier pregunta sobre este plan, las tareas, el proceso de las actividades, etc.",
            ],
        ];

        // Si hay historial de conversaciones previas, agregarlo (excluyendo el mensaje inicial del sistema si existe)
        // El historial contiene solo los mensajes user/assistant de conversaciones anteriores
        if (count($historial) > 0) {
            // Filtrar el historial para excluir mensajes de sistema que puedan estar duplicados
            $historialFiltrado = array_filter($historial, function($msg) {
                return isset($msg['role']) && $msg['role'] !== 'system';
            });
            
            // Agregar el historial filtrado después del contexto inicial
            foreach ($historialFiltrado as $msg) {
                $messages[] = $msg;
            }
        } else {
            // Si no hay historial, agregar el mensaje inicial de bienvenida
            $messages[] = [
                'role' => 'assistant',
                'content' => "¡Hola {$usuario->name}! 👋 Soy GuardIAno, tu capataz digital. He revisado tu plan de trabajo y todas tus tareas del día. Estoy aquí para ayudarte con cualquier pregunta sobre tus actividades, el proceso de trabajo, o cualquier duda que tengas. ¿En qué puedo asistirte?",
            ];
        }

        // Agregar el nuevo mensaje del usuario al final
        $messages[] = [
            'role' => 'user',
            'content' => $mensajeUsuario,
        ];

        return $messages;
    }
}

