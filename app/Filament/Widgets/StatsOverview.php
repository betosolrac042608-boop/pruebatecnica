<?php

namespace App\Filament\Widgets;

use App\Models\Animal;
use App\Models\Cultivo;
use App\Models\Herramienta;
use App\Models\Actividad;
use App\Models\AccionProgramada;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $animalesActivos = Animal::where('estado', 'Activo')->count();
        $cultivosActivos = Cultivo::where('estado', 'En Producción')->count();
        $herramientasFuncionales = Herramienta::where('estado', 'Funcional')->count();
        
        // Obtener ID del estado "Pendiente" de forma segura
        $estadoPendienteId = \App\Models\EstadoActividad::where('nombre', 'Pendiente')->first()?->id;
        $actividadesPendientes = $estadoPendienteId 
            ? Actividad::where('estado_id', $estadoPendienteId)->count()
            : 0;
            
        $accionesProgramadas = AccionProgramada::where('completed', false)->count();

        return [
            Stat::make('Animales Activos', $animalesActivos)
                ->description('Total de animales en el sistema')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 12, 8, 15, 13, 16, $animalesActivos]),
            
            Stat::make('Cultivos en Producción', $cultivosActivos)
                ->description('Cultivos activos')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('info')
                ->chart([3, 5, 4, 6, 5, 7, $cultivosActivos]),
            
            Stat::make('Herramientas Funcionales', $herramientasFuncionales)
                ->description('Equipamiento operativo')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('warning'),
            
            Stat::make('Actividades Pendientes', $actividadesPendientes)
                ->description('Requieren atención')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger')
                ->chart([10, 8, 12, 9, 14, 11, $actividadesPendientes]),
            
            Stat::make('Acciones Programadas', $accionesProgramadas)
                ->description('Próximas tareas')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
        ];
    }
}

