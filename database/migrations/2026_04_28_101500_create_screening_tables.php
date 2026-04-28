<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('screening_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            $table->enum('instrument', ['phq9', 'gad7', 'k10'])->index();
            $table->json('answers');                         // {q1: 0-3, q2: 0-3, ...}
            $table->unsignedSmallInteger('total_score');
            $table->string('severity', 60);                  // minimal | mild | moderate | severe
            $table->boolean('positive_self_harm')->default(false); // PHQ-9 Q9 > 0
            $table->boolean('reviewed')->default(false);
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('counselor_notes')->nullable();
            $table->timestamps();

            $table->index(['student_profile_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('screening_responses');
    }
};
