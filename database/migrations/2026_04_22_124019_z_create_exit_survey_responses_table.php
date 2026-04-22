<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exit_survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clearance_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exit_survey_question_id')->constrained()->cascadeOnDelete();
            $table->text('response')->nullable();
            $table->timestamps();

            $table->unique(['clearance_request_id', 'exit_survey_question_id'], 'esr_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exit_survey_responses');
    }
};
