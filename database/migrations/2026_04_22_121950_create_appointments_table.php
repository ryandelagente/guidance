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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('counselor_id')->constrained('users')->cascadeOnDelete();
            $table->enum('appointment_type', ['academic', 'personal_social', 'career', 'crisis']);
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', [
                'pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'
            ])->default('pending');
            $table->enum('meeting_type', ['in_person', 'virtual'])->default('in_person');
            $table->string('meeting_link')->nullable();    // Google Meet / Zoom URL
            $table->text('student_concern')->nullable();  // Student's stated reason
            $table->text('notes_for_student')->nullable();// Counselor's visible notes
            $table->string('cancelled_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
