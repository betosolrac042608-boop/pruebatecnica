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
        Schema::create('evaluaciones_plan_trabajo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_trabajo_id')->constrained('plan_trabajo_diarios')->cascadeOnDelete();
            $table->foreignId('zona_id')->constrained('zonas')->cascadeOnDelete();
            $table->foreignId('tarea_zona_id')->nullable()->constrained('tareas_zonas')->nullOnDelete();
            $table->json('resultados')->nullable()->comment('JSON con la evaluación entregada por IA o responsable');
            $table->string('calificacion', 50)->nullable()->comment('Ej: excelente, requiere_mejora');
            $table->text('comentarios')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_plan_trabajo');
    }
};
