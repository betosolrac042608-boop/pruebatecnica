<?php

namespace App\Filament\Concerns;

use Illuminate\Support\Facades\Auth;

trait HideFromOperarioSupervisor
{
    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        
        if (!$user || !$user->rol_id) {
            return true;
        }

        // Cargar el rol si no está cargado
        if (!$user->relationLoaded('rol')) {
            $user->load('rol');
        }

        if (!$user->rol) {
            return true;
        }

        // Ocultar del menú para Operador y Supervisor
        if (in_array($user->rol->nombre, ['Operador', 'Supervisor'])) {
            return false;
        }

        return true;
    }
}

