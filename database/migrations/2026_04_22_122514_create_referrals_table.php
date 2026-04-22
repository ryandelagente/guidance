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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('referred_by')->constrained('users')->cascadeOnDelete();       // faculty
            $table->foreignId('assigned_counselor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('reason_category', [
                'academic', 'behavioral', 'attendance', 'personal', 'mental_health', 'financial', 'other'
            ]);
            $table->enum('urgency', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->text('description');                          // faculty's detailed account
            $table->enum('status', [
                'pending', 'acknowledged', 'in_progress', 'resolved', 'closed'
            ])->default('pending');
            // Visible to faculty — what counselor can share without exposing confidential details
            $table->text('faculty_feedback')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
