<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WellnessCheckin extends Model
{
    protected $fillable = [
        'student_profile_id', 'mood', 'stress_level', 'sleep_quality',
        'academic_stress', 'notes', 'wants_counselor',
        'reviewed', 'reviewed_by', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'wants_counselor' => 'boolean',
            'reviewed'        => 'boolean',
            'reviewed_at'     => 'datetime',
        ];
    }

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Composite risk score: 1 (best) to 5 (worst).
     * Inverts mood/sleep so high score == high concern.
     */
    public function getRiskScoreAttribute(): float
    {
        return round(((6 - $this->mood) + $this->stress_level + (6 - $this->sleep_quality) + $this->academic_stress) / 4, 1);
    }

    public function getRiskLevelAttribute(): string
    {
        $score = $this->risk_score;
        if ($this->wants_counselor || $score >= 4) return 'high';
        if ($score >= 3) return 'medium';
        return 'low';
    }

    public function getRiskBadgeClass(): string
    {
        return match ($this->risk_level) {
            'high'   => 'bg-red-100 text-red-700',
            'medium' => 'bg-yellow-100 text-yellow-700',
            default  => 'bg-green-100 text-green-700',
        };
    }

    public static function moodEmoji(int $value): string
    {
        return ['😢','😟','😐','🙂','😄'][$value - 1] ?? '—';
    }

    public static function moodLabel(int $value): string
    {
        return ['Very Bad','Not Great','Okay','Good','Very Good'][$value - 1] ?? '—';
    }

    public static function intensityLabel(int $value): string
    {
        return ['None','Mild','Moderate','High','Severe'][$value - 1] ?? '—';
    }
}
