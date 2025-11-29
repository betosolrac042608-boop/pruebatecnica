<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        
        // Si es Operador o Supervisor, redirigir a su página principal
        if ($user && $user->rol_id) {
            if (!$user->relationLoaded('rol')) {
                $user->load('rol');
            }
            
            if ($user->rol && in_array($user->rol->nombre, ['Operador', 'Supervisor'])) {
                return redirect('/admin/plan-trabajo-operario');
            }
        }
        
        return redirect('/admin');
    }
    return redirect('/admin/login');
});

Route::get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');
