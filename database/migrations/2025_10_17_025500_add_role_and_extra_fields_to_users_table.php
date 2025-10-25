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
        Schema::table('users', function (Blueprint $table) {
            $table->string('telefono', 30)->nullable();
            $table->string('username', 100)->unique()->nullable();
            $table->foreignId('rol_id')->nullable()->constrained('roles');
            $table->boolean('active')->default(true);
            $table->timestamp('last_login')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['rol_id']);
            $table->dropColumn([
                'telefono',
                'username',
                'rol_id',
                'active',
                'last_login'
            ]);
        });
    }
};
