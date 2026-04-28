<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anonymous_concerns', function (Blueprint $table) {
            $table->id();
            $table->string('reference_code', 16)->unique();   // for the reporter to follow up
            $table->enum('concern_type', [
                'bullying','mental_health','self_harm','abuse',
                'substance','academic_dishonesty','harassment','safety','other'
            ]);
            $table->enum('urgency', ['low','medium','high','critical'])->default('medium');
            $table->text('description');
            $table->string('about_who', 200)->nullable();      // who is the concern about (free text — could be name/role/desc)
            $table->string('location', 200)->nullable();
            $table->string('reporter_relationship', 80)->nullable();   // student/faculty/parent/anonymous
            $table->string('contact_email')->nullable();        // optional — reporter may share email
            $table->enum('status', ['new','reviewing','action_taken','resolved','dismissed'])->default('new');
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('staff_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['status', 'urgency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anonymous_concerns');
    }
};
