<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    protected $fillable = [
        'organizer_id', 'title', 'description', 'category', 'venue',
        'mode', 'meeting_link', 'starts_at', 'ends_at', 'capacity',
        'rsvp_deadline', 'audience', 'status', 'cover_color',
    ];

    protected function casts(): array
    {
        return [
            'starts_at'     => 'datetime',
            'ends_at'       => 'datetime',
            'rsvp_deadline' => 'datetime',
        ];
    }

    public const CATEGORIES = [
        'mental_health' => '🧠 Mental Health',
        'academic'      => '📚 Academic',
        'career'        => '💼 Career',
        'life_skills'   => '🎯 Life Skills',
        'wellness'      => '🌱 Wellness',
        'seminar'       => '🎤 Seminar',
        'other'         => '📌 Other',
    ];

    public const COVER_COLORS = [
        'blue'    => 'from-blue-500 to-indigo-600',
        'purple'  => 'from-purple-500 to-pink-600',
        'green'   => 'from-green-500 to-emerald-600',
        'orange'  => 'from-orange-500 to-red-600',
        'teal'    => 'from-teal-500 to-cyan-600',
        'rose'    => 'from-rose-500 to-pink-600',
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function rsvps()
    {
        return $this->hasMany(WorkshopRsvp::class);
    }

    public function getRegisteredCountAttribute(): int
    {
        return $this->rsvps()->whereIn('status', ['registered','attended'])->count();
    }

    public function getAttendedCountAttribute(): int
    {
        return $this->rsvps()->where('status', 'attended')->count();
    }

    public function isFull(): bool
    {
        return $this->capacity && $this->registered_count >= $this->capacity;
    }

    public function isUpcoming(): bool
    {
        return $this->starts_at->isFuture();
    }

    public function isOngoing(): bool
    {
        return $this->starts_at->isPast() && $this->ends_at->isFuture();
    }

    public function isPast(): bool
    {
        return $this->ends_at->isPast();
    }

    public function rsvpsClosed(): bool
    {
        return $this->rsvp_deadline && $this->rsvp_deadline->isPast();
    }

    public function userIsRegistered(?User $user): bool
    {
        if (!$user) return false;
        return $this->rsvps()->where('user_id', $user->id)->whereIn('status', ['registered','attended'])->exists();
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
}
