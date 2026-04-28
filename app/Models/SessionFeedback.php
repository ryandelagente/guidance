<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionFeedback extends Model
{
    protected $table = 'session_feedback';

    protected $fillable = [
        'counseling_session_id', 'student_profile_id',
        'overall_rating', 'helpful_score', 'listened_score', 'comfort_score',
        'would_recommend', 'issue_resolved',
        'what_worked', 'what_could_improve',
    ];

    protected function casts(): array
    {
        return [
            'would_recommend' => 'boolean',
            'issue_resolved'  => 'boolean',
        ];
    }

    public function session()
    {
        return $this->belongsTo(CounselingSession::class, 'counseling_session_id');
    }

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function getAverageScoreAttribute(): float
    {
        return round(($this->overall_rating + $this->helpful_score + $this->listened_score + $this->comfort_score) / 4, 1);
    }
}
