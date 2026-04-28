<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class ActionPlan extends Model
{
    use LogsActivity;

    protected $fillable = [
        'student_profile_id', 'counselor_id', 'title', 'description',
        'focus_area', 'status', 'start_date', 'target_date',
        'completed_at', 'outcome_notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date'   => 'date',
            'target_date'  => 'date',
            'completed_at' => 'date',
        ];
    }

    public const FOCUS_AREAS = [
        'academic'      => '📚 Academic',
        'mental_health' => '🧠 Mental Health',
        'behavioral'    => '🎯 Behavioral',
        'career'        => '💼 Career',
        'social'        => '👥 Social',
        'financial'     => '💰 Financial',
        'other'         => '📌 Other',
    ];

    public const STATUSES = [
        'draft'     => 'Draft',
        'active'    => 'Active',
        'on_hold'   => 'On Hold',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function counselor()
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function milestones()
    {
        return $this->hasMany(ActionPlanMilestone::class)->orderBy('sort_order');
    }

    public function getProgressPercentAttribute(): int
    {
        $total = $this->milestones->count();
        if ($total === 0) return 0;
        $done = $this->milestones->whereNotNull('completed_at')->count();
        return (int) round($done / $total * 100);
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'draft'     => 'bg-gray-100 text-gray-600',
            'active'    => 'bg-blue-100 text-blue-700',
            'on_hold'   => 'bg-yellow-100 text-yellow-700',
            'completed' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-red-100 text-red-700',
            default     => 'bg-gray-100 text-gray-600',
        };
    }

    public function getFocusLabelAttribute(): string
    {
        return self::FOCUS_AREAS[$this->focus_area] ?? $this->focus_area;
    }

    public function isOverdue(): bool
    {
        return $this->target_date
            && $this->target_date->isPast()
            && in_array($this->status, ['active', 'draft']);
    }
}
