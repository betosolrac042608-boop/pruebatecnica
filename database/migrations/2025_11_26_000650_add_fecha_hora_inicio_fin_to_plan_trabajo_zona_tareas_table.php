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
        Schema::table('plan_trabajo_zona_tareas', function (Blueprint $table) {
            $table->dateTime('fecha_hora_inicio')->nullable()->after('estado');
            $table->dateTime('fecha_hora_fin')->nullable()->after('fecha_hora_inicio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_trabajo_zona_tareas', function (Blueprint $table) {
            $table->dropColumn(['fecha_hora_inicio', 'fecha_hora_fin']);
        });
    }
};
