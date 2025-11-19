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
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')
                  ->constrained('course_sections');
            $table->foreignId('student_id')
                  ->constrained('students');
            $table->foreignId('teacher_id')
                  ->constrained('teachers');
            $table->date('date');
            $table->time('check_in_time')->nullable();
            $table->enum('status', ['present', 'late', 'absent', 'excused'])
                  ->default('absent');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
 
            // Índice único compuesto
            $table->unique(['course_section_id', 'student_id', 'date'], 'ux_student_attendance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
    }
};
