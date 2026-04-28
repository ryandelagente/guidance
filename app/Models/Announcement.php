<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use LogsActivity;

    protected $fillable = [
        'created_by', 'title', 'body', 'audience', 'priority',
        'is_pinned', 'is_published', 'published_at', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned'    => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'expires_at'   => 'datetime',
        ];
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublishedFor($query, ?User $user)
    {
        $query->where('is_published', true)
              ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))
              ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()));

        if (!$user) return $query;

        $audiences = ['all'];
        if ($user->isStudent())  $audiences[] = 'students';
        if ($user->isFaculty())  $audiences[] = 'faculty';
        if ($user->isStaff())    { $audiences[] = 'staff'; $audiences[] = 'counselors'; }

        return $query->whereIn('audience', $audiences);
    }

    public function getPriorityBadgeClass(): string
    {
        return match ($this->priority) {
            'urgent'  => 'bg-red-100 text-red-700',
            'warning' => 'bg-yellow-100 text-yellow-700',
            default   => 'bg-blue-100 text-blue-700',
        };
    }

    public function getPriorityIcon(): string
    {
        return match ($this->priority) {
            'urgent'  => '🚨',
            'warning' => '⚠️',
            default   => 'ℹ️',
        };
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
