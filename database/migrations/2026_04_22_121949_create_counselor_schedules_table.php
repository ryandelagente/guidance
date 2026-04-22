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
        Schema::create('counselor_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('counselor_id')->constrained('users')->cascadeOnDelete();
            $table->enum('day_of_week', ['monday','tuesday','wednesday','thursday','friday','saturday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedTinyInteger('slot_duration')->default(60); // minutes per slot
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counselor_schedules');
    }
};
