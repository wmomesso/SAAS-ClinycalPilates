<?php

namespace App\Observers;

use App\Models\SecurityAuditLog;
use Illuminate\Database\Eloquent\Model;

class SecurityAuditObserver
{
    public function created(Model $model): void
    {
        SecurityAuditLog::record('created', $model, [
            'fields' => $this->safeFields(array_keys($model->getAttributes())),
        ]);
    }

    public function updated(Model $model): void
    {
        $fields = $this->safeFields(array_keys($model->getChanges()));

        if ($fields !== []) {
            SecurityAuditLog::record('updated', $model, ['fields' => $fields]);
        }
    }

    public function deleted(Model $model): void
    {
        SecurityAuditLog::record('deleted', $model);
    }

    public function restored(Model $model): void
    {
        SecurityAuditLog::record('restored', $model);
    }

    private function safeFields(array $fields): array
    {
        return array_values(array_diff($fields, [
            'password',
            'remember_token',
            'updated_at',
        ]));
    }
}
