<?php

namespace App\Http\Controllers;

use App\Models\Herramienta;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HerramientaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $herramientas = Herramienta::with(['responsable', 'actividades', 'accionesProgramadas'])
            ->paginate(15);
        
        return response()->json($herramientas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'matricula' => 'required|string|max:50|unique:herramientas,matricula',
            'nombre' => 'required|string|max:100',
            'tipo' => 'required|string|max:50',
            'marca' => 'nullable|string|max:50',
            'modelo' => 'nullable|string|max:50',
            'numero_serie' => 'nullable|string|max:100|unique:herramientas,numero_serie',
            'estado' => 'required|string|max:50',
            'ubicacion' => 'nullable|string|max:100',
            'fecha_adquisicion' => 'required|date',
            'valor' => 'nullable|numeric|min:0',
            'responsable_id' => 'nullable|exists:users,id',
            'ultimo_mantenimiento' => 'nullable|date',
            'proximo_mantenimiento' => 'nullable|date|after:ultimo_mantenimiento',
            'observaciones' => 'nullable|string',
        ]);

        $herramienta = Herramienta::create($validated);

        return response()->json([
            'message' => 'Herramienta creada exitosamente',
            'data' => $herramienta
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Herramienta $herramienta): JsonResponse
    {
        $herramienta->load(['responsable', 'actividades', 'accionesProgramadas']);
        
        return response()->json($herramienta);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Herramienta $herramienta): JsonResponse
    {
        $validated = $request->validate([
            'matricula' => 'sometimes|required|string|max:50|unique:herramientas,matricula,' . $herramienta->id,
            'nombre' => 'sometimes|required|string|max:100',
            'tipo' => 'sometimes|required|string|max:50',
            'marca' => 'nullable|string|max:50',
            'modelo' => 'nullable|string|max:50',
            'numero_serie' => 'nullable|string|max:100|unique:herramientas,numero_serie,' . $herramienta->id,
            'estado' => 'sometimes|required|string|max:50',
            'ubicacion' => 'nullable|string|max:100',
            'fecha_adquisicion' => 'sometimes|required|date',
            'valor' => 'nullable|numeric|min:0',
            'responsable_id' => 'nullable|exists:users,id',
            'ultimo_mantenimiento' => 'nullable|date',
            'proximo_mantenimiento' => 'nullable|date|after:ultimo_mantenimiento',
            'observaciones' => 'nullable|string',
        ]);

        $herramienta->update($validated);

        return response()->json([
            'message' => 'Herramienta actualizada exitosamente',
            'data' => $herramienta
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Herramienta $herramienta): JsonResponse
    {
        $herramienta->delete();

        return response()->json([
            'message' => 'Herramienta eliminada exitosamente'
        ]);
    }
}

