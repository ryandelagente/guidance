<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralIntervention extends Model
{
    protected $fillable = [
        'referral_id', 'counselor_id', 'status_label',
        'new_status', 'internal_notes',
    ];

    public function referral()
    {
        return $this->belongsTo(Referral::class);
    }

    public function counselor()
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }
}
