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
        Schema::create('acciones_programadas', function (Blueprint $table) {
            $table->string('id', 100)->primary();
            $table->morphs('entidad');
            $table->foreignId('tipo_accion_id')->nullable()->constrained('tipos_accion');
            $table->string('nombre_accion', 150)->nullable();
            $table->date('fecha_programada');
            $table->time('hora_programada')->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users');
            $table->boolean('completed')->default(false);
            $table->dateTime('completed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acciones_programadas');
    }
};
