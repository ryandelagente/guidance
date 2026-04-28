<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class CounselingSession extends Model
{
    use LogsActivity;

    protected $fillable = [
        'appointment_id', 'counselor_id', 'student_profile_id',
        'case_notes', 'recommendations', 'follow_up_date',
        'session_status', 'presenting_concern',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_date' => 'date',
            // AES-256 encryption — only the counselor who knows their PIN can read
            'case_notes' => 'encrypted',
        ];
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function counselor()
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }
}
