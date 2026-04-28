<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('body');
            $table->enum('audience', ['all', 'students', 'staff', 'counselors', 'faculty'])->default('all');
            $table->enum('priority', ['info', 'warning', 'urgent'])->default('info');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['is_published', 'published_at']);
            $table->index('audience');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
