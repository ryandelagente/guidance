<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    protected $fillable = [
        'student_profile_id', 'psychological_test_id', 'test_schedule_id',
        'recorded_by', 'raw_score', 'percentile', 'grade_equivalent',
        'interpretation_level', 'interpretation', 'career_matches',
        'test_date', 'is_released',
    ];

    protected function casts(): array
    {
        return [
            'test_date'     => 'date',
            'is_released'   => 'boolean',
            'career_matches'=> 'array',
        ];
    }

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function test()
    {
        return $this->belongsTo(PsychologicalTest::class, 'psychological_test_id');
    }

    public function schedule()
    {
        return $this->belongsTo(TestSchedule::class, 'test_schedule_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getInterpretationBadgeClass(): string
    {
        return match($this->interpretation_level) {
            'very_low'      => 'bg-red-100 text-red-700',
            'low'           => 'bg-orange-100 text-orange-700',
            'average'       => 'bg-yellow-100 text-yellow-700',
            'above_average' => 'bg-blue-100 text-blue-700',
            'superior'      => 'bg-green-100 text-green-700',
            'very_superior' => 'bg-purple-100 text-purple-700',
            default         => 'bg-gray-100 text-gray-500',
        };
    }
}
