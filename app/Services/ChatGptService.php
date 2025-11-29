<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatGptService
{
    public function planTrabajo(array $payload): array
    {
        Log::info('[ChatGPT] Iniciando petición a OpenAI', [
            'plan_id' => $payload['plan_id'] ?? 'N/A',
            'predio' => $payload['predio']['nombre'] ?? 'N/A',
            'fecha' => $payload['fecha'] ?? 'N/A',
        ]);

        $apiKey = config('services.openai.key') ?: env('OPENAI_API_KEY');
        $model = config('services.openai.model', env('OPENAI_MODEL', 'gpt-3.5-turbo'));
        $verify = config('services.openai.verify_ssl', env('OPENAI_VERIFY_SSL', false));
        $cacert = config('services.openai.cacert', env('OPENAI_CACERT_PATH'));

        Log::info('[ChatGPT] Configuración de API', [
            'model' => $model,
            'verify_ssl' => $verify,
            'has_api_key' => !empty($apiKey),
            'api_key_length' => $apiKey ? strlen($apiKey) : 0,
        ]);

        if (empty($apiKey)) {
            Log::warning('[ChatGPT] No se encontró API key, usando fallback');
            return $this->fallback($payload);
        }

        Log::info('[ChatGPT] Construyendo mensajes para el prompt');
        $messages = $this->buildMessages($payload);
        
        Log::info('[ChatGPT] Mensajes construidos', [
            'num_messages' => count($messages),
            'system_prompt_length' => strlen($messages[0]['content'] ?? ''),
            'user_prompt_length' => strlen($messages[1]['content'] ?? ''),
        ]);

        // Log completo de lo que se envía a GPT
        Log::info('[ChatGPT] PAYLOAD COMPLETO ENVIADO A GPT', [
            'messages' => $messages,
            'model' => $model,
        ]);

        $client = Http::withToken($apiKey);
        $options = [];
        if ($cacert) {
            $options['verify'] = $cacert;
        } else {
            $options['verify'] = (bool)$verify;
        }

        $requestPayload = [
            'model' => $model,
            'messages' => $messages,
            'response_format' => ['type' => 'json_object'],
        ];

        Log::info('[ChatGPT] Enviando petición a OpenAI API', [
            'url' => 'https://api.openai.com/v1/chat/completions',
            'model' => $model,
            'timeout' => 60,
            'request_payload' => $requestPayload,
        ]);

        $startTime = microtime(true);
        $response = $client->withOptions($options)
            ->timeout(60)
            ->post('https://api.openai.com/v1/chat/completions', $requestPayload);
        $duration = round((microtime(true) - $startTime) * 1000, 2);

        // Log completo de la respuesta recibida
        $responseBody = $response->json();
        Log::info('[ChatGPT] Respuesta recibida de OpenAI', [
            'duration_ms' => $duration,
            'status_code' => $response->status(),
            'success' => $response->successful(),
            'full_response' => $responseBody,
        ]);

        if ($response->failed()) {
            Log::error('[ChatGPT] Error en la petición a la API', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers(),
            ]);
            Log::warning('[ChatGPT] Usando fallback debido a error en API');
            return $this->fallback($payload);
        }

        $content = $response->json('choices.0.message.content');
        
        Log::info('[ChatGPT] CONTENIDO RECIBIDO DE GPT', [
            'content' => $content,
            'content_length' => strlen($content ?? ''),
            'has_content' => !empty($content),
        ]);
        
        if (empty($content)) {
            Log::error('[ChatGPT] Respuesta vacía de OpenAI', [
                'full_response' => $response->json(),
            ]);
            Log::warning('[ChatGPT] Usando fallback debido a respuesta vacía');
            return $this->fallback($payload);
        }

        // Limpiar el contenido de posibles markdown code blocks
        $originalContent = $content;
        $content = preg_replace('/^```json\s*/', '', $content);
        $content = preg_replace('/^```\s*/', '', $content);
        $content = preg_replace('/\s*```$/', '', $content);
        $content = trim($content);

        if ($originalContent !== $content) {
            Log::info('[ChatGPT] Se limpió el contenido de markdown code blocks', [
                'original' => $originalContent,
                'cleaned' => $content,
                'original_length' => strlen($originalContent),
                'cleaned_length' => strlen($content),
            ]);
        } else {
            Log::info('[ChatGPT] Contenido sin cambios (no tenía markdown)', [
                'content' => $content,
            ]);
        }

        Log::info('[ChatGPT] Decodificando JSON de la respuesta');
        $decoded = json_decode($content, true);

        if (! \is_array($decoded)) {
            Log::error('[ChatGPT] Error al decodificar JSON', [
                'content_preview' => substr($content, 0, 500),
                'json_error' => json_last_error_msg(),
                'json_error_code' => json_last_error(),
            ]);
            Log::warning('[ChatGPT] Usando fallback debido a JSON inválido');
            return $this->fallback($payload);
        }

        Log::info('[ChatGPT] JSON decodificado exitosamente', [
            'has_estado' => isset($decoded['estado']),
            'num_zonas' => count($decoded['zonas'] ?? []),
            'total_tareas' => collect($decoded['zonas'] ?? [])->sum(fn($z) => count($z['tareas'] ?? [])),
        ]);

        return $decoded;
    }

    protected function buildMessages(array $payload): array
    {
        Log::debug('[ChatGPT] Construyendo mensajes del prompt', [
            'encargado' => $payload['encargado'] ?? 'N/A',
            'num_zonas' => count($payload['tareas_por_zona'] ?? []),
        ]);

        $encargado = $payload['encargado'] ?? 'el encargado';
        
        // Construir información detallada de zonas y tareas
        $zonasInfo = collect($payload['tareas_por_zona'])->map(function ($zona) {
            $tareasDetalle = collect($zona['tareas'])->map(function ($tarea) {
                return sprintf(
                    "- %s (%s): %s. Objetivo: %s. Frecuencia: %s. Tiempo estimado: %d minutos.",
                    $tarea['clave'],
                    $tarea['nombre'],
                    $tarea['descripcion'] ?? 'Sin descripción',
                    $tarea['objetivo'] ?? 'Sin objetivo definido',
                    $tarea['frecuencia'] ?? 'No especificada',
                    $tarea['tiempo_minutos'] ?? 0
                );
            })->implode("\n");
            
            return sprintf(
                "ZONA: %s (%s)\nTareas:\n%s\n",
                $zona['zona_nombre'],
                $zona['zona_codigo'],
                $tareasDetalle
            );
        })->implode("\n---\n\n");

        Log::debug('[ChatGPT] Información de zonas construida', [
            'zonas_info_length' => strlen($zonasInfo),
        ]);

        // Prompt del sistema con el rol de GuardIAno
        $systemPrompt = <<<PROMPT
Eres GuardIAno, el capataz digital de Grupo MiBe. Tu propósito es supervisar campos como Quinta Celia y enviar planes claros y ordenados con tareas por zona.

Tu misión es:
- Acompañar a {$encargado} en sus labores diarias
- Proponer tareas organizadas por zona considerando horarios y tiempos
- Sugerir fotos antes/después de cada tarea
- Respetar horarios de trabajo (7:30-18:00 Lun-Vie, 7:30-14:00 sábados)
- Mejorar la presentación para que los clientes no se decepcionen
- Asegurar que todas las tareas se completen en el tiempo estipulado

IMPORTANTE: Debes responder ÚNICAMENTE con un JSON válido, sin texto adicional antes o después.
PROMPT;

        // Construir el mensaje del usuario con toda la información
        $userPrompt = sprintf(
            <<<PROMPT
Predio: %s (Código: %s)
Fecha del plan: %s
Encargado: %s

Horarios:
- Turno: %s a %s
- Comida: %s a %s

TAREAS POR ZONA:
%s

INSTRUCCIONES:
Analiza las tareas por zona y crea un plan de trabajo diario. Para cada zona, asigna las tareas que deben realizarse considerando:
1. La frecuencia de cada tarea
2. El tiempo estimado de cada tarea
3. Los horarios disponibles del encargado
4. La importancia de mantener la presentación del predio

RESPUESTA REQUERIDA (solo JSON, sin texto adicional):
{
  "estado": "en_progreso",
  "zonas": [
    {
      "codigo": "CODIGO_ZONA",
      "tareas": [
        {
          "nombre": "Nombre de la tarea",
          "descripcion": "Descripción detallada de lo que se debe hacer",
          "tarea_zona_id": ID_NUMERICO_DE_LA_TAREA,
          "estado": "pendiente",
          "comentarios": "Comentarios o recomendaciones adicionales"
        }
      ]
    }
  ],
  "resumen": {
    "mensajes": [
      "Mensaje motivacional o de resumen para el encargado"
    ]
  }
}
PROMPT,
            $payload['predio']['nombre'],
            $payload['predio']['codigo'],
            $payload['fecha'],
            $encargado,
            $payload['turnos']['inicio_turno'] ?? '07:30',
            $payload['turnos']['fin_turno'] ?? '18:00',
            $payload['turnos']['inicio_comida'] ?? '14:00',
            $payload['turnos']['fin_comida'] ?? '15:30',
            $zonasInfo
        );

        return [
            [
                'role' => 'system',
                'content' => $systemPrompt,
            ],
            [
                'role' => 'user',
                'content' => $userPrompt,
            ],
        ];
    }

    public function fallback(array $payload): array
    {
        Log::info('[ChatGPT] Ejecutando fallback', [
            'plan_id' => $payload['plan_id'] ?? 'N/A',
            'num_zonas' => count($payload['tareas_por_zona'] ?? []),
        ]);

        $fallbackResponse = [
            'estado' => 'en_progreso',
            'zonas' => collect($payload['tareas_por_zona'])->map(function ($zona) {
                return [
                    'codigo' => $zona['zona_codigo'],
                    'tareas' => array_map(function ($tarea) {
                        return [
                            'nombre' => $tarea['nombre'] ?? 'Tarea sin nombre',
                            'descripcion' => $tarea['descripcion'] ?? $tarea['nombre'] ?? 'Sin descripción',
                            'tarea_zona_id' => $tarea['id'] ?? null,
                            'estado' => 'pendiente',
                            'comentarios' => 'Tarea asignada automáticamente (fallback)',
                        ];
                    }, $zona['tareas'] ?? []),
                ];
            })->toArray(),
            'resumen' => [
                'mensajes' => [
                    'No se obtuvo respuesta de OpenAI; se muestra la copia local de las tareas.',
                    'Por favor, verifica la configuración de la API key o contacta al administrador.',
                ],
            ],
        ];

        Log::info('[ChatGPT] Fallback completado', [
            'estado' => $fallbackResponse['estado'],
            'num_zonas' => count($fallbackResponse['zonas']),
        ]);

        return $fallbackResponse;
    }

    public function conversacion(array $messages): string
    {
        Log::info('[ChatGPT] Iniciando conversación', [
            'num_messages' => count($messages),
        ]);

        $apiKey = config('services.openai.key') ?: env('OPENAI_API_KEY');
        $model = config('services.openai.model', env('OPENAI_MODEL', 'gpt-3.5-turbo'));
        $verify = config('services.openai.verify_ssl', env('OPENAI_VERIFY_SSL', false));
        $cacert = config('services.openai.cacert', env('OPENAI_CACERT_PATH'));

        if (empty($apiKey)) {
            Log::warning('[ChatGPT] No se encontró API key para conversación');
            return 'Lo siento, no puedo responder en este momento. Por favor, contacta al administrador.';
        }

        $client = Http::withToken($apiKey);
        $options = [];
        if ($cacert) {
            $options['verify'] = $cacert;
        } else {
            $options['verify'] = (bool)$verify;
        }

        $requestPayload = [
            'model' => $model,
            'messages' => $messages,
        ];

        Log::info('[ChatGPT] Enviando mensaje de conversación', [
            'model' => $model,
        ]);

        $startTime = microtime(true);
        $response = $client->withOptions($options)
            ->timeout(60)
            ->post('https://api.openai.com/v1/chat/completions', $requestPayload);
        $duration = round((microtime(true) - $startTime) * 1000, 2);

        if ($response->failed()) {
            Log::error('[ChatGPT] Error en conversación', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return 'Lo siento, hubo un error al procesar tu mensaje. Por favor, intenta de nuevo.';
        }

        $content = $response->json('choices.0.message.content');
        
        if (empty($content)) {
            Log::error('[ChatGPT] Respuesta vacía en conversación');
            return 'No recibí una respuesta válida. Por favor, intenta de nuevo.';
        }

        Log::info('[ChatGPT] Respuesta de conversación recibida', [
            'duration_ms' => $duration,
            'content_length' => strlen($content),
        ]);

        return $content;
    }

    /**
     * Evalúa las fotos antes/después de una tarea usando GPT-4 Vision
     * Evalúa cada foto por separado y luego compara los resultados
     */
    public function evaluarFotosTarea($tarea, $fotoAntes, $fotoDespues): array
    {
        Log::info('[ChatGPT] Iniciando evaluación de fotos de tarea', [
            'tarea_id' => $tarea->id,
            'descripcion' => $tarea->descripcion,
        ]);

        $apiKey = config('services.openai.key') ?: env('OPENAI_API_KEY');
        $model = config('services.openai.vision_model', env('OPENAI_VISION_MODEL', 'gpt-4o'));
        $verify = config('services.openai.verify_ssl', env('OPENAI_VERIFY_SSL', false));
        $cacert = config('services.openai.cacert', env('OPENAI_CACERT_PATH'));

        if (empty($apiKey)) {
            Log::warning('[ChatGPT] No se encontró API key para evaluación de imágenes');
            return $this->fallbackEvaluacionFotos($tarea);
        }

        // Leer las imágenes y convertirlas a base64
        Log::info('[ChatGPT] 📸 Preparando conversión de imágenes a base64', [
            'ruta_antes' => $fotoAntes->ruta,
            'ruta_despues' => $fotoDespues->ruta,
        ]);
        
        $imagenAntes = $this->convertirImagenABase64($fotoAntes->ruta);
        $imagenDespues = $this->convertirImagenABase64($fotoDespues->ruta);

        if (!$imagenAntes || !$imagenDespues) {
            Log::error('[ChatGPT] ❌ Error al convertir imágenes a base64', [
                'imagen_antes_convertida' => $imagenAntes !== null,
                'imagen_despues_convertida' => $imagenDespues !== null,
                'ruta_antes' => $fotoAntes->ruta,
                'ruta_despues' => $fotoDespues->ruta,
            ]);
            return $this->fallbackEvaluacionFotos($tarea);
        }
        
        Log::info('[ChatGPT] ✅ Ambas imágenes convertidas a base64', [
            'tamaño_antes' => strlen($imagenAntes),
            'tamaño_despues' => strlen($imagenDespues),
        ]);

        // PASO 1: Evaluar la foto "antes" individualmente
        Log::info('[ChatGPT] 📸 PASO 1: Evaluando foto ANTES individualmente...');
        $evaluacionAntes = $this->evaluarFotoIndividual(
            $tarea,
            $imagenAntes,
            'antes',
            $apiKey,
            $model,
            $verify,
            $cacert
        );

        // PASO 2: Evaluar la foto "después" individualmente
        Log::info('[ChatGPT] 📸 PASO 2: Evaluando foto DESPUÉS individualmente...');
        $evaluacionDespues = $this->evaluarFotoIndividual(
            $tarea,
            $imagenDespues,
            'despues',
            $apiKey,
            $model,
            $verify,
            $cacert
        );

        // PASO 3: Comparar ambas evaluaciones usando solo texto
        Log::info('[ChatGPT] 🔄 PASO 3: Comparando evaluaciones...');
        $comparacion = $this->compararEvaluaciones(
            $tarea,
            $evaluacionAntes,
            $evaluacionDespues,
            $apiKey,
            $model,
            $verify,
            $cacert
        );

        // Combinar los resultados
        return [
            'calificacion_general' => $comparacion['calificacion_general'] ?? 'revisar',
            'calificacion_antes' => $evaluacionAntes['calificacion'] ?? 'revisar',
            'calificacion_despues' => $evaluacionDespues['calificacion'] ?? 'revisar',
            'evaluacion_antes' => $evaluacionAntes['descripcion'] ?? 'No se pudo evaluar',
            'evaluacion_despues' => $evaluacionDespues['descripcion'] ?? 'No se pudo evaluar',
            'comentarios' => $comparacion['comentarios'] ?? null,
            'mejoras_detectadas' => $comparacion['mejoras_detectadas'] ?? [],
            'problemas_detectados' => $comparacion['problemas_detectados'] ?? [],
            'recomendaciones' => $comparacion['recomendaciones'] ?? [],
            'metadata' => [
                'calificacion_general' => $comparacion['calificacion_general'] ?? 'revisar',
                'comentarios' => $comparacion['comentarios'] ?? null,
                'mejoras_detectadas' => $comparacion['mejoras_detectadas'] ?? [],
                'problemas_detectados' => $comparacion['problemas_detectados'] ?? [],
                'recomendaciones' => $comparacion['recomendaciones'] ?? [],
            ],
        ];
    }

    /**
     * Evalúa una foto individual usando GPT-4 Vision
     */
    protected function evaluarFotoIndividual($tarea, $imagenBase64, $tipo, $apiKey, $model, $verify, $cacert): array
    {
        $systemPrompt = <<<PROMPT
Eres GuardIAno, el capataz digital de Grupo MiBe. Tu tarea es analizar una foto de una tarea de trabajo.

Analiza la imagen y describe detalladamente:
1. El estado general que se observa
2. Problemas, suciedad, desorden o áreas que necesitan trabajo (si es foto "antes")
3. Calidad del trabajo realizado y mejoras visibles (si es foto "después")
4. Si la tarea parece estar completa o necesita más trabajo

Responde ÚNICAMENTE con un JSON válido, sin texto adicional.
PROMPT;

        $tipoTexto = $tipo === 'antes' ? 'ANTES de realizar el trabajo' : 'DESPUÉS de realizar el trabajo';
        
        $userPrompt = sprintf(
            <<<PROMPT
TAREA A EVALUAR:
Descripción: %s
Zona: %s

Esta es la foto %s.

Analiza la imagen y describe:
- ¿Qué se observa en la imagen?
- ¿Cuál es el estado general?
- ¿Hay problemas, suciedad, desorden o áreas que necesitan trabajo?
- ¿La calidad es aceptable?
- ¿La tarea parece estar completa?

RESPUESTA REQUERIDA (solo JSON, sin texto adicional):
{
  "calificacion": "aprobado|rechazado|revisar",
  "descripcion": "Descripción detallada de lo que se observa en la imagen",
  "problemas": ["Lista de problemas detectados"],
  "observaciones": "Observaciones adicionales sobre el estado"
}
PROMPT,
            $tarea->descripcion ?? 'Sin descripción',
            $tarea->zona->nombre ?? 'N/A',
            $tipoTexto
        );

        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt,
            ],
            [
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => $userPrompt,
                    ],
                    [
                        'type' => 'image_url',
                        'image_url' => [
                            'url' => $imagenBase64,
                        ],
                    ],
                ],
            ],
        ];

        $client = Http::withToken($apiKey);
        $options = [];
        if ($cacert) {
            $options['verify'] = $cacert;
        } else {
            $options['verify'] = (bool)$verify;
        }

        $requestPayload = [
            'model' => $model,
            'messages' => $messages,
            'response_format' => ['type' => 'json_object'],
            'max_tokens' => 500,
        ];

        try {
            Log::info("[ChatGPT] Enviando evaluación de foto {$tipo} a OpenAI");
            
            $response = $client->withOptions($options)
                ->timeout(60)
                ->post('https://api.openai.com/v1/chat/completions', $requestPayload);

            if ($response->failed()) {
                Log::error("[ChatGPT] Error en evaluación de foto {$tipo}", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return $this->fallbackEvaluacionFotoIndividual($tipo);
            }

            $content = $response->json('choices.0.message.content');
            
            if (empty($content)) {
                Log::error("[ChatGPT] Respuesta vacía en evaluación de foto {$tipo}");
                return $this->fallbackEvaluacionFotoIndividual($tipo);
            }

            // Limpiar el contenido
            $content = preg_replace('/^```json\s*/', '', $content);
            $content = preg_replace('/^```\s*/', '', $content);
            $content = preg_replace('/\s*```$/', '', $content);
            $content = trim($content);

            $decoded = json_decode($content, true);

            if (!is_array($decoded)) {
                Log::error("[ChatGPT] Error al decodificar JSON de foto {$tipo}", [
                    'json_error' => json_last_error_msg(),
                ]);
                return $this->fallbackEvaluacionFotoIndividual($tipo);
            }

            Log::info("[ChatGPT] ✅ Evaluación de foto {$tipo} completada", [
                'calificacion' => $decoded['calificacion'] ?? 'N/A',
            ]);

            return $decoded;

        } catch (\Exception $e) {
            Log::error("[ChatGPT] Excepción en evaluación de foto {$tipo}", [
                'error' => $e->getMessage(),
            ]);
            return $this->fallbackEvaluacionFotoIndividual($tipo);
        }
    }

    /**
     * Compara dos evaluaciones usando solo texto (sin imágenes)
     */
    protected function compararEvaluaciones($tarea, $evaluacionAntes, $evaluacionDespues, $apiKey, $model, $verify, $cacert): array
    {
        $systemPrompt = <<<PROMPT
Eres GuardIAno, el capataz digital de Grupo MiBe. Tu tarea es comparar dos evaluaciones de fotos (antes y después) y determinar si el trabajo se completó correctamente.

Analiza las dos evaluaciones y determina:
1. Si el trabajo se completó según la descripción de la tarea
2. Las mejoras visibles entre antes y después
3. Si hay problemas que deban corregirse
4. Recomendaciones para mejorar

Responde ÚNICAMENTE con un JSON válido, sin texto adicional.
PROMPT;

        $userPrompt = sprintf(
            <<<PROMPT
TAREA A EVALUAR:
Descripción: %s
Zona: %s

EVALUACIÓN FOTO "ANTES":
Calificación: %s
Descripción: %s
Problemas: %s

EVALUACIÓN FOTO "DESPUÉS":
Calificación: %s
Descripción: %s
Problemas: %s

INSTRUCCIONES:
Compara ambas evaluaciones y determina:
- ¿Se completó correctamente la tarea según su descripción?
- ¿Qué mejoras son visibles entre antes y después?
- ¿Hay problemas que deban corregirse?
- ¿La calidad del trabajo es aceptable?

RESPUESTA REQUERIDA (solo JSON, sin texto adicional):
{
  "calificacion_general": "aprobado|rechazado|revisar",
  "comentarios": "Comentarios generales comparando antes vs después",
  "mejoras_detectadas": ["Lista de mejoras visibles"],
  "problemas_detectados": ["Lista de problemas que deben corregirse"],
  "recomendaciones": ["Recomendaciones para mejorar el trabajo"]
}
PROMPT,
            $tarea->descripcion ?? 'Sin descripción',
            $tarea->zona->nombre ?? 'N/A',
            $evaluacionAntes['calificacion'] ?? 'revisar',
            $evaluacionAntes['descripcion'] ?? 'Sin evaluación',
            implode(', ', $evaluacionAntes['problemas'] ?? []),
            $evaluacionDespues['calificacion'] ?? 'revisar',
            $evaluacionDespues['descripcion'] ?? 'Sin evaluación',
            implode(', ', $evaluacionDespues['problemas'] ?? [])
        );

        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt,
            ],
            [
                'role' => 'user',
                'content' => $userPrompt,
            ],
        ];

        $client = Http::withToken($apiKey);
        $options = [];
        if ($cacert) {
            $options['verify'] = $cacert;
        } else {
            $options['verify'] = (bool)$verify;
        }

        $requestPayload = [
            'model' => $model,
            'messages' => $messages,
            'response_format' => ['type' => 'json_object'],
            'max_tokens' => 800,
        ];

        try {
            Log::info('[ChatGPT] Enviando comparación de evaluaciones a OpenAI');
            
            $response = $client->withOptions($options)
                ->timeout(60)
                ->post('https://api.openai.com/v1/chat/completions', $requestPayload);

            if ($response->failed()) {
                Log::error('[ChatGPT] Error en comparación de evaluaciones', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return $this->fallbackComparacion();
            }

            $content = $response->json('choices.0.message.content');
            
            if (empty($content)) {
                Log::error('[ChatGPT] Respuesta vacía en comparación');
                return $this->fallbackComparacion();
            }

            // Limpiar el contenido
            $content = preg_replace('/^```json\s*/', '', $content);
            $content = preg_replace('/^```\s*/', '', $content);
            $content = preg_replace('/\s*```$/', '', $content);
            $content = trim($content);

            $decoded = json_decode($content, true);

            if (!is_array($decoded)) {
                Log::error('[ChatGPT] Error al decodificar JSON de comparación', [
                    'json_error' => json_last_error_msg(),
                ]);
                return $this->fallbackComparacion();
            }

            Log::info('[ChatGPT] ✅ Comparación completada', [
                'calificacion_general' => $decoded['calificacion_general'] ?? 'N/A',
            ]);

            return $decoded;

        } catch (\Exception $e) {
            Log::error('[ChatGPT] Excepción en comparación', [
                'error' => $e->getMessage(),
            ]);
            return $this->fallbackComparacion();
        }
    }

    /**
     * Fallback para evaluación individual de foto
     */
    protected function fallbackEvaluacionFotoIndividual($tipo): array
    {
        return [
            'calificacion' => 'revisar',
            'descripcion' => "No se pudo evaluar automáticamente la foto {$tipo}. Requiere revisión manual.",
            'problemas' => [],
            'observaciones' => 'Evaluación automática no disponible',
        ];
    }

    /**
     * Fallback para comparación
     */
    protected function fallbackComparacion(): array
    {
        return [
            'calificacion_general' => 'revisar',
            'comentarios' => 'No se pudo realizar la comparación automática. Requiere revisión manual.',
            'mejoras_detectadas' => [],
            'problemas_detectados' => [],
            'recomendaciones' => ['Revisar manualmente las fotos para validar el trabajo'],
        ];
    }

    /**
     * Convierte una imagen a base64 para enviarla a GPT-4 Vision
     */
    protected function convertirImagenABase64(string $ruta): ?string
    {
        try {
            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            
            Log::info('[ChatGPT] Intentando leer imagen', [
                'ruta' => $ruta,
                'existe' => $disk->exists($ruta),
                'path_completo' => $disk->path($ruta),
            ]);
            
            if (!$disk->exists($ruta)) {
                Log::error('[ChatGPT] ❌ La imagen no existe en disco public', [
                    'ruta' => $ruta,
                    'path_completo' => $disk->path($ruta),
                ]);
                return null;
            }

            $contenido = $disk->get($ruta);
            $mimeType = $disk->mimeType($ruta);
            
            Log::info('[ChatGPT] Imagen leída correctamente', [
                'ruta' => $ruta,
                'tamaño' => strlen($contenido),
                'mime_type' => $mimeType,
            ]);
            
            if (!$mimeType || !in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                $mimeType = 'image/jpeg'; // Default
                Log::warning('[ChatGPT] MIME type no reconocido, usando jpeg por defecto', [
                    'mime_original' => $disk->mimeType($ruta),
                ]);
            }

            $base64 = base64_encode($contenido);
            $resultado = 'data:' . $mimeType . ';base64,' . $base64;
            
            Log::info('[ChatGPT] ✅ Imagen convertida a base64', [
                'ruta' => $ruta,
                'base64_length' => strlen($resultado),
            ]);
            
            return $resultado;

        } catch (\Exception $e) {
            Log::error('[ChatGPT] ❌ Error al convertir imagen a base64', [
                'ruta' => $ruta,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Fallback cuando no se puede evaluar con GPT
     */
    protected function fallbackEvaluacionFotos($tarea): array
    {
        Log::info('[ChatGPT] Usando fallback para evaluación de fotos', [
            'tarea_id' => $tarea->id,
        ]);

        return [
            'calificacion_general' => 'revisar',
            'calificacion_antes' => 'revisar',
            'calificacion_despues' => 'revisar',
            'evaluacion_antes' => 'No se pudo evaluar automáticamente. Requiere revisión manual.',
            'evaluacion_despues' => 'No se pudo evaluar automáticamente. Requiere revisión manual.',
            'comentarios' => 'La evaluación automática no está disponible en este momento. Por favor, revisa manualmente las fotos.',
            'mejoras_detectadas' => [],
            'problemas_detectados' => [],
            'recomendaciones' => ['Revisar manualmente las fotos para validar el trabajo'],
        ];
    }
}

