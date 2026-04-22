<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PsychologicalTest extends Model
{
    protected $fillable = [
        'name', 'test_type', 'category', 'description',
        'total_items', 'publisher', 'edition_year', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function schedules()
    {
        return $this->hasMany(TestSchedule::class);
    }

    public function results()
    {
        return $this->hasMany(TestResult::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->test_type) {
            'iq'             => 'IQ / Intelligence',
            'personality'    => 'Personality',
            'career_aptitude'=> 'Career Aptitude',
            'interest'       => 'Interest Inventory',
            'mental_health'  => 'Mental Health',
            default          => 'Other',
        };
    }
}
