<x-filament-panels::page>
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
        
        /* Asegurar que el contenido ocupe todo el espacio */
        .fi-body {
            padding-left: 0 !important;
        }

        /* Forzar que el contenedor de la página tenga ancho completo */
        .fi-page-content,
        .fi-page-content > div,
        [class*="fi-page"],
        .fi-page > div {
            max-width: 100% !important;
            width: 100% !important;
        }

        .fi-main-ctn > div,
        .fi-main > div {
            max-width: 100% !important;
        }

        /* Forzar grid de dos columnas */
        #main-grid {
            display: grid !important;
            width: 100% !important;
            grid-template-columns: 1fr !important;
        }

        @media (min-width: 1024px) {
            #main-grid {
                grid-template-columns: 1fr 1fr !important;
            }
        }

        #main-grid > div {
            width: 100% !important;
            min-width: 0 !important;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out;
        }

        /* Scrollbar personalizado */
        #chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        #chat-messages::-webkit-scrollbar-track {
            background: rgb(248 250 252);
            border-radius: 10px;
        }

        #chat-messages::-webkit-scrollbar-thumb {
            background: rgb(203 213 225);
            border-radius: 10px;
        }

        .dark #chat-messages::-webkit-scrollbar-track {
            background: rgb(15 23 42);
        }

        .dark #chat-messages::-webkit-scrollbar-thumb {
            background: rgb(51 65 85);
        }

        /* Gradientes profesionales - siempre oscuros */
        .gradient-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%) !important;
        }

        .gradient-success {
            background: linear-gradient(135deg, #047857 0%, #059669 50%, #10b981 100%) !important;
        }

        /* Asegurar que los textos en gradientes siempre sean blancos */
        .gradient-header *,
        .gradient-success * {
            color: white !important;
        }

        .gradient-header h2,
        .gradient-header h3,
        .gradient-header p,
        .gradient-header span {
            color: white !important;
        }

        .gradient-success h3,
        .gradient-success span {
            color: white !important;
        }

        /* Forzar fondos oscuros en dark mode para todos los componentes */
        .dark .bg-white {
            background-color: rgb(15 23 42) !important; /* slate-900 */
        }

        /* Forzar fondos de contenedores principales */
        .dark [class*="bg-white"] {
            background-color: rgb(15 23 42) !important;
        }

        /* Asegurar que los mensajes del asistente tengan fondo oscuro */
        .dark .bg-white.dark\:bg-slate-800 {
            background-color: rgb(30 41 59) !important; /* slate-800 */
        }

        /* Forzar fondo del área de mensajes */
        .dark #chat-messages {
            background-color: rgb(2 6 23) !important; /* slate-950 */
        }

        /* Forzar fondos de tarjetas de información */
        .dark .bg-slate-50 {
            background-color: rgb(30 41 59) !important; /* slate-800 */
        }

        /* Forzar fondo del input */
        .dark .bg-slate-50.dark\:bg-slate-900 {
            background-color: rgb(15 23 42) !important; /* slate-900 */
        }

        /* Asegurar que todos los textos sean visibles en dark */
        .dark .text-slate-900 {
            color: rgb(241 245 249) !important; /* slate-100 */
        }

        .dark .text-slate-800 {
            color: rgb(226 232 240) !important; /* slate-200 */
        }

        .dark .text-slate-700 {
            color: rgb(203 213 225) !important; /* slate-300 */
        }
    </style>

    @php
        $user = auth()->user();
    @endphp

    <div class="space-y-6 animate-fade-in-up">
        {{-- Header de Bienvenida --}}
        <div class="relative gradient-header text-white dark:text-white rounded-xl shadow-xl p-8 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-950/20 via-transparent to-slate-950/20"></div>
            
            <div class="relative flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex-1 mb-4 md:mb-0">
                    <div class="flex items-center space-x-4 mb-3">
                        <div class="w-14 h-14 bg-emerald-500/20 backdrop-blur-md rounded-xl flex items-center justify-center border border-emerald-500/30 shadow-lg">
                            <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-3xl lg:text-4xl font-bold tracking-tight mb-1 text-white dark:text-white">¡Hola, {{ $user->name }}! 👋</h2>
                            <p class="text-slate-200 dark:text-slate-200 text-sm lg:text-base font-medium">
                                {{ now()->translatedFormat('l, d \d\e F \d\e Y') }}
                            </p>
                        </div>
                    </div>
                    <p class="text-slate-300 dark:text-slate-300 text-sm lg:text-base ml-[72px] font-medium">
                        Aquí está tu plan de trabajo del día
                    </p>
                </div>
                <div class="hidden md:block relative">
                    <div class="w-20 h-20 lg:w-24 lg:h-24 bg-emerald-500/20 backdrop-blur-md rounded-xl flex items-center justify-center shadow-xl border border-emerald-500/30">
                        <span class="text-4xl lg:text-5xl">📋</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grid Principal --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 w-full" id="main-grid">
            {{-- COLUMNA IZQUIERDA: CHATBOT --}}
            <div class="space-y-6 order-1 lg:order-1">
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-xl flex flex-col border border-slate-200 dark:border-slate-700 overflow-hidden h-full" style="min-height: 650px;">
                    {{-- Header del Chat --}}
                    <div class="gradient-header text-white dark:text-white p-6 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="relative">
                                    <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg">
                                        <span class="text-2xl">🤖</span>
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-400 border-2 border-slate-900 rounded-full shadow-lg animate-pulse"></div>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg tracking-tight text-white dark:text-white">GuardIAno</h3>
                                    <p class="text-sm text-slate-200 dark:text-slate-200 font-medium flex items-center gap-1.5">
                                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                                        Asistente Virtual Activo
                                    </p>
                                </div>
                            </div>
                            <div class="hidden md:flex items-center space-x-2 px-3 py-1.5 bg-emerald-500/20 rounded-lg backdrop-blur-sm border border-emerald-500/30">
                                <svg class="w-4 h-4 text-emerald-300 dark:text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-xs font-bold text-emerald-100 dark:text-emerald-100">En línea</span>
                            </div>
                        </div>
                    </div>

                    {{-- Mensajes --}}
                    <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-slate-50 dark:bg-slate-950" id="chat-messages">
                        @if(!empty($this->conversaciones))
                            @foreach($this->conversaciones as $conversacion)
                                <div class="flex {{ $conversacion['rol'] === 'user' ? 'justify-end' : 'justify-start' }} animate-fade-in-up">
                                    <div class="flex items-start space-x-3 max-w-[85%] {{ $conversacion['rol'] === 'user' ? 'flex-row-reverse space-x-reverse' : '' }}">
                                        @if($conversacion['rol'] === 'assistant')
                                            <div class="flex-shrink-0 w-9 h-9 bg-gradient-to-br from-emerald-600 to-teal-600 rounded-lg flex items-center justify-center shadow-md">
                                                <span class="text-white text-xs font-bold">GA</span>
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            @if($conversacion['rol'] === 'user')
                                                <div class="rounded-xl bg-slate-700 shadow-lg px-4 py-3 border border-slate-600">
                                                    <p class="text-sm leading-relaxed whitespace-pre-wrap text-white font-medium">{{ $conversacion['mensaje'] }}</p>
                                                </div>
                                            @else
                                                <div class="rounded-xl bg-white dark:bg-slate-800 shadow-md border border-slate-200 dark:border-slate-700 px-4 py-3">
                                                    <p class="text-sm leading-relaxed whitespace-pre-wrap text-slate-900 dark:text-slate-100">{{ $conversacion['mensaje'] }}</p>
                                                </div>
                                            @endif
                                            <p class="text-xs mt-1.5 {{ $conversacion['rol'] === 'user' ? 'text-right' : '' }} text-slate-600 dark:text-slate-400 font-medium">
                                                {{ $conversacion['fecha'] }}
                                            </p>
                                        </div>
                                        @if($conversacion['rol'] === 'user')
                                            <div class="flex-shrink-0 w-9 h-9 bg-gradient-to-br from-slate-600 to-slate-700 rounded-lg flex items-center justify-center shadow-md border border-slate-500">
                                                <span class="text-white text-xs font-bold">{{ strtoupper(substr($conversacion['usuario'], 0, 2)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            
                            
                            {{-- Indicador de "escribiendo..." --}}
                            <div class="flex justify-start animate-fade-in-up" wire:loading wire:target="enviarMensaje">
                                <div class="flex items-start space-x-3 max-w-[85%]">
                                    <div class="flex-shrink-0 w-9 h-9 bg-gradient-to-br from-emerald-600 to-teal-600 rounded-lg flex items-center justify-center shadow-md">
                                        <span class="text-white text-xs font-bold">GA</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-md border border-slate-200 dark:border-slate-700 px-4 py-3">
                                            <div class="flex items-center space-x-2">
                                                <div class="flex space-x-1">
                                                    <div class="w-2 h-2 bg-emerald-500 dark:bg-emerald-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                                                    <div class="w-2 h-2 bg-emerald-500 dark:bg-emerald-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                                                    <div class="w-2 h-2 bg-emerald-500 dark:bg-emerald-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                                                </div>
                                                <span class="text-xs text-slate-600 dark:text-slate-400 italic font-medium">GuardIAno está escribiendo...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($this->planDelDia && $this->planDelDia->resumen_ia)
                            <div class="flex justify-start animate-fade-in-up">
                                <div class="flex items-start space-x-3 max-w-[85%]">
                                    <div class="flex-shrink-0 w-9 h-9 bg-gradient-to-br from-emerald-600 to-teal-600 rounded-lg flex items-center justify-center shadow-md">
                                        <span class="text-white text-xs font-bold">GA</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-md border border-slate-200 dark:border-slate-700 px-4 py-3">
                                            <p class="text-sm text-slate-900 dark:text-slate-100 leading-relaxed whitespace-pre-wrap">{{ $this->planDelDia->resumen_ia }}</p>
                                        </div>
                                        <p class="text-xs mt-1.5 text-slate-600 dark:text-slate-400 font-medium">{{ now()->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex justify-start animate-fade-in-up">
                                <div class="flex items-start space-x-3 max-w-[85%]">
                                    <div class="flex-shrink-0 w-9 h-9 bg-gradient-to-br from-emerald-600 to-teal-600 rounded-lg flex items-center justify-center shadow-md">
                                        <span class="text-white text-xs font-bold">GA</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-md border border-slate-200 dark:border-slate-700 px-4 py-3">
                                            <p class="text-sm text-slate-900 dark:text-slate-100 leading-relaxed">
                                                ¡Hola! 👋 Soy GuardIAno, tu asistente virtual. 
                                                @if($this->planTrabajoId)
                                                    Estoy aquí para ayudarte con tu plan de trabajo de hoy. ¿En qué puedo asistirte?
                                                @else
                                                    Actualmente no tienes un plan de trabajo asignado. Una vez que tu supervisor te asigne un plan, podré ayudarte con tus tareas del día.
                                                @endif
                                            </p>
                                        </div>
                                        <p class="text-xs mt-1.5 text-slate-600 dark:text-slate-400 font-medium">{{ now()->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Input de Mensaje --}}
                    <div class="border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 p-4">
                        <form wire:submit.prevent="enviarMensaje" class="flex items-end space-x-3">
                            <div class="flex-1 relative">
                                <textarea
                                    wire:model="mensaje"
                                    rows="2"
                                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-500 dark:placeholder-slate-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-emerald-400/20 transition-all resize-none px-4 py-3 text-sm shadow-sm"
                                    placeholder="{{ $this->planTrabajoId ? 'Escribe tu mensaje aquí...' : 'Escribe tu consulta...' }}"
                                    @if(!$this->planTrabajoId) disabled @endif
                                ></textarea>
                            </div>
                            <button
                                type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white rounded-lg font-bold transition-all shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center min-w-[60px]"
                                wire:loading.attr="disabled"
                                @if(!$this->planTrabajoId) disabled @endif
                            >
                                <span wire:loading.remove wire:target="enviarMensaje">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </span>
                                <span wire:loading wire:target="enviarMensaje" class="flex items-center">
                                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                        </form>
                        @if(!$this->planTrabajoId)
                            <p class="text-xs text-amber-700 dark:text-amber-300 mt-3 text-center flex items-center justify-center space-x-1.5 font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Necesitas un plan de trabajo asignado</span>
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: TAREAS --}}
            <div class="space-y-6 order-2 lg:order-2">
                {{-- Información del Plan --}}
                @if($this->planDelDia)
                    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden transition-all hover:shadow-xl">
                        <div class="gradient-success p-5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold text-white dark:text-white flex items-center tracking-tight">
                                    <svg class="w-5 h-5 mr-2 text-white dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    {{ $this->planDelDia->predio->nombre ?? 'N/A' }}
                                </h3>
                                <span class="px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-lg text-xs font-bold text-white dark:text-white capitalize border border-white/30">
                                    {{ $this->planDelDia->estado }}
                                </span>
                            </div>
                        </div>
                        <div class="p-6 bg-white dark:bg-slate-900">
                            <div class="grid grid-cols-2 gap-4 mb-5">
                                <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/40 rounded-lg border border-emerald-200 dark:border-emerald-700">
                                            <svg class="w-5 h-5 text-emerald-700 dark:text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-600 dark:text-slate-400 font-bold uppercase tracking-wider">Tareas</p>
                                            <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ count($this->tareasDelDia) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-slate-200 dark:bg-slate-700 rounded-lg border border-slate-300 dark:border-slate-600">
                                            <svg class="w-5 h-5 text-slate-700 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-600 dark:text-slate-400 font-bold uppercase tracking-wider">Fecha</p>
                                            <p class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $this->planDelDia->fecha->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($this->planDelDia->resumen_ia)
                                <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 rounded-lg border-l-4 border-emerald-600 dark:border-emerald-500">
                                    <p class="text-sm text-emerald-900 dark:text-emerald-100 whitespace-pre-wrap leading-relaxed font-medium">{{ $this->planDelDia->resumen_ia }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-amber-50 dark:bg-amber-950/30 rounded-xl shadow-lg p-6 border-2 border-amber-400 dark:border-amber-700">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-amber-200 dark:bg-amber-900/50 rounded-lg flex items-center justify-center border border-amber-400 dark:border-amber-700">
                                    <svg class="w-6 h-6 text-amber-700 dark:text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-amber-900 dark:text-amber-100 mb-2">Sin plan asignado</h3>
                                <p class="text-sm text-amber-800 dark:text-amber-200 font-medium leading-relaxed">
                                    No tienes un plan de trabajo asignado para hoy. Contacta a tu supervisor para que te asigne uno.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Lista de Tareas --}}
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="gradient-success p-5">
                        <h3 class="text-xl font-bold text-white dark:text-white flex items-center tracking-tight">
                            <svg class="w-6 h-6 mr-2 text-white dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            Mis Tareas del Día
                            @if(count($this->tareasDelDia) > 0)
                                <span class="ml-2 px-2.5 py-1 bg-white/20 backdrop-blur-sm rounded-lg text-sm font-bold text-white dark:text-white border border-white/30">
                                    {{ count($this->tareasDelDia) }}
                                </span>
                            @endif
                        </h3>
                    </div>

                    <div class="p-6 bg-white dark:bg-slate-900 max-h-[600px] overflow-y-auto">
                        @if(count($this->tareasDelDia) > 0)
                            <div class="space-y-3">
                                @foreach($this->tareasDelDia as $index => $tarea)
                                    <div class="group bg-slate-50 dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700 hover:shadow-lg hover:border-emerald-500 dark:hover:border-emerald-500 transition-all duration-200">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center {{ $tarea['estado'] === 'completado' ? 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-700' : ($tarea['estado'] === 'en_progreso' ? 'bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-200 border border-amber-300 dark:border-amber-700' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-600') }}">
                                                @if($tarea['estado'] === 'completado')
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @elseif($tarea['estado'] === 'en_progreso')
                                                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center flex-wrap gap-2 mb-2">
                                                    <span class="px-2.5 py-1 text-xs font-bold rounded-md uppercase tracking-wide {{ $tarea['estado'] === 'completado' ? 'bg-emerald-100 text-emerald-900 dark:bg-emerald-900/60 dark:text-emerald-100 border border-emerald-300 dark:border-emerald-700' : ($tarea['estado'] === 'en_progreso' ? 'bg-amber-100 text-amber-900 dark:bg-amber-900/60 dark:text-amber-100 border border-amber-300 dark:border-amber-700' : 'bg-slate-200 text-slate-900 dark:bg-slate-700 dark:text-slate-100 border border-slate-300 dark:border-slate-600') }}">
                                                        {{ ucfirst($tarea['estado']) }}
                                                    </span>
                                                    <span class="text-sm font-bold text-slate-900 dark:text-slate-100 truncate">
                                                        📍 {{ $tarea['zona'] }}
                                                    </span>
                                                </div>
                                                <p class="text-sm text-slate-900 dark:text-slate-100 font-medium leading-relaxed mb-2">{{ $tarea['descripcion'] }}</p>
                                                
                                                {{-- Botones compactos para fotos --}}
                                                <div class="mt-2 flex flex-wrap gap-2">
                                                    @if($tarea['foto_antes'])
                                                        <button 
                                                            x-data="{ open: false }"
                                                            @click="open = !open"
                                                            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border transition-colors {{ $tarea['foto_antes']['calificacion'] === 'aprobado' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-700' : ($tarea['foto_antes']['calificacion'] === 'rechazado' ? 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700' : 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-700') }} hover:opacity-80">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                            Foto Antes
                                                            @if($tarea['foto_antes']['calificacion'])
                                                                <span class="px-1.5 py-0.5 text-[10px] rounded {{ $tarea['foto_antes']['calificacion'] === 'aprobado' ? 'bg-emerald-200 text-emerald-800 dark:bg-emerald-800 dark:text-emerald-200' : ($tarea['foto_antes']['calificacion'] === 'rechazado' ? 'bg-red-200 text-red-800 dark:bg-red-800 dark:text-red-200' : 'bg-amber-200 text-amber-800 dark:bg-amber-800 dark:text-amber-200') }}">
                                                                    {{ ucfirst($tarea['foto_antes']['calificacion']) }}
                                                                </span>
                                                            @endif
                                                        </button>
                                                    @else
                                                        <label class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer transition-colors">
                                                            <input type="file" class="hidden" accept="image/*" 
                                                                   x-on:change="$wire.upload('fotoTemporal', $event.target.files[0], () => { $wire.call('procesarFotoSubida', {{ $tarea['id'] }}, 'antes'); }, () => { console.error('Error al subir'); })"
                                                                   wire:loading.attr="disabled">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                            </svg>
                                                            <span wire:loading.remove wire:target="fotoTemporal">Subir Antes</span>
                                                            <span wire:loading wire:target="fotoTemporal" class="text-emerald-500 flex items-center gap-1">
                                                                <svg class="animate-spin h-3 w-3" fill="none" viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                </svg>
                                                                Subiendo...
                                                            </span>
                                                        </label>
                                                    @endif

                                                    @if($tarea['foto_despues'])
                                                        <button 
                                                            x-data="{ open: false }"
                                                            @click="open = !open"
                                                            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border transition-colors {{ $tarea['foto_despues']['calificacion'] === 'aprobado' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-700' : ($tarea['foto_despues']['calificacion'] === 'rechazado' ? 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-700' : 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-700') }} hover:opacity-80">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                            Foto Después
                                                            @if($tarea['foto_despues']['calificacion'])
                                                                <span class="px-1.5 py-0.5 text-[10px] rounded {{ $tarea['foto_despues']['calificacion'] === 'aprobado' ? 'bg-emerald-200 text-emerald-800 dark:bg-emerald-800 dark:text-emerald-200' : ($tarea['foto_despues']['calificacion'] === 'rechazado' ? 'bg-red-200 text-red-800 dark:bg-red-800 dark:text-red-200' : 'bg-amber-200 text-amber-800 dark:bg-amber-800 dark:text-amber-200') }}">
                                                                    {{ ucfirst($tarea['foto_despues']['calificacion']) }}
                                                                </span>
                                                            @endif
                                                        </button>
                                                    @else
                                                        <label class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer transition-colors">
                                                            <input type="file" class="hidden" accept="image/*" 
                                                                   x-on:change="$wire.upload('fotoTemporal', $event.target.files[0], () => { $wire.call('procesarFotoSubida', {{ $tarea['id'] }}, 'despues'); }, () => { console.error('Error al subir'); })"
                                                                   wire:loading.attr="disabled">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                            </svg>
                                                            <span wire:loading.remove wire:target="fotoTemporal">Subir Después</span>
                                                            <span wire:loading wire:target="fotoTemporal" class="text-emerald-500 flex items-center gap-1">
                                                                <svg class="animate-spin h-3 w-3" fill="none" viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                </svg>
                                                                Subiendo...
                                                            </span>
                                                        </label>
                                                    @endif

                                                    @if($tarea['evaluacion_completa'])
                                                        <button 
                                                            x-data="{ open: false }"
                                                            @click="open = !open"
                                                            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-blue-300 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            Ver Evaluación GPT
                                                            <svg class="w-3 h-3 transition-transform" x-bind:class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                            </svg>
                                                        </button>
                                                    @endif
                                                </div>

                                                {{-- Acordeón para Fotos y Evaluación --}}
                                                <div x-data="{ 
                                                    openAntes: false, 
                                                    openDespues: false, 
                                                    openEvaluacion: false 
                                                }" class="mt-2 space-y-2">
                                                    {{-- Acordeón Foto Antes --}}
                                                    @if($tarea['foto_antes'])
                                                        <div class="border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden">
                                                            <button 
                                                                @click="openAntes = !openAntes"
                                                                class="w-full flex items-center justify-between px-3 py-2 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                                                <span class="text-xs font-medium text-slate-700 dark:text-slate-300">📸 Foto Antes</span>
                                                                <svg class="w-4 h-4 text-slate-500 transition-transform" x-bind:class="openAntes ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                                </svg>
                                                            </button>
                                                            <div x-show="openAntes" x-transition class="p-3 bg-white dark:bg-slate-900">
                                                                <div class="relative group mb-2">
                                                                    <img src="{{ $tarea['foto_antes']['url'] }}" alt="Foto antes" class="w-full h-48 object-cover rounded-lg cursor-pointer" onclick="window.open('{{ $tarea['foto_antes']['url'] }}', '_blank')">
                                                                    <button wire:click="eliminarFoto({{ $tarea['foto_antes']['id'] }})" class="absolute top-2 right-2 bg-red-500 text-white p-1.5 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                                @if($tarea['foto_antes']['evaluacion'])
                                                                    <div class="mt-2 p-2 bg-slate-50 dark:bg-slate-800 rounded text-xs text-slate-700 dark:text-slate-300">
                                                                        <strong>Evaluación GPT:</strong>
                                                                        <p class="mt-1 italic">{{ $tarea['foto_antes']['evaluacion'] }}</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif

                                                    {{-- Acordeón Foto Después --}}
                                                    @if($tarea['foto_despues'])
                                                        <div class="border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden">
                                                            <button 
                                                                @click="openDespues = !openDespues"
                                                                class="w-full flex items-center justify-between px-3 py-2 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                                                <span class="text-xs font-medium text-slate-700 dark:text-slate-300">📸 Foto Después</span>
                                                                <svg class="w-4 h-4 text-slate-500 transition-transform" x-bind:class="openDespues ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                                </svg>
                                                            </button>
                                                            <div x-show="openDespues" x-transition class="p-3 bg-white dark:bg-slate-900">
                                                                <div class="relative group mb-2">
                                                                    <img src="{{ $tarea['foto_despues']['url'] }}" alt="Foto después" class="w-full h-48 object-cover rounded-lg cursor-pointer" onclick="window.open('{{ $tarea['foto_despues']['url'] }}', '_blank')">
                                                                    <button wire:click="eliminarFoto({{ $tarea['foto_despues']['id'] }})" class="absolute top-2 right-2 bg-red-500 text-white p-1.5 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                                @if($tarea['foto_despues']['evaluacion'])
                                                                    <div class="mt-2 p-2 bg-slate-50 dark:bg-slate-800 rounded text-xs text-slate-700 dark:text-slate-300">
                                                                        <strong>Evaluación GPT:</strong>
                                                                        <p class="mt-1 italic">{{ $tarea['foto_despues']['evaluacion'] }}</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif

                                                    {{-- Acordeón Evaluación Completa GPT --}}
                                                    @if($tarea['evaluacion_completa'])
                                                        <div class="border border-blue-200 dark:border-blue-800 rounded-lg overflow-hidden">
                                                            <button 
                                                                @click="openEvaluacion = !openEvaluacion"
                                                                class="w-full flex items-center justify-between px-3 py-2 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                                                                <span class="text-xs font-medium text-blue-700 dark:text-blue-300">🤖 Evaluación Completa GPT</span>
                                                                <svg class="w-4 h-4 text-blue-500 transition-transform" x-bind:class="openEvaluacion ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                                </svg>
                                                            </button>
                                                            <div x-show="openEvaluacion" x-transition class="p-4 bg-white dark:bg-slate-900 space-y-3">
                                                                @if($tarea['evaluacion_completa']['calificacion_general'])
                                                                    <div class="flex items-center gap-2">
                                                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Calificación General:</span>
                                                                        <span class="px-2 py-1 text-xs rounded font-medium {{ $tarea['evaluacion_completa']['calificacion_general'] === 'aprobado' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300' : ($tarea['evaluacion_completa']['calificacion_general'] === 'rechazado' ? 'bg-red-100 text-red-700 dark:bg-red-900/60 dark:text-red-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/60 dark:text-amber-300') }}">
                                                                            {{ ucfirst($tarea['evaluacion_completa']['calificacion_general']) }}
                                                                        </span>
                                                                    </div>
                                                                @endif

                                                                @if($tarea['evaluacion_completa']['evaluacion_antes'])
                                                                    <div class="p-2 bg-slate-50 dark:bg-slate-800 rounded">
                                                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">📸 Evaluación Foto Antes:</p>
                                                                        <p class="text-xs text-slate-600 dark:text-slate-400 italic">{{ $tarea['evaluacion_completa']['evaluacion_antes'] }}</p>
                                                                    </div>
                                                                @endif

                                                                @if($tarea['evaluacion_completa']['evaluacion_despues'])
                                                                    <div class="p-2 bg-slate-50 dark:bg-slate-800 rounded">
                                                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">📸 Evaluación Foto Después:</p>
                                                                        <p class="text-xs text-slate-600 dark:text-slate-400 italic">{{ $tarea['evaluacion_completa']['evaluacion_despues'] }}</p>
                                                                    </div>
                                                                @endif

                                                                @if($tarea['evaluacion_completa']['comentarios'])
                                                                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded">
                                                                        <p class="text-xs font-bold text-blue-700 dark:text-blue-300 mb-1">💬 Comentarios:</p>
                                                                        <p class="text-xs text-blue-600 dark:text-blue-400">{{ $tarea['evaluacion_completa']['comentarios'] }}</p>
                                                                    </div>
                                                                @endif

                                                                @if(!empty($tarea['evaluacion_completa']['mejoras_detectadas']))
                                                                    <div class="p-2 bg-emerald-50 dark:bg-emerald-900/20 rounded">
                                                                        <p class="text-xs font-bold text-emerald-700 dark:text-emerald-300 mb-1">✅ Mejoras Detectadas:</p>
                                                                        <ul class="text-xs text-emerald-600 dark:text-emerald-400 list-disc list-inside space-y-1">
                                                                            @foreach($tarea['evaluacion_completa']['mejoras_detectadas'] as $mejora)
                                                                                <li>{{ $mejora }}</li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                @endif

                                                                @if(!empty($tarea['evaluacion_completa']['problemas_detectados']))
                                                                    <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded">
                                                                        <p class="text-xs font-bold text-red-700 dark:text-red-300 mb-1">⚠️ Problemas Detectados:</p>
                                                                        <ul class="text-xs text-red-600 dark:text-red-400 list-disc list-inside space-y-1">
                                                                            @foreach($tarea['evaluacion_completa']['problemas_detectados'] as $problema)
                                                                                <li>{{ $problema }}</li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                @endif

                                                                @if(!empty($tarea['evaluacion_completa']['recomendaciones']))
                                                                    <div class="p-2 bg-amber-50 dark:bg-amber-900/20 rounded">
                                                                        <p class="text-xs font-bold text-amber-700 dark:text-amber-300 mb-1">💡 Recomendaciones:</p>
                                                                        <ul class="text-xs text-amber-600 dark:text-amber-400 list-disc list-inside space-y-1">
                                                                            @foreach($tarea['evaluacion_completa']['recomendaciones'] as $recomendacion)
                                                                                <li>{{ $recomendacion }}</li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                @if($tarea['comentarios'])
                                                    <div class="mt-2 flex items-start space-x-2 bg-slate-100 dark:bg-slate-800/80 rounded-lg p-2.5 border border-slate-200 dark:border-slate-700">
                                                        <svg class="w-4 h-4 text-slate-700 dark:text-slate-300 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                                        </svg>
                                                        <p class="text-xs text-slate-900 dark:text-slate-100 italic flex-1 font-medium">{{ $tarea['comentarios'] }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-16">
                                <div class="w-20 h-20 mx-auto mb-4 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-center border border-slate-200 dark:border-slate-700">
                                    <svg class="w-10 h-10 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <p class="text-slate-900 dark:text-slate-100 font-bold text-lg mb-1">Sin tareas asignadas</p>
                                <p class="text-sm text-slate-600 dark:text-slate-400 font-medium">Tu supervisor te asignará tareas pronto</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        Livewire.hook('morph.updated', () => {
            setTimeout(() => {
                const chatMessages = document.getElementById('chat-messages');
                if (chatMessages) {
                    chatMessages.scrollTo({
                        top: chatMessages.scrollHeight,
                        behavior: 'smooth'
                    });
                }
            }, 100);
        });

        $wire.on('mensajeEnviado', () => {
            setTimeout(() => {
                const chatMessages = document.getElementById('chat-messages');
                if (chatMessages) {
                    chatMessages.scrollTo({
                        top: chatMessages.scrollHeight,
                        behavior: 'smooth'
                    });
                }
            }, 100);
        });

        document.addEventListener('DOMContentLoaded', () => {
            const textarea = document.querySelector('textarea[wire\\:model="mensaje"]');
            if (textarea && !textarea.disabled) {
                setTimeout(() => textarea.focus(), 200);
            }
        });
    </script>
    @endscript
</x-filament-panels::page>
