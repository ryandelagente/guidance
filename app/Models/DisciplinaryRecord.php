<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisciplinaryRecord extends Model
{
    protected $fillable = [
        'student_profile_id', 'reported_by', 'handled_by',
        'offense_type', 'offense_category', 'incident_date',
        'description', 'action_taken', 'status',
        'sanction', 'sanction_end_date',
    ];

    protected function casts(): array
    {
        return [
            'incident_date'    => 'date',
            'sanction_end_date' => 'date',
        ];
    }

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function getOffenseTypeBadgeClass(): string
    {
        return $this->offense_type === 'major'
            ? 'bg-red-100 text-red-700'
            : 'bg-yellow-100 text-yellow-700';
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'pending'      => 'bg-yellow-100 text-yellow-700',
            'under_review' => 'bg-blue-100 text-blue-700',
            'resolved'     => 'bg-green-100 text-green-700',
            'escalated'    => 'bg-red-100 text-red-700',
            default        => 'bg-gray-100 text-gray-500',
        };
    }
}
