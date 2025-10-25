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
        Schema::create('cultivos', function (Blueprint $table) {
            $table->id();
            $table->string('matricula', 20)->unique()->comment('Código de identificación del cultivo');
            $table->string('nombre', 150)->comment('Nombre identificativo del cultivo o parcela');
            $table->string('tipo', 50)->comment('Tipo: Cereal, Frutal, Hortaliza, etc.');
            $table->string('especie', 100)->comment('Especie cultivada');
            $table->decimal('area', 10, 2)->comment('Área en metros cuadrados');
            $table->string('ubicacion', 255)->comment('Ubicación o sector');
            $table->date('fecha_siembra')->comment('Fecha de siembra');
            $table->string('estado', 50)->comment('Estado: Crecimiento, Cosecha, etc.');
            $table->date('ultima_fertilizacion')->nullable();
            $table->date('ultimo_riego')->nullable();
            $table->date('fecha_estimada_cosecha')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cultivos');
    }
};
