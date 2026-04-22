<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'student_profile_id', 'counselor_id', 'appointment_type',
        'appointment_date', 'start_time', 'end_time', 'status',
        'meeting_type', 'meeting_link', 'student_concern',
        'notes_for_student', 'cancelled_reason', 'cancelled_by',
    ];

    protected function casts(): array
    {
        return ['appointment_date' => 'date'];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function counselor()
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function session()
    {
        return $this->hasOne(CounselingSession::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeUpcoming($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed'])
                     ->where('appointment_date', '>=', now()->toDateString())
                     ->orderBy('appointment_date')
                     ->orderBy('start_time');
    }

    public function scopeForCounselor($query, int $counselorId)
    {
        return $query->where('counselor_id', $counselorId);
    }

    public function scopeForStudent($query, int $studentProfileId)
    {
        return $query->where('student_profile_id', $studentProfileId);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isConfirmed(): bool { return $this->status === 'confirmed'; }
    public function isCompleted(): bool { return $this->status === 'completed'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'pending'     => 'bg-yellow-100 text-yellow-700',
            'confirmed'   => 'bg-blue-100 text-blue-700',
            'in_progress' => 'bg-purple-100 text-purple-700',
            'completed'   => 'bg-green-100 text-green-700',
            'cancelled'   => 'bg-red-100 text-red-700',
            'no_show'     => 'bg-gray-100 text-gray-500',
            default       => 'bg-gray-100 text-gray-600',
        };
    }
}
