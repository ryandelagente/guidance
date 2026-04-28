<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riasec_questions', function (Blueprint $table) {
            $table->id();
            $table->string('text', 300);
            $table->enum('type', ['R','I','A','S','E','C'])->index(); // Realistic, Investigative, Artistic, Social, Enterprising, Conventional
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('riasec_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('score_r')->default(0);
            $table->unsignedTinyInteger('score_i')->default(0);
            $table->unsignedTinyInteger('score_a')->default(0);
            $table->unsignedTinyInteger('score_s')->default(0);
            $table->unsignedTinyInteger('score_e')->default(0);
            $table->unsignedTinyInteger('score_c')->default(0);
            $table->string('top_code', 3); // e.g. "SAI"
            $table->json('answers')->nullable(); // {question_id: 1|0}
            $table->timestamp('completed_at');
            $table->timestamps();

            $table->index(['student_profile_id', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riasec_responses');
        Schema::dropIfExists('riasec_questions');
    }
};
