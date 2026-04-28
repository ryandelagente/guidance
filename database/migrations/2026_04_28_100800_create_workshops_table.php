<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('description');
            $table->enum('category', ['mental_health','academic','career','life_skills','wellness','seminar','other'])->default('other');
            $table->string('venue', 200);
            $table->enum('mode', ['in_person', 'virtual', 'hybrid'])->default('in_person');
            $table->string('meeting_link', 500)->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->unsignedSmallInteger('capacity')->nullable();   // null = unlimited
            $table->dateTime('rsvp_deadline')->nullable();
            $table->enum('audience', ['all','students','staff','faculty'])->default('all');
            $table->enum('status', ['draft','published','cancelled','completed'])->default('published');
            $table->string('cover_color', 20)->default('blue'); // theme accent
            $table->timestamps();

            $table->index(['status', 'starts_at']);
        });

        Schema::create('workshop_rsvps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['registered','attended','cancelled','no_show'])->default('registered');
            $table->timestamp('attended_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['workshop_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop_rsvps');
        Schema::dropIfExists('workshops');
    }
};
