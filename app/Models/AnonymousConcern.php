<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AnonymousConcern extends Model
{
    protected $fillable = [
        'reference_code', 'concern_type', 'urgency', 'description',
        'about_who', 'location', 'reporter_relationship', 'contact_email',
        'status', 'handled_by', 'staff_notes', 'resolved_at', 'ip_address',
    ];

    protected function casts(): array
    {
        return ['resolved_at' => 'datetime'];
    }

    public const TYPES = [
        'bullying'             => '💢 Bullying',
        'mental_health'        => '🧠 Mental Health (peer)',
        'self_harm'            => '🆘 Self-Harm Concern',
        'abuse'                => '⚠️ Abuse',
        'substance'            => '🌿 Substance Use',
        'academic_dishonesty'  => '📝 Academic Dishonesty',
        'harassment'           => '🚫 Harassment',
        'safety'               => '🚨 Safety / Threat',
        'other'                => '❓ Other',
    ];

    public const URGENCIES = [
        'low'      => 'Low — non-urgent',
        'medium'   => 'Medium — needs attention',
        'high'     => 'High — needs prompt action',
        'critical' => '🚨 Critical — immediate action needed',
    ];

    public const STATUSES = [
        'new'           => 'New',
        'reviewing'     => 'Reviewing',
        'action_taken'  => 'Action Taken',
        'resolved'      => 'Resolved',
        'dismissed'     => 'Dismissed',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->reference_code)) {
                $model->reference_code = self::generateReference();
            }
        });
    }

    public static function generateReference(): string
    {
        do {
            $code = 'TIP-' . strtoupper(Str::random(8));
        } while (self::where('reference_code', $code)->exists());
        return $code;
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->concern_type] ?? $this->concern_type;
    }

    public function getUrgencyBadgeClass(): string
    {
        return match ($this->urgency) {
            'critical' => 'bg-red-100 text-red-700 border-red-300',
            'high'     => 'bg-orange-100 text-orange-700 border-orange-300',
            'medium'   => 'bg-yellow-100 text-yellow-700 border-yellow-300',
            default    => 'bg-gray-100 text-gray-600 border-gray-300',
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'new'           => 'bg-blue-100 text-blue-700',
            'reviewing'     => 'bg-yellow-100 text-yellow-700',
            'action_taken'  => 'bg-purple-100 text-purple-700',
            'resolved'      => 'bg-green-100 text-green-700',
            'dismissed'     => 'bg-gray-100 text-gray-500',
            default         => 'bg-gray-100 text-gray-600',
        };
    }
}
