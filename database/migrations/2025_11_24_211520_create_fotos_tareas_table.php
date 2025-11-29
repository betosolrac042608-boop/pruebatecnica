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
        Schema::create('fotos_tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_trabajo_zona_tarea_id')->constrained('plan_trabajo_zona_tareas')->cascadeOnDelete();
            $table->enum('tipo', ['antes', 'despues'])->comment('Tipo de foto: antes o después de la tarea');
            $table->string('ruta')->comment('Ruta del archivo de la imagen');
            $table->text('evaluacion_gpt')->nullable()->comment('Evaluación de GPT sobre la imagen');
            $table->json('metadata_gpt')->nullable()->comment('Metadatos adicionales de la evaluación');
            $table->string('calificacion')->nullable()->comment('Calificación de la evaluación (aprobado, rechazado, revisar)');
            $table->timestamp('tomada_en')->useCurrent()->comment('Fecha y hora en que se tomó la foto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fotos_tareas');
    }
};
