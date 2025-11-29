<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToPlanTrabajo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo aplicar si el usuario está autenticado
        if (auth()->check()) {
            $user = auth()->user();
            
            if ($user && $user->rol_id) {
                if (!$user->relationLoaded('rol')) {
                    $user->load('rol');
                }
                
                // Si es Operador o Supervisor, redirigir a su página principal
                if ($user->rol && in_array($user->rol->nombre, ['Operador', 'Supervisor'])) {
                    // Redirigir si está accediendo al dashboard o a la raíz del admin
                    if ($request->is('admin') && !$request->is('admin/*')) {
                        return redirect('/admin/plan-trabajo-operario');
                    }
                    
                    // También redirigir si intenta acceder al dashboard directamente
                    if ($request->is('admin/dashboard') || $request->is('admin/dashboard/*')) {
                        return redirect('/admin/plan-trabajo-operario');
                    }
                }
            }
        }

        return $next($request);
    }
}

