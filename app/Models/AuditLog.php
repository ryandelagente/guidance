<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'auditable_type', 'auditable_id',
        'description', 'ip_address', 'user_agent', 'changes',
    ];

    protected function casts(): array
    {
        return ['changes' => 'array'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    /**
     * Convenience recorder used from controllers and listeners.
     */
    public static function record(string $action, ?Model $subject = null, ?string $description = null, ?array $changes = null): self
    {
        $request = request();
        return self::create([
            'user_id'        => Auth::id(),
            'action'         => $action,
            'auditable_type' => $subject ? $subject->getMorphClass() : null,
            'auditable_id'   => $subject?->getKey(),
            'description'    => $description,
            'ip_address'     => $request?->ip(),
            'user_agent'     => $request?->userAgent() ? substr($request->userAgent(), 0, 500) : null,
            'changes'        => $changes,
        ]);
    }

    public function getActionBadgeClass(): string
    {
        return match ($this->action) {
            'created'      => 'bg-green-100 text-green-700',
            'updated'      => 'bg-blue-100 text-blue-700',
            'deleted'      => 'bg-red-100 text-red-700',
            'viewed'       => 'bg-gray-100 text-gray-600',
            'login'        => 'bg-emerald-100 text-emerald-700',
            'logout'       => 'bg-slate-100 text-slate-600',
            'failed_login' => 'bg-red-100 text-red-700',
            'export'       => 'bg-indigo-100 text-indigo-700',
            default        => 'bg-gray-100 text-gray-600',
        };
    }
}
