<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('action_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('counselor_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->enum('focus_area', ['academic','mental_health','behavioral','career','social','financial','other'])->default('other');
            $table->enum('status', ['draft','active','on_hold','completed','cancelled'])->default('active');
            $table->date('start_date');
            $table->date('target_date')->nullable();
            $table->date('completed_at')->nullable();
            $table->text('outcome_notes')->nullable();
            $table->timestamps();

            $table->index(['student_profile_id', 'status']);
            $table->index(['counselor_id', 'status']);
        });

        Schema::create('action_plan_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_plan_id')->constrained()->cascadeOnDelete();
            $table->string('description', 300);
            $table->date('target_date')->nullable();
            $table->date('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('action_plan_milestones');
        Schema::dropIfExists('action_plans');
    }
};
