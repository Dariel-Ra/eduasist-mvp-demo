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
            // Drop the old 'name' column
            $table->dropColumn('name');

            // Add new columns
            $table->string('first_name', 125)->after('id');
            $table->string('last_name', 125)->after('first_name');
            $table->string('nickname', 255)->after('last_name');
            $table->string('phone', 20)->nullable()->after('password');
            $table->enum('role', ['admin', 'teacher', 'parent'])->after('phone');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add back the 'name' column
            $table->string('name')->after('id');

            // Drop the new columns
            $table->dropColumn([
                'first_name',
                'last_name',
                'nickname',
                'phone',
                'role',
                'status',
            ]);
        });
    }
};
