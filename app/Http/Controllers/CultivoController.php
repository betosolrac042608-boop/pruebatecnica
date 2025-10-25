<?php

namespace App\Http\Controllers;

use App\Models\Cultivo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CultivoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $cultivos = Cultivo::with(['actividades', 'accionesProgramadas'])
            ->paginate(15);
        
        return response()->json($cultivos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'matricula' => 'required|string|max:50|unique:cultivos,matricula',
            'nombre' => 'required|string|max:100',
            'tipo' => 'required|string|max:50',
            'especie' => 'required|string|max:50',
            'area' => 'required|numeric|min:0',
            'estado' => 'required|string|max:50',
            'ubicacion' => 'required|string|max:100',
            'fecha_siembra' => 'required|date',
            'fecha_estimada_cosecha' => 'nullable|date|after:fecha_siembra',
            'ultima_fertilizacion' => 'nullable|date',
            'ultimo_riego' => 'nullable|date',
            'observaciones' => 'nullable|string',
        ]);

        $cultivo = Cultivo::create($validated);

        return response()->json([
            'message' => 'Cultivo creado exitosamente',
            'data' => $cultivo
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cultivo $cultivo): JsonResponse
    {
        $cultivo->load(['actividades', 'accionesProgramadas']);
        
        return response()->json($cultivo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cultivo $cultivo): JsonResponse
    {
        $validated = $request->validate([
            'matricula' => 'sometimes|required|string|max:50|unique:cultivos,matricula,' . $cultivo->id,
            'nombre' => 'sometimes|required|string|max:100',
            'tipo' => 'sometimes|required|string|max:50',
            'especie' => 'sometimes|required|string|max:50',
            'area' => 'sometimes|required|numeric|min:0',
            'estado' => 'sometimes|required|string|max:50',
            'ubicacion' => 'sometimes|required|string|max:100',
            'fecha_siembra' => 'sometimes|required|date',
            'fecha_estimada_cosecha' => 'nullable|date|after:fecha_siembra',
            'ultima_fertilizacion' => 'nullable|date',
            'ultimo_riego' => 'nullable|date',
            'observaciones' => 'nullable|string',
        ]);

        $cultivo->update($validated);

        return response()->json([
            'message' => 'Cultivo actualizado exitosamente',
            'data' => $cultivo
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cultivo $cultivo): JsonResponse
    {
        $cultivo->delete();

        return response()->json([
            'message' => 'Cultivo eliminado exitosamente'
        ]);
    }
}

