<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'student_id',
        'employee_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // ── Role helpers ─────────────────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isGuidanceDirector(): bool
    {
        return $this->role === 'guidance_director';
    }

    public function isCounselor(): bool
    {
        return $this->role === 'guidance_counselor';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isFaculty(): bool
    {
        return $this->role === 'faculty';
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['super_admin', 'guidance_director', 'guidance_counselor']);
    }

    public function getRoleDisplayName(): string
    {
        return match ($this->role) {
            'super_admin'         => 'Super Administrator',
            'guidance_director'   => 'Guidance Director',
            'guidance_counselor'  => 'Guidance Counselor',
            'student'             => 'Student',
            'faculty'             => 'Faculty / Staff',
            default               => 'Unknown',
        };
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function studentProfile()
    {
        return $this->hasOne(\App\Models\StudentProfile::class);
    }

    public function assignedStudents()
    {
        return $this->hasMany(\App\Models\StudentProfile::class, 'assigned_counselor_id');
    }
}
