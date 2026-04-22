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
        Schema::create('counseling_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('counselor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            // Encrypted case notes — AES-256 via Laravel Crypt facade
            $table->longText('case_notes')->nullable();
            $table->text('recommendations')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->enum('session_status', ['initial','ongoing','terminated','referred'])->default('initial');
            $table->enum('presenting_concern', [
                'academic', 'personal_social', 'career', 'financial',
                'family', 'mental_health', 'behavioral', 'other'
            ])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counseling_sessions');
    }
};
