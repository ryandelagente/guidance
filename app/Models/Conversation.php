<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['counselor_id', 'student_user_id', 'subject', 'last_message_at'];

    protected function casts(): array
    {
        return ['last_message_at' => 'datetime'];
    }

    public function counselor()
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function studentUser()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function otherParty(User $user): User
    {
        return $user->id === $this->counselor_id ? $this->studentUser : $this->counselor;
    }

    public function unreadCountFor(User $user): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->count();
    }

    public function involves(User $user): bool
    {
        return in_array($user->id, [$this->counselor_id, $this->student_user_id]);
    }
}
