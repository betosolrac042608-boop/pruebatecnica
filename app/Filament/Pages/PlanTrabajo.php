<?php

namespace App\Filament\Pages;

use App\Models\TareaZona;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class PlanTrabajo extends Page
{
    protected static string $view = 'filament.pages.plan-trabajo';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Plan de Trabajo';
    protected static ?string $navigationGroup = 'Gestión de Activos';
    protected static ?int $navigationSort = 5;
    protected static ?string $slug = 'plan-trabajo';

    protected static bool $shouldRegisterNavigation = true;

    public function getViewData(): array
    {
        $tareas = TareaZona::with('zona.predio')
            ->orderBy('predio_id')
            ->orderBy('zona_id')
            ->orderBy('clave')
            ->get();

        $porPredio = $tareas->groupBy(fn (TareaZona $tarea) => $tarea->zona->predio->nombre);

        $resumen = $porPredio->map(fn (Collection $tareas, string $nombrePredio) => [
            'nombre' => $nombrePredio,
            'total' => $tareas->count(),
            'zonas' => $tareas->groupBy(fn (TareaZona $t) => $t->zona->nombre)
                ->map(fn (Collection $items, string $zonaNombre) => [
                    'nombre' => $zonaNombre,
                    'total' => $items->count(),
                    'duracion' => $items->sum('tiempo_minutos'),
                ])
                ->values(),
        ])->values();

        return [
            'tareas' => $tareas,
            'resumen' => $resumen,
        ];
    }
}

