<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::logAction($model, 'create');
        });

        static::updated(function ($model) {
            self::logAction($model, 'update');
        });

        static::deleted(function ($model) {
            self::logAction($model, 'delete');
        });
    }

    protected static function logAction($model, $action)
    {
        // Don't log if not authenticated
        if (!Auth::check()) {
            return;
        }

        $targetName = $model->code ?? $model->name ?? $model->email ?? '#' . $model->id;

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'target_type' => get_class($model),
            'target_id' => $model->id,
            'metadata' => [
                'target_name' => $targetName,
                'changes' => $action === 'update' ? $model->getDirty() : null,
            ],
        ]);
    }
}
