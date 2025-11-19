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
        Schema::create('guardian_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')
                  ->constrained('student_attendances');
            $table->foreignId('guardian_id')
                  ->constrained('guardians');
            $table->enum('type', ['late', 'absent', 'excused']);
            $table->enum('method', ['email', 'sms', 'whatsapp'])
                  ->default('email');
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed'])
                  ->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardian_notifications');
    }
};
