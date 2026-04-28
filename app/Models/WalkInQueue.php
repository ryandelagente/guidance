<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalkInQueue extends Model
{
    protected $table = 'walk_in_queue';

    protected $fillable = [
        'student_profile_id', 'name', 'contact_number', 'reason',
        'priority', 'status', 'assigned_counselor_id',
        'arrived_at', 'called_at', 'completed_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'arrived_at'   => 'datetime',
            'called_at'    => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function counselor()
    {
        return $this->belongsTo(User::class, 'assigned_counselor_id');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->studentProfile?->full_name ?? $this->name ?? 'Walk-in';
    }

    public function getWaitMinutesAttribute(): int
    {
        $end = $this->called_at ?? now();
        return $this->arrived_at->diffInMinutes($end);
    }

    public function getPriorityBadgeClass(): string
    {
        return match ($this->priority) {
            'crisis' => 'bg-red-100 text-red-700 border-red-300',
            'urgent' => 'bg-orange-100 text-orange-700 border-orange-300',
            default  => 'bg-gray-100 text-gray-600 border-gray-300',
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'waiting'    => 'bg-yellow-100 text-yellow-700',
            'being_seen' => 'bg-blue-100 text-blue-700',
            'completed'  => 'bg-green-100 text-green-700',
            'no_show'    => 'bg-gray-100 text-gray-500',
            'cancelled'  => 'bg-red-100 text-red-600',
            default      => 'bg-gray-100 text-gray-600',
        };
    }
}
