<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('good_moral_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('issued_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('clearance_request_id')->nullable()->constrained()->nullOnDelete();
            $table->string('certificate_number')->unique();    // e.g. GMC-2026-00001
            $table->string('purpose');
            $table->tinyInteger('validity_months')->default(6);
            $table->timestamp('issued_at');
            $table->boolean('is_revoked')->default(false);
            $table->string('revoked_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('good_moral_certificates');
    }
};
