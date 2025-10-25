<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-800 dark:to-teal-800 rounded-lg shadow-lg p-6 border border-emerald-200 dark:border-emerald-700">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-emerald-900 dark:text-white">Bienvenido al Sistema Agrícola</h2>
                    <p class="mt-2 text-emerald-700 dark:text-emerald-100">
                        Panel de administración y gestión de activos agrícolas
                    </p>
                </div>
                <div class="hidden md:block">
                    <svg class="w-24 h-24 text-emerald-300 dark:text-emerald-500 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
            </div>
        </div>

        <x-filament-widgets::widgets
            :widgets="$this->getWidgets()"
            :columns="$this->getColumns()"
        />
    </div>
</x-filament-panels::page>

