<x-filament-panels::page.simple>
    <div class="mb-8 text-center">
        <!-- Logo/Icono del Sistema -->
        <div class="flex justify-center mb-6">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-400 to-teal-500 rounded-full blur-xl opacity-30"></div>
                <div class="relative bg-gradient-to-br from-emerald-500 to-teal-600 p-6 rounded-full shadow-2xl">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        <!-- Elementos adicionales para hacer parecer una granja -->
                        <circle cx="12" cy="8" r="1" fill="currentColor"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 21h6"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <h2 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-emerald-600 to-teal-600 dark:from-emerald-400 dark:to-teal-400 bg-clip-text text-transparent">
            Sistema Agrícola
        </h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Panel de Administración y Gestión
        </p>
    </div>

    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    <div class="mt-4 text-center text-xs text-gray-600 dark:text-gray-400">
        <p>
            © {{ date('Y') }} Sistema Agrícola. Todos los derechos reservados.
        </p>
    </div>
</x-filament-panels::page.simple>

