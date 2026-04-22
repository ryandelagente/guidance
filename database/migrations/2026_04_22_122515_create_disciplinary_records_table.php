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
        Schema::create('disciplinary_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reported_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('offense_type', ['minor', 'major']);
            $table->enum('offense_category', [
                'tardiness', 'absences', 'misconduct', 'cheating',
                'property_damage', 'harassment', 'substance', 'other'
            ]);
            $table->date('incident_date');
            $table->text('description');
            $table->text('action_taken')->nullable();
            $table->enum('status', ['pending','under_review','resolved','escalated'])->default('pending');
            $table->string('sanction')->nullable();       // e.g. "Written Warning", "Suspension"
            $table->date('sanction_end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disciplinary_records');
    }
};
