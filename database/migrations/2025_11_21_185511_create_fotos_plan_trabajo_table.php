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
        Schema::create('fotos_plan_trabajo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_trabajo_id')->constrained('plan_trabajo_diarios')->cascadeOnDelete();
            $table->foreignId('zona_id')->constrained('zonas')->cascadeOnDelete();
            $table->enum('tipo', ['antes', 'despues'])->comment('Foto tomada antes o después de ejecutar tareas');
            $table->string('ruta')->comment('Ruta/URL del archivo almacenado');
            $table->text('metadata')->nullable()->comment('Metadatos de la imagen (exif, tags, etc.)');
            $table->dateTime('tomada_en')->comment('Fecha y hora en que se tomó la foto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fotos_plan_trabajo');
    }
};
