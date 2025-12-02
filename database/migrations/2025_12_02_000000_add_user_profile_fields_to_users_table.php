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
            // Agregar nuevos campos de perfil
            $table->string('first_name', 125)->after('id');
            $table->string('last_name', 125)->after('first_name');
            $table->string('phone', 20)->nullable()->after('email');
            $table->enum('role', ['sysadmin', 'admin', 'teacher', 'guardian'])->after('phone');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('role');

            // Hacer el campo 'name' nullable para compatibilidad con código existente
            // Puede ser eliminado en una migración futura si ya no se necesita
            $table->string('name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'phone',
                'role',
                'status',
            ]);

            // Revertir el campo 'name' a NOT NULL
            $table->string('name')->nullable(false)->change();
        });
    }
};
