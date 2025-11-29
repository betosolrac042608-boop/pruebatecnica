<x-filament-panels::page>
    @php
        $user = auth()->user();
    @endphp

    {{-- DEBUG: Chatbot SIEMPRE visible - Sin condiciones --}}
        {{-- CSS para ocultar sidebar y elementos innecesarios --}}
        <style>
            /* Ocultar sidebar completamente */
            .fi-sidebar,
            .fi-sidebar-nav,
            [x-data*="sidebar"],
            aside[class*="sidebar"] {
                display: none !important;
            }
            
            /* Ajustar contenido para ocupar todo el ancho */
            .fi-main,
            .fi-main-ctn {
                margin-left: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            
            /* Ocultar AccountWidget del topbar */
            .fi-topbar .fi-account-widget,
            .fi-topbar [data-widget="account"] {
                display: none !important;
            }
            
            /* Ocultar welcome message si existe */
            .fi-topbar .fi-welcome-message,
            [class*="welcome"] {
                display: none !important;
            }
            
            /* Asegurar que el contenido ocupe todo el espacio */
            .fi-body {
                padding-left: 0 !important;
            }
        </style>
        {{-- SOLO CHATBOT Y TAREAS PARA OPERADOR Y SUPERVISOR --}}
        {{-- DEBUG: Chatbot siempre visible --}}
        <div class="space-y-6">
            {{-- Mensaje de Debug Visible --}}
            <div class="bg-red-500 text-white p-4 rounded-lg shadow-lg">
                <p class="font-bold">🔍 DEBUG: Chatbot forzado a estar visible</p>
                <p>Usuario: {{ $user->name ?? 'N/A' }}</p>
                <p>Rol ID: {{ $user->rol_id ?? 'N/A' }}</p>
            </div>
            {{-- Header de Bienvenida --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold">¡Hola, {{ $user->name }}! 👋</h2>
                        <p class="mt-2 text-blue-100">
                            Tu plan de trabajo del día {{ now()->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="hidden md:block">
                        <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center">
                            <span class="text-4xl">🤖</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Columna Izquierda: Chatbot --}}
                <div class="space-y-6">
                    {{-- Información del Plan --}}
                    @if($this->planDelDia)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100 flex items-center">
                                <span class="mr-2">📋</span>
                                Plan: {{ $this->planDelDia->predio->nombre ?? 'N/A' }}
                            </h3>
                            <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Estado:</span>
                                    <p class="text-gray-900 dark:text-gray-100 capitalize">{{ $this->planDelDia->estado }}</p>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Tareas:</span>
                                    <p class="text-gray-900 dark:text-gray-100">{{ count($this->tareasDelDia) }}</p>
                                </div>
                            </div>
                            @if($this->planDelDia->resumen_ia)
                                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded border-l-4 border-blue-500">
                                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $this->planDelDia->resumen_ia }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg shadow p-6 border border-yellow-200 dark:border-yellow-700">
                            <p class="text-yellow-800 dark:text-yellow-200">
                                ⚠️ No tienes un plan de trabajo asignado para hoy. Contacta a tu supervisor.
                            </p>
                        </div>
                    @endif

                    {{-- Chatbot --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg flex flex-col" style="height: 600px;">
                        {{-- Header del Chat --}}
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 rounded-t-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                                    <span class="text-xl">🤖</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold">GuardIAno</h3>
                                    <p class="text-sm text-blue-100">Tu capataz digital</p>
                                </div>
                            </div>
                        </div>

                        {{-- Mensajes --}}
                        <div class="flex-1 overflow-y-auto p-4 space-y-4" id="chat-messages">
                            @if(!empty($this->conversaciones))
                                {{-- Mostrar conversaciones existentes --}}
                                @foreach($this->conversaciones as $conversacion)
                                    <div class="flex {{ $conversacion['rol'] === 'user' ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-xs md:max-w-md lg:max-w-lg px-4 py-2 rounded-lg {{ $conversacion['rol'] === 'user' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100' }}">
                                            <div class="flex items-start space-x-2">
                                                @if($conversacion['rol'] === 'assistant')
                                                    <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                                        <span class="text-white text-xs font-bold">G</span>
                                                    </div>
                                                @endif
                                                <div class="flex-1">
                                                    <p class="text-sm whitespace-pre-wrap">{{ $conversacion['mensaje'] }}</p>
                                                    <p class="text-xs mt-1 opacity-75">{{ $conversacion['fecha'] }}</p>
                                                </div>
                                                @if($conversacion['rol'] === 'user')
                                                    <div class="w-6 h-6 bg-blue-800 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                                        <span class="text-white text-xs font-bold">{{ substr($conversacion['usuario'], 0, 1) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @elseif($this->planDelDia && $this->planDelDia->resumen_ia)
                                {{-- Mostrar mensaje inicial de la IA si existe resumen pero no hay conversaciones --}}
                                <div class="flex justify-start">
                                    <div class="max-w-xs md:max-w-md lg:max-w-lg px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                        <div class="flex items-start space-x-2">
                                            <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                                <span class="text-white text-xs font-bold">G</span>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm whitespace-pre-wrap">{{ $this->planDelDia->resumen_ia }}</p>
                                                <p class="text-xs mt-1 opacity-75">{{ now()->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Mensaje de bienvenida cuando no hay plan ni conversaciones --}}
                                <div class="flex justify-start">
                                    <div class="max-w-xs md:max-w-md lg:max-w-lg px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                        <div class="flex items-start space-x-2">
                                            <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                                <span class="text-white text-xs font-bold">G</span>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm">
                                                    ¡Hola! 👋 Soy GuardIAno, tu capataz digital. 
                                                    @if($this->planTrabajoId)
                                                        Estoy aquí para ayudarte con tu plan de trabajo de hoy. ¿En qué puedo asistirte?
                                                    @else
                                                        Actualmente no tienes un plan de trabajo asignado. Una vez que tu supervisor te asigne un plan, podré ayudarte con tus tareas del día.
                                                    @endif
                                                </p>
                                                <p class="text-xs mt-1 opacity-75">{{ now()->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Input de Mensaje --}}
                        <div class="border-t dark:border-gray-700 p-4">
                            <form wire:submit.prevent="enviarMensaje" class="flex space-x-2">
                                <textarea
                                    wire:model="mensaje"
                                    rows="2"
                                    class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="{{ $this->planTrabajoId ? 'Escribe tu mensaje aquí...' : 'Escribe para preguntar sobre tu plan de trabajo...' }}"
                                    style="resize: none;"
                                    @if(!$this->planTrabajoId) disabled @endif
                                ></textarea>
                                <button
                                    type="submit"
                                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    wire:loading.attr="disabled"
                                    @if(!$this->planTrabajoId) disabled @endif
                                >
                                    <span wire:loading.remove wire:target="enviarMensaje">Enviar</span>
                                    <span wire:loading wire:target="enviarMensaje">Enviando...</span>
                                </button>
                            </form>
                            @if(!$this->planTrabajoId)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">
                                    ⚠️ Necesitas un plan de trabajo asignado para chatear con GuardIAno
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Columna Derecha: Tareas del Día --}}
                <div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                        <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100 flex items-center">
                            <span class="mr-2">✅</span>
                            Mis Tareas del Día
                        </h3>

                        @if(count($this->tareasDelDia) > 0)
                            <div class="space-y-3">
                                @foreach($this->tareasDelDia as $tarea)
                                    <div class="border dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2 mb-2">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded {{ $tarea['estado'] === 'completado' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : ($tarea['estado'] === 'en_progreso' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200') }}">
                                                        {{ ucfirst($tarea['estado']) }}
                                                    </span>
                                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                                        {{ $tarea['zona'] }}
                                                    </span>
                                                </div>
                                                <p class="text-gray-900 dark:text-gray-100 mb-2">{{ $tarea['descripcion'] }}</p>
                                                @if($tarea['comentarios'])
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 italic">💬 {{ $tarea['comentarios'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p>No hay tareas asignadas para hoy</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @script
        <script>
            // Auto-scroll cuando se cargan las conversaciones
            Livewire.hook('morph.updated', () => {
                setTimeout(() => {
                    const chatMessages = document.getElementById('chat-messages');
                    if (chatMessages) {
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                }, 100);
            });

            // Auto-scroll después de enviar mensaje
            $wire.on('mensajeEnviado', () => {
                setTimeout(() => {
                    const chatMessages = document.getElementById('chat-messages');
                    if (chatMessages) {
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                }, 100);
            });
        </script>
        @endscript
</x-filament-panels::page>
