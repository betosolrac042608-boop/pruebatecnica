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
        Schema::create('tareas_zonas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('predio_id')->constrained('predios')->cascadeOnDelete();
            $table->foreignId('zona_id')->constrained('zonas')->cascadeOnDelete();
            $table->string('clave', 50);
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->text('objetivo')->nullable();
            $table->text('tareas_sugeridas')->nullable();
            $table->string('frecuencia', 150)->nullable();
            $table->integer('tiempo_minutos')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tareas_zonas');
    }
};
