<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AnimalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $animales = Animal::with(['actividades', 'accionesProgramadas'])
            ->paginate(15);
        
        return response()->json($animales);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'matricula' => 'required|string|max:50|unique:animales,matricula',
            'nombre' => 'required|string|max:100',
            'especie' => 'required|string|max:50',
            'raza' => 'nullable|string|max:50',
            'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'fecha_adquisicion' => 'required|date',
            'sexo' => 'required|in:M,H',
            'peso' => 'nullable|numeric|min:0',
            'estado' => 'required|string|max:50',
            'ubicacion' => 'nullable|string|max:100',
            'ultima_revision' => 'nullable|date',
            'ultima_vacuna' => 'nullable|date',
            'observaciones' => 'nullable|string',
        ]);

        $animal = Animal::create($validated);

        return response()->json([
            'message' => 'Animal creado exitosamente',
            'data' => $animal
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Animal $animal): JsonResponse
    {
        $animal->load(['actividades', 'accionesProgramadas']);
        
        return response()->json($animal);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Animal $animal): JsonResponse
    {
        $validated = $request->validate([
            'matricula' => 'sometimes|required|string|max:50|unique:animales,matricula,' . $animal->id,
            'nombre' => 'sometimes|required|string|max:100',
            'especie' => 'sometimes|required|string|max:50',
            'raza' => 'nullable|string|max:50',
            'fecha_nacimiento' => 'sometimes|required|date|before_or_equal:today',
            'fecha_adquisicion' => 'sometimes|required|date',
            'sexo' => 'sometimes|required|in:M,H',
            'peso' => 'nullable|numeric|min:0',
            'estado' => 'sometimes|required|string|max:50',
            'ubicacion' => 'nullable|string|max:100',
            'ultima_revision' => 'nullable|date',
            'ultima_vacuna' => 'nullable|date',
            'observaciones' => 'nullable|string',
        ]);

        $animal->update($validated);

        return response()->json([
            'message' => 'Animal actualizado exitosamente',
            'data' => $animal
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Animal $animal): JsonResponse
    {
        $animal->delete();

        return response()->json([
            'message' => 'Animal eliminado exitosamente'
        ]);
    }
}

