<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScreeningResponse extends Model
{
    protected $fillable = [
        'student_profile_id', 'instrument', 'answers', 'total_score',
        'severity', 'positive_self_harm', 'reviewed', 'reviewed_by',
        'reviewed_at', 'counselor_notes',
    ];

    protected function casts(): array
    {
        return [
            'answers'            => 'array',
            'positive_self_harm' => 'boolean',
            'reviewed'           => 'boolean',
            'reviewed_at'        => 'datetime',
        ];
    }

    public const INSTRUMENTS = [
        'phq9' => 'PHQ-9 (Depression)',
        'gad7' => 'GAD-7 (Anxiety)',
        'k10'  => 'K-10 (Psychological Distress)',
    ];

    /**
     * Standard PHQ-9 questionnaire (Patient Health Questionnaire-9 — depression).
     * Validated by Spitzer, Kroenke, Williams (1999). Public domain.
     */
    public const PHQ9_QUESTIONS = [
        'Little interest or pleasure in doing things',
        'Feeling down, depressed, or hopeless',
        'Trouble falling/staying asleep, or sleeping too much',
        'Feeling tired or having little energy',
        'Poor appetite or overeating',
        'Feeling bad about yourself — or that you are a failure or have let yourself or your family down',
        'Trouble concentrating on things, such as reading or watching TV',
        'Moving or speaking so slowly that others noticed — or being so fidgety/restless that you moved around a lot more than usual',
        'Thoughts that you would be better off dead, or of hurting yourself in some way',
    ];

    /**
     * Standard GAD-7 (Generalized Anxiety Disorder-7).
     * Validated by Spitzer et al. (2006). Public domain.
     */
    public const GAD7_QUESTIONS = [
        'Feeling nervous, anxious, or on edge',
        'Not being able to stop or control worrying',
        'Worrying too much about different things',
        'Trouble relaxing',
        'Being so restless that it\'s hard to sit still',
        'Becoming easily annoyed or irritable',
        'Feeling afraid as if something awful might happen',
    ];

    /**
     * K-10 (Kessler Psychological Distress Scale).
     * Public domain, widely used in PH/AU mental health screening.
     */
    public const K10_QUESTIONS = [
        'About how often did you feel tired out for no good reason?',
        'About how often did you feel nervous?',
        'About how often did you feel so nervous that nothing could calm you down?',
        'About how often did you feel hopeless?',
        'About how often did you feel restless or fidgety?',
        'About how often did you feel so restless you could not sit still?',
        'About how often did you feel depressed?',
        'About how often did you feel that everything was an effort?',
        'About how often did you feel so sad that nothing could cheer you up?',
        'About how often did you feel worthless?',
    ];

    public const RESPONSE_OPTIONS_PHQ_GAD = [
        0 => 'Not at all',
        1 => 'Several days',
        2 => 'More than half the days',
        3 => 'Nearly every day',
    ];

    public const RESPONSE_OPTIONS_K10 = [
        1 => 'None of the time',
        2 => 'A little of the time',
        3 => 'Some of the time',
        4 => 'Most of the time',
        5 => 'All of the time',
    ];

    public static function questions(string $instrument): array
    {
        return match ($instrument) {
            'phq9' => self::PHQ9_QUESTIONS,
            'gad7' => self::GAD7_QUESTIONS,
            'k10'  => self::K10_QUESTIONS,
            default => [],
        };
    }

    public static function options(string $instrument): array
    {
        return $instrument === 'k10' ? self::RESPONSE_OPTIONS_K10 : self::RESPONSE_OPTIONS_PHQ_GAD;
    }

    /**
     * Score interpretation per validated cutoffs.
     */
    public static function interpretScore(string $instrument, int $score): string
    {
        if ($instrument === 'phq9') {
            return match (true) {
                $score >= 20 => 'severe',
                $score >= 15 => 'moderately_severe',
                $score >= 10 => 'moderate',
                $score >= 5  => 'mild',
                default      => 'minimal',
            };
        }
        if ($instrument === 'gad7') {
            return match (true) {
                $score >= 15 => 'severe',
                $score >= 10 => 'moderate',
                $score >= 5  => 'mild',
                default      => 'minimal',
            };
        }
        // K-10
        return match (true) {
            $score >= 30 => 'severe',
            $score >= 25 => 'moderate',
            $score >= 20 => 'mild',
            default      => 'low',
        };
    }

    public function getSeverityBadgeClass(): string
    {
        return match ($this->severity) {
            'severe', 'moderately_severe' => 'bg-red-100 text-red-700',
            'moderate' => 'bg-orange-100 text-orange-700',
            'mild'     => 'bg-yellow-100 text-yellow-700',
            default    => 'bg-green-100 text-green-700',
        };
    }

    public function getInstrumentLabelAttribute(): string
    {
        return self::INSTRUMENTS[$this->instrument] ?? $this->instrument;
    }

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
