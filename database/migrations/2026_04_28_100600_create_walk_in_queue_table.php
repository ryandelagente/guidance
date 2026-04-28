<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('walk_in_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->nullable();              // for non-students or guests
            $table->string('contact_number')->nullable();
            $table->string('reason', 200);
            $table->enum('priority', ['normal','urgent','crisis'])->default('normal');
            $table->enum('status', ['waiting','being_seen','completed','no_show','cancelled'])->default('waiting');
            $table->foreignId('assigned_counselor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('arrived_at');
            $table->timestamp('called_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'arrived_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('walk_in_queue');
    }
};
