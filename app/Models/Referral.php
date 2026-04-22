<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'student_profile_id', 'referred_by', 'assigned_counselor_id',
        'reason_category', 'urgency', 'description', 'status',
        'faculty_feedback', 'acknowledged_at', 'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'acknowledged_at' => 'datetime',
            'resolved_at'     => 'datetime',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function assignedCounselor()
    {
        return $this->belongsTo(User::class, 'assigned_counselor_id');
    }

    public function interventions()
    {
        return $this->hasMany(ReferralIntervention::class)->latest();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function getUrgencyBadgeClass(): string
    {
        return match($this->urgency) {
            'low'      => 'bg-gray-100 text-gray-600',
            'medium'   => 'bg-yellow-100 text-yellow-700',
            'high'     => 'bg-orange-100 text-orange-700',
            'critical' => 'bg-red-100 text-red-700',
            default    => 'bg-gray-100 text-gray-500',
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'pending'      => 'bg-yellow-100 text-yellow-700',
            'acknowledged' => 'bg-blue-100 text-blue-700',
            'in_progress'  => 'bg-purple-100 text-purple-700',
            'resolved'     => 'bg-green-100 text-green-700',
            'closed'       => 'bg-gray-100 text-gray-500',
            default        => 'bg-gray-100 text-gray-500',
        };
    }

    public function scopePending($query)    { return $query->where('status', 'pending'); }
    public function scopeUrgent($query)     { return $query->whereIn('urgency', ['high','critical']); }
}
