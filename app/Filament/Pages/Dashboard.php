<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
    
    protected static ?string $title = 'Dashboard';
    protected static ?string $navigationLabel = 'Inicio';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatsOverview::class,
            \App\Filament\Widgets\AnimalesPorEspecieChart::class,
            \App\Filament\Widgets\ActividadesRecientes::class,
            \App\Filament\Widgets\ProximasAcciones::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 2;
    }
}

