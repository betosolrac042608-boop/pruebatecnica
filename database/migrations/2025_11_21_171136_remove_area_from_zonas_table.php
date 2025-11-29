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
        Schema::table('zonas', function (Blueprint $table) {
            if (Schema::hasColumn('zonas', 'area')) {
                $table->dropColumn('area');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zonas', function (Blueprint $table) {
            if (! Schema::hasColumn('zonas', 'area')) {
                $table->decimal('area', 10, 2)->nullable()->comment('Área de la zona en hectáreas');
            }
        });
    }
};
