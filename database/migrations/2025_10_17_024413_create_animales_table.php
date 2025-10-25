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
        Schema::create('animales', function (Blueprint $table) {
            $table->id();
            $table->string('matricula', 20)->unique()->comment('Código de identificación del animal');
            $table->string('nombre', 150)->nullable()->comment('Nombre o identificador del animal');
            $table->string('especie', 100)->comment('Especie del animal');
            $table->string('raza', 100)->nullable()->comment('Raza específica del animal');
            $table->date('fecha_nacimiento')->comment('Fecha de nacimiento');
            $table->date('fecha_adquisicion')->nullable()->comment('Fecha de adquisición si fue comprado');
            $table->enum('sexo', ['M', 'H'])->comment('M=Macho, H=Hembra');
            $table->decimal('peso', 6, 2)->nullable()->comment('Peso en kg');
            $table->string('estado', 50)->comment('Estado actual: Activo, Vendido, etc.');
            $table->string('ubicacion', 255)->nullable()->comment('Ubicación o corral');
            $table->date('ultima_revision')->nullable()->comment('Última revisión veterinaria');
            $table->date('ultima_vacuna')->nullable()->comment('Última vacunación');
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
        Schema::dropIfExists('animales');
    }
};
