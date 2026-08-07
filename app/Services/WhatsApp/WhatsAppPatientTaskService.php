<?php

namespace App\Services\WhatsApp;

use App\Jobs\ProcessWhatsAppPatientTaskJob;
use App\Models\WhatsAppPatientTask;

class WhatsAppPatientTaskService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes, bool $dispatch = true): WhatsAppPatientTask
    {
        if (isset($attributes['deduplication_key'])) {
            $attributes['deduplication_key'] = hash('sha256', (string) $attributes['deduplication_key']);
        }

        $identity = array_filter([
            'webhook_event_id' => $attributes['webhook_event_id'] ?? null,
            'deduplication_key' => $attributes['deduplication_key'] ?? null,
        ], static fn (mixed $value): bool => $value !== null);
        $values = $attributes + [
            'status' => WhatsAppPatientTask::STATUS_PENDING,
            'available_at' => now(),
            'max_attempts' => 5,
        ];

        $task = $identity === []
            ? WhatsAppPatientTask::query()->create($values)
            : WhatsAppPatientTask::query()->firstOrCreate($identity, $values);

        if ($dispatch && $task->wasRecentlyCreated) {
            ProcessWhatsAppPatientTaskJob::dispatch($task->id)->afterCommit();
        }

        return $task;
    }

    public function dispatch(WhatsAppPatientTask $task): void
    {
        ProcessWhatsAppPatientTaskJob::dispatch($task->id)->afterCommit();
    }
}
