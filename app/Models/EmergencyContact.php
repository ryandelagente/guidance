<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    protected $fillable = [
        'student_profile_id', 'name', 'relationship',
        'contact_number', 'address', 'is_primary',
    ];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean'];
    }

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }
}
