<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wellness_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('mood');             // 1=Very Bad … 5=Very Good
            $table->unsignedTinyInteger('stress_level');     // 1=None … 5=Overwhelming
            $table->unsignedTinyInteger('sleep_quality');    // 1=Poor … 5=Excellent
            $table->unsignedTinyInteger('academic_stress');  // 1=None … 5=Severe
            $table->text('notes')->nullable();
            $table->boolean('wants_counselor')->default(false);
            $table->boolean('reviewed')->default(false);
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['student_profile_id', 'created_at']);
            $table->index('wants_counselor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wellness_checkins');
    }
};
