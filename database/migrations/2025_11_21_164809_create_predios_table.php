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
        Schema::create('predios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150)->comment('Nombre del predio o propiedad');
            $table->string('codigo', 50)->unique()->comment('Código único de identificación');
            $table->text('direccion')->nullable()->comment('Dirección del predio');
            $table->decimal('area_total', 10, 2)->nullable()->comment('Área total en hectáreas');
            $table->text('descripcion')->nullable()->comment('Descripción del predio');
            $table->foreignId('responsable_id')->nullable()->constrained('users')->comment('Usuario responsable del predio');
            $table->boolean('activo')->default(true)->comment('Indica si el predio está activo');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predios');
    }
};
