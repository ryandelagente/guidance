<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('psychological_test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('administered_by')->constrained('users')->cascadeOnDelete();
            $table->string('college')->nullable();
            $table->string('program')->nullable();
            $table->enum('year_level', ['1st','2nd','3rd','4th','5th','Graduate'])->nullable();
            $table->date('scheduled_date');
            $table->time('start_time');
            $table->string('venue')->nullable();
            $table->unsignedSmallInteger('expected_participants')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['scheduled','ongoing','completed','cancelled'])->default('scheduled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_schedules');
    }
};
