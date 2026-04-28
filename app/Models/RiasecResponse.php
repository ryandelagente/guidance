<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiasecResponse extends Model
{
    protected $fillable = [
        'student_profile_id',
        'score_r','score_i','score_a','score_s','score_e','score_c',
        'top_code', 'answers', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'answers'      => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public const TYPE_LABELS = [
        'R' => 'Realistic',
        'I' => 'Investigative',
        'A' => 'Artistic',
        'S' => 'Social',
        'E' => 'Enterprising',
        'C' => 'Conventional',
    ];

    public const TYPE_DESCRIPTIONS = [
        'R' => 'The Doer — Practical, hands-on, mechanical, athletic. Likes working with tools, machines, and the outdoors.',
        'I' => 'The Thinker — Analytical, intellectual, curious, scientific. Likes solving problems and conducting research.',
        'A' => 'The Creator — Imaginative, expressive, original, independent. Likes art, design, music, and creative writing.',
        'S' => 'The Helper — Cooperative, empathetic, supportive, communicative. Likes teaching, counseling, and serving others.',
        'E' => 'The Persuader — Confident, ambitious, assertive, sociable. Likes leading, selling, and influencing others.',
        'C' => 'The Organizer — Detail-oriented, methodical, structured, dependable. Likes data, organization, and procedures.',
    ];

    public const TYPE_COLORS = [
        'R' => 'bg-green-100 text-green-700',
        'I' => 'bg-blue-100 text-blue-700',
        'A' => 'bg-purple-100 text-purple-700',
        'S' => 'bg-pink-100 text-pink-700',
        'E' => 'bg-orange-100 text-orange-700',
        'C' => 'bg-gray-100 text-gray-700',
    ];

    public const CAREER_MATCHES = [
        'R' => ['Engineer', 'Mechanic', 'Electrician', 'Forester', 'Athlete', 'Chef', 'Carpenter', 'Pilot', 'Farmer', 'Construction Manager'],
        'I' => ['Researcher', 'Scientist', 'Doctor', 'Statistician', 'Software Developer', 'Mathematician', 'Pharmacist', 'Biologist', 'Economist', 'Data Analyst'],
        'A' => ['Graphic Designer', 'Writer', 'Musician', 'Architect', 'Photographer', 'Animator', 'Fashion Designer', 'Film Director', 'Interior Designer', 'Journalist'],
        'S' => ['Guidance Counselor', 'Teacher', 'Nurse', 'Social Worker', 'Pastor / Religious Worker', 'Speech Therapist', 'Childcare Worker', 'Coach', 'Community Organizer', 'Psychologist'],
        'E' => ['Lawyer', 'Sales Manager', 'Entrepreneur', 'Marketing Director', 'Real Estate Agent', 'Politician', 'Corporate Executive', 'Travel Agent', 'Insurance Agent', 'Public Relations Specialist'],
        'C' => ['Accountant', 'Bookkeeper', 'Bank Teller', 'Office Manager', 'Auditor', 'Tax Preparer', 'Database Administrator', 'Logistics Coordinator', 'Court Clerk', 'Medical Records Technician'],
    ];

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function getScoresArrayAttribute(): array
    {
        return [
            'R' => $this->score_r,
            'I' => $this->score_i,
            'A' => $this->score_a,
            'S' => $this->score_s,
            'E' => $this->score_e,
            'C' => $this->score_c,
        ];
    }

    public function getCareerMatchesAttribute(): array
    {
        $codes = str_split($this->top_code);
        $matches = [];
        foreach ($codes as $code) {
            $careers = self::CAREER_MATCHES[$code] ?? [];
            foreach ($careers as $c) {
                $matches[$c] = ($matches[$c] ?? 0) + (3 - count($matches) % 3); // weighted
            }
        }
        arsort($matches);
        return array_slice(array_keys($matches), 0, 12);
    }
}
