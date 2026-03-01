<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        // Log lors de la création d'un nouvel enregistrement
        static::created(fn (Model $model) => static::recordAudit($model, 'created'));

        // Log lors d'une modification
        static::updated(function (Model $model) {
            // On extrait uniquement ce qui a été modifié
            $newValues = $model->getDirty();

            // On récupère les valeurs originales pour ces champs précis
            $oldValues = array_intersect_key($model->getOriginal(), $newValues);

            // On ignore les colonnes de mise à jour système
            unset($newValues['updated_at'], $oldValues['updated_at']);

            if (empty($newValues)) {
                return;
            }

            static::recordAudit($model, 'updated', $oldValues, $newValues);
        });

        // Log lors de la suppression
        static::deleted(fn (Model $model) => static::recordAudit($model, 'deleted', $model->getOriginal()));
    }

    protected static function recordAudit(Model $model, string $event, ?array $old = null, ?array $new = null): void
    {
        $userAgent = null;
        $ipAddress = null;

        if (app()->runningInConsole() === false && app()->bound('request')) {
            $request = request();
            $userAgent = $request->userAgent();
            $ipAddress = $request->ip();
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_id' => $model->getKey(),
            'auditable_type' => $model->getMorphClass(),
            'event' => $event,
            'old_values' => $old,
            'new_values' => $new,
            'user_agent' => $userAgent,
            'ip_address' => $ipAddress,
        ]);
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
