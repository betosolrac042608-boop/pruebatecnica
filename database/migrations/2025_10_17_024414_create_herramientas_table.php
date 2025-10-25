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
        Schema::create('herramientas', function (Blueprint $table) {
            $table->id();
            $table->string('matricula', 20)->unique()->comment('Código de identificación de la herramienta');
            $table->string('nombre', 150)->comment('Nombre de la herramienta');
            $table->string('tipo', 50)->comment('Tipo: Manual, Eléctrica, Maquinaria, etc.');
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->string('numero_serie', 100)->nullable();
            $table->string('estado', 50)->comment('Estado: Operativa, En reparación, etc.');
            $table->string('ubicacion', 255)->nullable()->comment('Ubicación o almacén');
            $table->date('fecha_adquisicion');
            $table->decimal('valor', 10, 2)->nullable();
            $table->foreignId('responsable_id')->nullable()->constrained('users')->comment('Usuario responsable de la herramienta');
            $table->date('ultimo_mantenimiento')->nullable();
            $table->date('proximo_mantenimiento')->nullable();
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
        Schema::dropIfExists('herramientas');
    }
};
