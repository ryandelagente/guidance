<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('counselor_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->enum('focus', ['anxiety','depression','peer_support','academic_stress','social_skills','grief','substance','other'])->default('other');
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('venue', 200);
            $table->unsignedSmallInteger('max_participants')->default(15);
            $table->enum('status', ['scheduled','in_progress','completed','cancelled'])->default('scheduled');
            $table->text('group_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('group_session_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            $table->enum('attendance', ['registered','attended','no_show','withdrew'])->default('registered');
            $table->timestamps();

            $table->unique(['group_session_id', 'student_profile_id'], 'gs_participants_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_session_participants');
        Schema::dropIfExists('group_sessions');
    }
};
