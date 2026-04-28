<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class GroupSession extends Model
{
    use LogsActivity;

    protected $fillable = [
        'counselor_id', 'title', 'description', 'focus',
        'session_date', 'start_time', 'end_time', 'venue',
        'max_participants', 'status', 'group_notes',
    ];

    protected function casts(): array
    {
        return ['session_date' => 'date'];
    }

    public const FOCUSES = [
        'anxiety'         => '😰 Anxiety',
        'depression'      => '😔 Depression',
        'peer_support'    => '🤝 Peer Support',
        'academic_stress' => '📚 Academic Stress',
        'social_skills'   => '👥 Social Skills',
        'grief'           => '🕊️ Grief & Loss',
        'substance'       => '🌿 Substance Use',
        'other'           => '📌 Other',
    ];

    public function counselor()
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function participants()
    {
        return $this->hasMany(GroupSessionParticipant::class)->with('studentProfile');
    }

    public function getRegisteredCountAttribute(): int
    {
        return $this->participants()->whereIn('attendance', ['registered','attended'])->count();
    }

    public function isFull(): bool
    {
        return $this->registered_count >= $this->max_participants;
    }
}
