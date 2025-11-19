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
        Schema::create('course_section_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')
                  ->constrained('course_sections')
                  ->onDelete('cascade');
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->onDelete('cascade');
            $table->enum('status', ['active', 'dropped'])
                  ->default('active');
            $table->timestamp('created_at')->useCurrent();

            // Índice único compuesto
            $table->unique(['course_section_id', 'student_id'], 'ux_course_section_student');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_section_student');
    }
};
