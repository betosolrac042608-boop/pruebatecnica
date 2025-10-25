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
        Schema::create('actividades', function (Blueprint $table) {
            $table->id();
            $table->morphs('entidad');
            $table->foreignId('tipo_accion_id')->constrained('tipos_accion');
            $table->string('nombre_accion', 150)->nullable();
            $table->text('descripcion')->nullable();
            $table->date('fecha_programada')->nullable();
            $table->time('hora_programada')->nullable();
            $table->date('fecha_realizada')->nullable();
            $table->time('hora_realizada')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users');
            $table->foreignId('estado_id')->nullable()->constrained('estados_actividad');
            $table->text('notas')->nullable();
            $table->boolean('from_scheduled')->default(false);
            $table->string('original_scheduled_id', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
