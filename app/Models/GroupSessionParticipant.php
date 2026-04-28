<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupSessionParticipant extends Model
{
    protected $fillable = ['group_session_id', 'student_profile_id', 'attendance'];

    public function groupSession()
    {
        return $this->belongsTo(GroupSession::class);
    }

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }
}
