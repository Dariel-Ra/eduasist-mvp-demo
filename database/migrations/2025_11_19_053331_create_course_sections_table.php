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
        Schema::create('course_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')
                  ->constrained('courses');
            $table->foreignId('teacher_id')
                  ->constrained('teachers');
            $table->string('section', 50)->nullable();
            $table->string('classroom', 50)->nullable();
            $table->integer('max_students')->nullable();

            // Campos de horario
            $table->set('schedule_days', [
                'monday', 
                'tuesday', 
                'wednesday', 
                'thursday', 
                'friday'
            ])->comment('Días de la semana en que se imparte la clase');
            $table->time('start_time')
                  ->comment('Hora de inicio de la clase');
            $table->time('end_time')
                  ->comment('Hora de fin de la clase');

            $table->boolean('active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_sections');
    }
};
