<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Record an auditable action.
     *
     * @param string      $action      e.g. 'create', 'update', 'delete', 'login', 'approve'
     * @param User|null   $actor       Who performed the action (defaults to auth user)
     * @param Model|null  $entity      The affected model instance
     * @param array|null  $oldData     State before change
     * @param array|null  $newData     State after change / extra context
     * @param string|null $description Human-readable description
     * @param string|null $module      Module name for filtering
     */
    public function log(
        string  $action,
        ?User   $actor = null,
        ?Model  $entity = null,
        ?array  $oldData = null,
        ?array  $newData = null,
        ?string $description = null,
        ?string $module = null
    ): AuditLog {
        $actor = $actor ?? Auth::user();

        return AuditLog::create([
            'user_id'      => $actor?->id,
            'user_name'    => $actor?->name,
            'action'       => $action,
            'entity'       => $entity ? class_basename($entity) : null,
            'entity_id'    => $entity?->getKey(),
            'entity_label' => $entity ? $this->resolveLabel($entity) : null,
            'old_data'     => $oldData,
            'new_data'     => $newData,
            'description'  => $description,
            'ip_address'   => Request::ip(),
            'user_agent'   => Request::userAgent(),
            'module'       => $module ?? ($entity ? class_basename($entity) : null),
        ]);
    }

    /**
     * Log a model creation.
     */
    public function created(Model $model, ?string $module = null): AuditLog
    {
        return $this->log('create', null, $model, null, $model->toArray(), null, $module);
    }

    /**
     * Log a model update, automatically computing diff.
     */
    public function updated(Model $model, array $original, ?string $module = null): AuditLog
    {
        $changed = collect($model->getChanges())
            ->keys()
            ->mapWithKeys(fn($key) => [$key => [
                'from' => $original[$key] ?? null,
                'to'   => $model->getAttribute($key),
            ]])
            ->toArray();

        return $this->log('update', null, $model, $original, $changed, null, $module);
    }

    /**
     * Log a model soft-deletion.
     */
    public function deleted(Model $model, ?string $reason = null, ?string $module = null): AuditLog
    {
        return $this->log('delete', null, $model, $model->toArray(), null, $reason, $module);
    }

    /**
     * Attempt to resolve a human-readable label from a model.
     */
    private function resolveLabel(Model $model): string
    {
        if (method_exists($model, 'getFullNameAttribute')) {
            return $model->full_name;
        }

        foreach (['name', 'title', 'admission_no', 'invoice_no', 'receipt_no'] as $field) {
            if (isset($model->{$field})) {
                return (string) $model->{$field};
            }
        }

        return class_basename($model) . ' #' . $model->getKey();
    }
}
