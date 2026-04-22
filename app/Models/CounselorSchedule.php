<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CounselorSchedule extends Model
{
    protected $fillable = [
        'counselor_id', 'day_of_week', 'start_time', 'end_time',
        'slot_duration', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function counselor()
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    // Returns array of available HH:MM slot starts for this schedule row
    public function generateSlots(): array
    {
        $slots = [];
        $current = strtotime($this->start_time);
        $end     = strtotime($this->end_time);
        $step    = $this->slot_duration * 60;

        while ($current + $step <= $end) {
            $slots[] = date('H:i', $current);
            $current += $step;
        }

        return $slots;
    }
}
