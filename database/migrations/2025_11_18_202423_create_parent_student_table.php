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
        Schema::create('parent_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')
                  ->constrained('parentsmodel')
                  ->cascadeOnDelete();
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->cascadeOnDelete();
            $table->enum('relationship', ['father', 'mother', 'guardian', 'other'])
                  ->default('guardian');
            $table->boolean('is_primary')->default(false);
            $table->timestamp('created_at')->useCurrent();
 
            // Índice único compuesto
            $table->unique(['parent_id', 'student_id'], 'ux_parent_student');
        });
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_student');
    }
};

