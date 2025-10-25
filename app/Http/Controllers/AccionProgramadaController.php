<?php

namespace App\Http\Controllers;

use App\Models\AccionProgramada;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AccionProgramadaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = AccionProgramada::with(['usuario', 'tipoAccion', 'entidad']);

        // Filtros opcionales
        if ($request->has('completed')) {
            $query->where('completed', $request->boolean('completed'));
        }

        if ($request->has('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->has('tipo_accion_id')) {
            $query->where('tipo_accion_id', $request->tipo_accion_id);
        }

        $acciones = $query->orderBy('fecha_programada', 'asc')
            ->paginate(15);
        
        return response()->json($acciones);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tipo_accion_id' => 'required|exists:tipos_accion,id',
            'nombre_accion' => 'required|string|max:100',
            'fecha_programada' => 'required|date|after_or_equal:today',
            'hora_programada' => 'required',
            'notas' => 'nullable|string',
            'usuario_id' => 'required|exists:users,id',
            'entidad_type' => 'required|string',
            'entidad_id' => 'required|integer',
        ]);

        $accionProgramada = AccionProgramada::create($validated);

        return response()->json([
            'message' => 'Acción programada creada exitosamente',
            'data' => $accionProgramada->load(['usuario', 'tipoAccion', 'entidad'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AccionProgramada $accionProgramada): JsonResponse
    {
        $accionProgramada->load(['usuario', 'tipoAccion', 'entidad']);
        
        return response()->json($accionProgramada);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AccionProgramada $accionProgramada): JsonResponse
    {
        $validated = $request->validate([
            'tipo_accion_id' => 'sometimes|required|exists:tipos_accion,id',
            'nombre_accion' => 'sometimes|required|string|max:100',
            'fecha_programada' => 'sometimes|required|date',
            'hora_programada' => 'sometimes|required',
            'notas' => 'nullable|string',
            'usuario_id' => 'sometimes|required|exists:users,id',
            'completed' => 'sometimes|boolean',
            'entidad_type' => 'sometimes|required|string',
            'entidad_id' => 'sometimes|required|integer',
        ]);

        // Si se marca como completada, registrar la fecha
        if (isset($validated['completed']) && $validated['completed'] && !$accionProgramada->completed) {
            $validated['completed_at'] = now();
        }

        $accionProgramada->update($validated);

        return response()->json([
            'message' => 'Acción programada actualizada exitosamente',
            'data' => $accionProgramada->load(['usuario', 'tipoAccion', 'entidad'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccionProgramada $accionProgramada): JsonResponse
    {
        $accionProgramada->delete();

        return response()->json([
            'message' => 'Acción programada eliminada exitosamente'
        ]);
    }

    /**
     * Marcar una acción programada como completada.
     */
    public function complete(AccionProgramada $accionProgramada): JsonResponse
    {
        $accionProgramada->update([
            'completed' => true,
            'completed_at' => now()
        ]);

        return response()->json([
            'message' => 'Acción marcada como completada',
            'data' => $accionProgramada
        ]);
    }
}

