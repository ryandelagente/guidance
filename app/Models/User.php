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
        'case_note_pin_hash',
        'case_note_pin_set_at',
        'calendar_feed_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_enabled_at',
        'notification_preferences',
        'phone_number',
        'phone_verified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'case_note_pin_hash',
        'calendar_feed_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'         => 'datetime',
            'password'                  => 'hashed',
            'is_active'                 => 'boolean',
            'case_note_pin_set_at'      => 'datetime',
            'two_factor_secret'         => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_enabled_at'     => 'datetime',
            'notification_preferences'  => 'array',
            'phone_verified'            => 'boolean',
        ];
    }

    public static function defaultNotificationPreferences(): array
    {
        return [
            'channels' => ['email' => true, 'sms' => false, 'inapp' => true],
            'events' => [
                'appointment_reminder'  => true,
                'appointment_confirmed' => true,
                'referral_assigned'     => true,
                'message_received'      => true,
                'announcement_urgent'   => true,
                'announcement_normal'   => false,
                'wellness_crisis_alert' => true,
                'data_correction_response' => true,
            ],
        ];
    }

    public function getNotificationPrefsResolved(): array
    {
        $stored = $this->notification_preferences ?? [];
        return array_replace_recursive(self::defaultNotificationPreferences(), is_array($stored) ? $stored : []);
    }

    public function wantsNotification(string $event, string $channel = 'email'): bool
    {
        $prefs = $this->getNotificationPrefsResolved();
        return ($prefs['channels'][$channel] ?? true)
            && ($prefs['events'][$event] ?? true);
    }

    public function hasTwoFactorEnabled(): bool
    {
        return !empty($this->two_factor_enabled_at) && !empty($this->two_factor_secret);
    }

    public function generateTwoFactorRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtolower(\Illuminate\Support\Str::random(5)) . '-' . strtolower(\Illuminate\Support\Str::random(5));
        }
        return $codes;
    }

    public function consumeRecoveryCode(string $code): bool
    {
        $codes = $this->two_factor_recovery_codes ?? [];
        $needle = strtolower(trim($code));
        $idx = array_search($needle, array_map('strtolower', $codes), true);
        if ($idx === false) return false;
        unset($codes[$idx]);
        $this->two_factor_recovery_codes = array_values($codes);
        $this->save();
        return true;
    }

    public function hasCaseNotePin(): bool
    {
        return !empty($this->case_note_pin_hash);
    }

    public function setCaseNotePin(string $pin): void
    {
        $this->case_note_pin_hash   = \Illuminate\Support\Facades\Hash::make($pin);
        $this->case_note_pin_set_at = now();
        $this->save();
    }

    public function verifyCaseNotePin(string $pin): bool
    {
        return $this->case_note_pin_hash
            && \Illuminate\Support\Facades\Hash::check($pin, $this->case_note_pin_hash);
    }

    public function ensureCalendarToken(): string
    {
        if (!$this->calendar_feed_token) {
            $this->calendar_feed_token = \Illuminate\Support\Str::random(48);
            $this->save();
        }
        return $this->calendar_feed_token;
    }

    public function regenerateCalendarToken(): string
    {
        $this->calendar_feed_token = \Illuminate\Support\Str::random(48);
        $this->save();
        return $this->calendar_feed_token;
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
