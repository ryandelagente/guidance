<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use LogsActivity;

    protected $fillable = [
        'user_id', 'first_name', 'middle_name', 'last_name', 'suffix',
        'date_of_birth', 'sex', 'civil_status', 'religion', 'nationality',
        'contact_number', 'home_address', 'student_id_number', 'college',
        'program', 'year_level', 'student_type', 'scholarship', 'academic_status',
        'father_name', 'father_occupation', 'father_contact',
        'mother_name', 'mother_occupation', 'mother_contact', 'parents_status',
        'guardian_name', 'guardian_relationship', 'guardian_contact',
        'monthly_family_income', 'is_pwd', 'pwd_details', 'is_working_student',
        'profile_photo', 'assigned_counselor_id',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth'      => 'date',
            'is_pwd'             => 'boolean',
            'is_working_student' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedCounselor()
    {
        return $this->belongsTo(User::class, 'assigned_counselor_id');
    }

    public function documents()
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function emergencyContacts()
    {
        return $this->hasMany(EmergencyContact::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function counselingSessions()
    {
        return $this->hasMany(CounselingSession::class);
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class);
    }

    public function disciplinaryRecords()
    {
        return $this->hasMany(DisciplinaryRecord::class);
    }

    public function testResults()
    {
        return $this->hasMany(TestResult::class);
    }

    public function clearanceRequests()
    {
        return $this->hasMany(ClearanceRequest::class);
    }

    public function certificates()
    {
        return $this->hasMany(GoodMoralCertificate::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name} {$this->suffix}");
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }
}
