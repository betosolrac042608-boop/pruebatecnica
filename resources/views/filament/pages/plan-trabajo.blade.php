<x-filament::page>
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($resumen as $grupo)
            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Predio</p>
                        <p class="text-lg font-semibold">{{ $grupo['nombre'] }}</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 text-xs font-medium bg-slate-100 text-slate-700 rounded-full">
                        {{ $grupo['total'] }} tareas
                    </span>
                </div>
                <div class="mt-4 space-y-2">
                    @foreach ($grupo['zonas'] as $zona)
                        <div class="flex items-center justify-between text-sm text-slate-600">
                            <span>{{ $zona['nombre'] }}</span>
                            <span class="font-semibold">
                                {{ $zona['total'] }} tareas · {{ $zona['duracion'] ?: 0 }} min
                            </span>
                        </div>
                    @endforeach
                </div>
            </x-filament::card>
        @endforeach
    </div>

    <x-filament::card class="mt-6">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-700">Plan de trabajo por zona</h2>
            <p class="text-sm text-slate-500">Actualizado automáticamente</p>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase text-slate-500 bg-slate-50">
                    <tr>
                        <th class="px-3 py-2">Predio</th>
                        <th class="px-3 py-2">Zona</th>
                        <th class="px-3 py-2">Clave</th>
                        <th class="px-3 py-2">Tarea</th>
                        <th class="px-3 py-2">Frecuencia</th>
                        <th class="px-3 py-2">Tiempo (min)</th>
                        <th class="px-3 py-2">Activo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tareas as $tarea)
                        <tr class="border-b">
                            <td class="px-3 py-2">{{ $tarea->zona->predio->nombre }}</td>
                            <td class="px-3 py-2">{{ $tarea->zona->nombre }}</td>
                            <td class="px-3 py-2 font-semibold">{{ $tarea->clave }}</td>
                            <td class="px-3 py-2">{{ $tarea->nombre }}</td>
                            <td class="px-3 py-2">{{ $tarea->frecuencia }}</td>
                            <td class="px-3 py-2">{{ $tarea->tiempo_minutos ?? '—' }}</td>
                            <td class="px-3 py-2">
                                @if ($tarea->activo)
                                    <span class="px-2 py-0.5 text-xs font-medium text-emerald-700 bg-emerald-100 rounded-full">Activo</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-medium text-slate-700 bg-slate-100 rounded-full">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::card>
</x-filament::page>

