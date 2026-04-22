<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodMoralCertificate extends Model
{
    protected $fillable = [
        'student_profile_id', 'issued_by', 'clearance_request_id',
        'certificate_number', 'purpose', 'validity_months',
        'issued_at', 'is_revoked', 'revoked_reason',
    ];

    protected function casts(): array
    {
        return [
            'issued_at'  => 'datetime',
            'is_revoked' => 'boolean',
        ];
    }

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function clearanceRequest()
    {
        return $this->belongsTo(ClearanceRequest::class);
    }

    public function expiresAt(): \Carbon\Carbon
    {
        return $this->issued_at->addMonths($this->validity_months);
    }

    public function isExpired(): bool
    {
        return $this->expiresAt()->isPast();
    }

    public static function generateNumber(): string
    {
        $latest = static::latest()->first();
        $seq    = $latest ? ((int) substr($latest->certificate_number, -5)) + 1 : 1;
        return 'GMC-' . date('Y') . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }
}
