<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clearance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('clearance_type', ['graduation','departmental','scholarship','employment','other']);
            $table->string('academic_year', 20);         // e.g. "2025-2026"
            $table->enum('semester', ['1st','2nd','Summer']);
            $table->string('purpose')->nullable();
            $table->enum('status', ['pending','for_exit_survey','survey_done','approved','rejected','on_hold'])
                  ->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clearance_requests');
    }
};
