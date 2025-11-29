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
        Schema::table('plan_trabajo_diarios', function (Blueprint $table) {
            $table->text('resumen_ia')->nullable()->after('datos_gpt')->comment('Resumen o mensaje de texto de la IA');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_trabajo_diarios', function (Blueprint $table) {
            $table->dropColumn('resumen_ia');
        });
    }
};
