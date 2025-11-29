<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlanTrabajoDiario;
use App\Models\Zona;
use App\Services\PlanTrabajoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanTrabajoController extends Controller
{
    public function planInicial(PlanTrabajoService $service, Request $request)
    {
        $request->validate([
            'predio_id' => 'required|exists:predios,id',
            'fecha' => 'required|date',
            'rol_encargado' => 'required|string',
            'turno_inicio' => 'required|date_format:H:i',
            'turno_fin' => 'required|date_format:H:i',
            'comida_inicio' => 'required|date_format:H:i',
            'comida_fin' => 'required|date_format:H:i',
        ]);

        $plan = PlanTrabajoDiario::create([
            'predio_id' => $request->predio_id,
            'usuario_id' => Auth::id(),
            'fecha' => $request->fecha,
            'estado' => 'pendiente',
            'rol_encargado' => $request->rol_encargado,
            'turno_inicio' => $request->turno_inicio,
            'turno_fin' => $request->turno_fin,
            'comida_inicio' => $request->comida_inicio,
            'comida_fin' => $request->comida_fin,
        ]);

        $respuesta = $service->generarPlan($plan);

        return response()->json($respuesta);
    }

    public function subirFoto(Request $request)
    {
        $request->validate([
            'plan_trabajo_id' => 'required|exists:plan_trabajo_diarios,id',
            'zona_id' => 'required|exists:zonas,id',
            'tipo' => 'required|in:antes,despues',
            'ruta' => 'required|string',
            'tomada_en' => 'required|date',
        ]);

        $plan = PlanTrabajoDiario::findOrFail($request->plan_trabajo_id);
        $zona = Zona::findOrFail($request->zona_id);

        $plan->fotos()->create([
            'zona_id' => $zona->id,
            'tipo' => $request->tipo,
            'ruta' => $request->ruta,
            'metadata' => $request->metadata ?? null,
            'tomada_en' => $request->tomada_en,
        ]);

        return response()->json(['ok' => true]);
    }

    public function evaluarPlan(Request $request, PlanTrabajoService $service)
    {
        $request->validate([
            'plan_trabajo_id' => 'required|exists:plan_trabajo_diarios,id',
            'zona_id' => 'required|exists:zonas,id',
            'calificacion' => 'required|string',
        ]);

        $plan = PlanTrabajoDiario::findOrFail($request->plan_trabajo_id);
        $zona = Zona::findOrFail($request->zona_id);

        $evaluacion = $service->registrarEvaluacion($plan, $zona, $request->only([
            'tarea_zona_id',
            'resultados',
            'calificacion',
            'comentarios',
        ]));

        return response()->json($evaluacion);
    }
}

