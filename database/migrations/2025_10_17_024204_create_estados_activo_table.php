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
        Schema::create('estados_activo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->foreignId('tipo_activo_id')->constrained('tipos_activo')->onDelete('cascade');
            $table->unique(['nombre', 'tipo_activo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estados_activo');
    }
};
