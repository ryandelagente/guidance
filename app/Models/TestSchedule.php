<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestSchedule extends Model
{
    protected $fillable = [
        'psychological_test_id', 'administered_by', 'college', 'program',
        'year_level', 'scheduled_date', 'start_time', 'venue',
        'expected_participants', 'notes', 'status',
    ];

    protected function casts(): array
    {
        return ['scheduled_date' => 'date'];
    }

    public function test()
    {
        return $this->belongsTo(PsychologicalTest::class, 'psychological_test_id');
    }

    public function administeredBy()
    {
        return $this->belongsTo(User::class, 'administered_by');
    }

    public function results()
    {
        return $this->hasMany(TestResult::class);
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'scheduled'  => 'bg-blue-100 text-blue-700',
            'ongoing'    => 'bg-yellow-100 text-yellow-700',
            'completed'  => 'bg-green-100 text-green-700',
            'cancelled'  => 'bg-red-100 text-red-700',
            default      => 'bg-gray-100 text-gray-500',
        };
    }
}
