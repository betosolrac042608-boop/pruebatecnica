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
        Schema::create('zonas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('predio_id')->constrained('predios')->onDelete('cascade')->comment('Predio al que pertenece la zona');
            $table->string('nombre', 150)->comment('Nombre de la zona');
            $table->string('codigo', 50)->comment('Código de identificación de la zona');
            $table->text('descripcion')->nullable()->comment('Descripción de la zona');
            $table->text('ubicacion')->nullable()->comment('Ubicación específica dentro del predio');
            $table->boolean('activo')->default(true)->comment('Indica si la zona está activa');
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['predio_id', 'codigo'], 'zonas_predio_codigo_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zonas');
    }
};
