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
        Schema::create('guardian_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guardian_id')
                  ->constrained('guardians')
                  ->onDelete('cascade');
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->onDelete('cascade');
            $table->enum('relationship', ['father', 'mother', 'guardian', 'other'])
                  ->default('guardian');
            $table->boolean('is_primary')->default(false);
            $table->timestamp('created_at')->useCurrent();

            // Índice único compuesto
            $table->unique(['guardian_id', 'student_id'], 'ux_guardian_student');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardian_student');
    }
};
