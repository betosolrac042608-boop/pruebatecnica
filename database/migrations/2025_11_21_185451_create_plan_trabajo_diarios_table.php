<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plan_trabajo_diarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('predio_id')->constrained('predios')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->date('fecha')->comment('Fecha del ingreso y plan diario');
            $table->string('estado', 50)->default('pendiente')->comment('pendiente, en_progreso, completado');
            $table->json('datos_gpt')->nullable()->comment('JSON recibido de ChatGPT con hallazgos / tareas detectadas');
            $table->string('rol_encargado', 50)->nullable()->comment('Rol del responsable (Eduardo, Supervisor, etc.)');
            $table->time('turno_inicio')->nullable()->comment('Hora de inicio del turno');
            $table->time('turno_fin')->nullable()->comment('Hora de fin del turno');
            $table->time('comida_inicio')->nullable()->comment('Hora de entrada a comida');
            $table->time('comida_fin')->nullable()->comment('Hora de salida de comida');
            $table->timestamps();
            $table->unique(['predio_id', 'fecha'], 'plan_trabajo_unico_por_predio_fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_trabajo_diarios');
    }
};
