<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionPlanMilestone extends Model
{
    protected $fillable = [
        'action_plan_id', 'description', 'target_date',
        'completed_at', 'notes', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'target_date'  => 'date',
            'completed_at' => 'date',
        ];
    }

    public function actionPlan()
    {
        return $this->belongsTo(ActionPlan::class);
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function isOverdue(): bool
    {
        return !$this->isCompleted() && $this->target_date && $this->target_date->isPast();
    }
}
