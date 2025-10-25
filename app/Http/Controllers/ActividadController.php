<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ActividadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Actividad::with(['usuario', 'estado', 'tipoAccion', 'entidad']);

        // Filtros opcionales
        if ($request->has('estado_id')) {
            $query->where('estado_id', $request->estado_id);
        }

        if ($request->has('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->has('tipo_accion_id')) {
            $query->where('tipo_accion_id', $request->tipo_accion_id);
        }

        $actividades = $query->orderBy('fecha_programada', 'desc')
            ->paginate(15);
        
        return response()->json($actividades);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tipo_accion_id' => 'required|exists:tipos_accion,id',
            'nombre_accion' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'fecha_programada' => 'required|date',
            'hora_programada' => 'nullable',
            'fecha_realizada' => 'nullable|date',
            'hora_realizada' => 'nullable',
            'usuario_id' => 'required|exists:users,id',
            'estado_id' => 'required|exists:estados_actividad,id',
            'notas' => 'nullable|string',
            'entidad_type' => 'required|string',
            'entidad_id' => 'required|integer',
        ]);

        $actividad = Actividad::create($validated);

        return response()->json([
            'message' => 'Actividad creada exitosamente',
            'data' => $actividad->load(['usuario', 'estado', 'tipoAccion', 'entidad'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Actividad $actividad): JsonResponse
    {
        $actividad->load(['usuario', 'estado', 'tipoAccion', 'entidad']);
        
        return response()->json($actividad);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Actividad $actividad): JsonResponse
    {
        $validated = $request->validate([
            'tipo_accion_id' => 'sometimes|required|exists:tipos_accion,id',
            'nombre_accion' => 'sometimes|required|string|max:100',
            'descripcion' => 'nullable|string',
            'fecha_programada' => 'sometimes|required|date',
            'hora_programada' => 'nullable',
            'fecha_realizada' => 'nullable|date',
            'hora_realizada' => 'nullable',
            'usuario_id' => 'sometimes|required|exists:users,id',
            'estado_id' => 'sometimes|required|exists:estados_actividad,id',
            'notas' => 'nullable|string',
            'entidad_type' => 'sometimes|required|string',
            'entidad_id' => 'sometimes|required|integer',
        ]);

        $actividad->update($validated);

        return response()->json([
            'message' => 'Actividad actualizada exitosamente',
            'data' => $actividad->load(['usuario', 'estado', 'tipoAccion', 'entidad'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Actividad $actividad): JsonResponse
    {
        $actividad->delete();

        return response()->json([
            'message' => 'Actividad eliminada exitosamente'
        ]);
    }
}

