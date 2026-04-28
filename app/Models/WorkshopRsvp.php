<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkshopRsvp extends Model
{
    protected $fillable = ['workshop_id', 'user_id', 'status', 'attended_at', 'notes'];

    protected function casts(): array
    {
        return ['attended_at' => 'datetime'];
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
