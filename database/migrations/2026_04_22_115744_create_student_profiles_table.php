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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Personal Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('sex', ['male', 'female'])->nullable();
            $table->string('civil_status')->nullable();
            $table->string('religion')->nullable();
            $table->string('nationality')->nullable()->default('Filipino');
            $table->string('contact_number')->nullable();
            $table->text('home_address')->nullable();

            // Academic Information
            $table->string('student_id_number')->nullable()->unique();
            $table->string('college')->nullable();
            $table->string('program')->nullable();
            $table->string('year_level')->nullable();
            $table->enum('student_type', ['regular', 'irregular', 'transferee', 'returnee'])->default('regular');
            $table->string('scholarship')->nullable();
            $table->enum('academic_status', ['good_standing', 'probation', 'at_risk', 'dismissed'])->default('good_standing');

            // Family Background
            $table->string('father_name')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('father_contact')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('mother_contact')->nullable();
            $table->enum('parents_status', ['married', 'separated', 'widowed', 'single_parent', 'deceased'])->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relationship')->nullable();
            $table->string('guardian_contact')->nullable();

            // Socioeconomic
            $table->string('monthly_family_income')->nullable();
            $table->boolean('is_pwd')->default(false);
            $table->text('pwd_details')->nullable();
            $table->boolean('is_working_student')->default(false);

            // Profile photo
            $table->string('profile_photo')->nullable();

            // Assigned counselor
            $table->foreignId('assigned_counselor_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
