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
        Schema::create('plan_trabajo_zona_tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_trabajo_id')->constrained('plan_trabajo_diarios')->cascadeOnDelete();
            $table->foreignId('zona_id')->constrained('zonas')->cascadeOnDelete();
            $table->foreignId('tarea_zona_id')->nullable()->constrained('tareas_zonas')->nullOnDelete();
            $table->string('descripcion')->nullable()->comment('Descripción detectada por GPT o ajustada manualmente');
            $table->string('estado', 50)->default('pendiente')->comment('pendiente, en_progreso, completado');
            $table->text('comentarios')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_trabajo_zona_tareas');
    }
};
