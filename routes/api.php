<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnimalController;
use App\Http\Controllers\CultivoController;
use App\Http\Controllers\HerramientaController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\AccionProgramadaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas públicas de la API (puedes agregar middleware auth:sanctum si necesitas autenticación)
Route::prefix('v1')->group(function () {
    // Gestión de Animales
    Route::apiResource('animales', AnimalController::class);
    
    // Gestión de Cultivos
    Route::apiResource('cultivos', CultivoController::class);
    
    // Gestión de Herramientas
    Route::apiResource('herramientas', HerramientaController::class);
    
    // Gestión de Actividades
    Route::apiResource('actividades', ActividadController::class);
    
    // Gestión de Acciones Programadas
    Route::apiResource('acciones-programadas', AccionProgramadaController::class);
    Route::post('acciones-programadas/{accionProgramada}/complete', [AccionProgramadaController::class, 'complete'])
        ->name('acciones-programadas.complete');
});

// Rutas protegidas con autenticación Sanctum
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Aquí puedes agregar rutas que requieran autenticación
    // Por ejemplo: estadísticas, reportes, etc.
});
