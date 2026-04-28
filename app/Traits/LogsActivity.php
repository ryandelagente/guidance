<?php

namespace App\Traits;

use App\Models\AuditLog;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            AuditLog::record(
                action: 'created',
                subject: $model,
                description: static::auditDescription($model, 'created'),
            );
        });

        static::updated(function ($model) {
            $changed = $model->getChanges();
            unset($changed['updated_at']);
            if (empty($changed)) return;

            $original = collect($changed)->map(fn ($v, $k) => $model->getOriginal($k))->toArray();

            AuditLog::record(
                action: 'updated',
                subject: $model,
                description: static::auditDescription($model, 'updated'),
                changes: ['old' => $original, 'new' => $changed],
            );
        });

        static::deleted(function ($model) {
            AuditLog::record(
                action: 'deleted',
                subject: $model,
                description: static::auditDescription($model, 'deleted'),
            );
        });
    }

    protected static function auditDescription($model, string $action): string
    {
        $label = class_basename($model);
        $name  = $model->name
              ?? $model->title
              ?? $model->full_name
              ?? ($model->first_name ? trim($model->first_name . ' ' . ($model->last_name ?? '')) : null)
              ?? "#{$model->getKey()}";
        return "{$label} {$action}: {$name}";
    }
}
