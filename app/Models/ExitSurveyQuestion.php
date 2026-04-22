<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExitSurveyQuestion extends Model
{
    protected $fillable = [
        'question_text', 'question_type', 'options',
        'is_required', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'options'     => 'array',
            'is_required' => 'boolean',
            'is_active'   => 'boolean',
        ];
    }

    public function responses()
    {
        return $this->hasMany(ExitSurveyResponse::class);
    }
}
