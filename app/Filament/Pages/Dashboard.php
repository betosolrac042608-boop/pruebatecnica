<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
    
    protected static ?string $title = 'Dashboard';
    protected static ?string $navigationLabel = 'Inicio';
    
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        
        // Ocultar del menú para Operador y Supervisor (ellos tienen su propia página)
        if ($user && $user->rol_id) {
            if (!$user->relationLoaded('rol')) {
                $user->load('rol');
            }
            
            if ($user->rol && in_array($user->rol->nombre, ['Operador', 'Supervisor'])) {
                return false;
            }
        }
        
        return true;
    }

    public function getWidgets(): array
    {
        $user = Auth::user();
        
        // Si es Operador o Supervisor, no mostrar widgets (deberían estar en su página)
        if ($user && $user->rol_id) {
            if (!$user->relationLoaded('rol')) {
                $user->load('rol');
            }
            
            if ($user->rol && in_array($user->rol->nombre, ['Operador', 'Supervisor'])) {
                return [];
            }
        }

        // Para otros roles (Admin), mostrar widgets normales
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

    public static function canAccess(): bool
    {
        return true;
    }

    public function mount(): void
    {
        $user = Auth::user();
        
        // Si es Operador o Supervisor, redirigir a su página de trabajo diario
        if ($user && $user->rol_id) {
            if (!$user->relationLoaded('rol')) {
                $user->load('rol');
            }
            
            if ($user->rol && in_array($user->rol->nombre, ['Operador', 'Supervisor'])) {
                $this->redirect(route('filament.admin.pages.plan-trabajo-operario'));
            }
        }
    }
}
