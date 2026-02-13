<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            static::logAction($model, 'created');
        });

        static::updated(function ($model) {
            static::logAction($model, 'updated');
        });

        static::deleted(function ($model) {
            static::logAction($model, 'deleted');
        });
    }

    protected static function logAction($model, $action)
    {
        $oldValues = null;
        $newValues = null;

        if ($action === 'updated') {
            $changes = $model->getChanges();
            $oldValues = array_intersect_key($model->getOriginal(), $changes);
            $newValues = $changes;
        } elseif ($action === 'created') {
            $newValues = $model->toArray();
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => "Menyelesaikan aksi $action pada " . class_basename($model) . " (ID: $model->id)",
        ]);
    }
}
