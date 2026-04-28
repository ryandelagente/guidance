<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('counseling_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('overall_rating');     // 1-5
            $table->unsignedTinyInteger('helpful_score');      // 1-5
            $table->unsignedTinyInteger('listened_score');     // 1-5
            $table->unsignedTinyInteger('comfort_score');      // 1-5
            $table->boolean('would_recommend')->default(true);
            $table->boolean('issue_resolved')->default(false);
            $table->text('what_worked')->nullable();
            $table->text('what_could_improve')->nullable();
            $table->timestamps();

            $table->unique('counseling_session_id'); // one survey per session
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_feedback');
    }
};
