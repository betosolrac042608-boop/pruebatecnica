<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Selector de Plan -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            {{ $this->form }}
        </div>

        @if($this->planSeleccionado)
            <!-- Información del Plan -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                    📋 Plan de Trabajo: {{ $this->planSeleccionado->predio->nombre ?? 'N/A' }}
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700 dark:text-gray-300">Fecha:</span>
                        <p class="text-gray-900 dark:text-gray-100">{{ $this->planSeleccionado->fecha->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700 dark:text-gray-300">Estado:</span>
                        <p class="text-gray-900 dark:text-gray-100 capitalize">{{ $this->planSeleccionado->estado }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700 dark:text-gray-300">Tareas:</span>
                        <p class="text-gray-900 dark:text-gray-100">{{ $this->planSeleccionado->tareas->count() }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700 dark:text-gray-300">Encargado:</span>
                        <p class="text-gray-900 dark:text-gray-100">{{ $this->planSeleccionado->usuario->name ?? 'N/A' }}</p>
                    </div>
                </div>
                @if($this->planSeleccionado->resumen_ia)
                    <div class="mt-4 p-4 bg-white dark:bg-gray-700 rounded border-l-4 border-blue-500">
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $this->planSeleccionado->resumen_ia }}</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Área de Chat -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg flex flex-col" style="height: 600px;">
            <!-- Header del Chat -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 rounded-t-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold">GuardIAno</h3>
                        <p class="text-sm text-blue-100">Tu capataz digital</p>
                    </div>
                </div>
            </div>

            <!-- Mensajes -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4" id="chat-messages">
                @if(empty($this->conversaciones))
                    <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                        <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <p>Selecciona un plan de trabajo para comenzar a chatear con GuardIAno</p>
                    </div>
                @else
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
                @endif
            </div>

            <!-- Input de Mensaje -->
            @if($this->planTrabajoId)
                <div class="border-t dark:border-gray-700 p-4">
                    <form wire:submit.prevent="enviarMensaje" class="flex space-x-2">
                        <textarea
                            wire:model="mensaje"
                            rows="2"
                            class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Escribe tu mensaje aquí..."
                            style="resize: none;"
                        ></textarea>
                        <button
                            type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="enviarMensaje">Enviar</span>
                            <span wire:loading wire:target="enviarMensaje">Enviando...</span>
                        </button>
                    </form>
                </div>
            @else
                <div class="border-t dark:border-gray-700 p-4 text-center text-gray-500 dark:text-gray-400">
                    <p>Selecciona un plan de trabajo para comenzar</p>
                </div>
            @endif
        </div>
    </div>

    @script
    <script>
        $wire.on('mensajeEnviado', () => {
            setTimeout(() => {
                const chatMessages = document.getElementById('chat-messages');
                if (chatMessages) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            }, 100);
        });

        // Auto-scroll cuando se cargan las conversaciones
        Livewire.hook('morph.updated', () => {
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

