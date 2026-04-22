<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('psychological_test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_schedule_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('raw_score')->nullable();
            $table->decimal('percentile', 5, 2)->nullable();
            $table->string('grade_equivalent')->nullable();
            $table->enum('interpretation_level', ['very_low','low','average','above_average','superior','very_superior'])->nullable();
            $table->text('interpretation')->nullable();
            $table->json('career_matches')->nullable();
            $table->date('test_date');
            $table->boolean('is_released')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_results');
    }
};
