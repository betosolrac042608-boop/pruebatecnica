<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Auth;

class Login extends BaseLogin
{
    /**
     * Obtener la URL de redirección después del login exitoso
     */
    protected function getRedirectUrl(): string
    {
        $user = Auth::user();
        
        if ($user && $user->rol_id) {
            if (!$user->relationLoaded('rol')) {
                $user->load('rol');
            }
            
            // Si es Operador o Supervisor, redirigir a la página de trabajo diario
            if ($user->rol && in_array($user->rol->nombre, ['Operador', 'Supervisor'])) {
                return route('filament.admin.pages.plan-trabajo-operario');
            }
        }
        
        // Para otros roles, redirigir al dashboard por defecto
        return parent::getRedirectUrl();
    }
}

