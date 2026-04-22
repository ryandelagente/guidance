<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClearanceRequest extends Model
{
    protected $fillable = [
        'student_profile_id', 'processed_by', 'clearance_type',
        'academic_year', 'semester', 'purpose', 'status', 'notes', 'processed_at',
    ];

    protected function casts(): array
    {
        return ['processed_at' => 'datetime'];
    }

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function surveyResponses()
    {
        return $this->hasMany(ExitSurveyResponse::class);
    }

    public function certificate()
    {
        return $this->hasOne(GoodMoralCertificate::class);
    }

    public function requiresExitSurvey(): bool
    {
        return $this->clearance_type === 'graduation';
    }

    public function surveyCompleted(): bool
    {
        if (!$this->requiresExitSurvey()) return true;
        $required = ExitSurveyQuestion::where('is_active', true)->where('is_required', true)->count();
        return $required === 0 || $this->surveyResponses()->count() >= $required;
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'pending'          => 'bg-yellow-100 text-yellow-700',
            'for_exit_survey'  => 'bg-purple-100 text-purple-700',
            'survey_done'      => 'bg-blue-100 text-blue-700',
            'approved'         => 'bg-green-100 text-green-700',
            'rejected'         => 'bg-red-100 text-red-700',
            'on_hold'          => 'bg-orange-100 text-orange-700',
            default            => 'bg-gray-100 text-gray-500',
        };
    }
}
