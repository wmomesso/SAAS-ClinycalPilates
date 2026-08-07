<?php

namespace App\Jobs;

use App\Models\WhatsAppPatientTask;
use App\Services\WhatsApp\WhatsAppPatientTaskProcessor;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class ProcessWhatsAppPatientTaskJob implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    /** @var list<int> */
    public array $backoff = [60, 300, 900, 1800];

    public function __construct(public readonly int $taskId) {}

    public function handle(WhatsAppPatientTaskProcessor $processor): void
    {
        $claimed = WhatsAppPatientTask::query()
            ->whereKey($this->taskId)
            ->whereIn('status', [WhatsAppPatientTask::STATUS_PENDING, WhatsAppPatientTask::STATUS_RETRYING])
            ->whereColumn('attempts', '<', 'max_attempts')
            ->update([
                'status' => WhatsAppPatientTask::STATUS_PROCESSING,
                'attempts' => DB::raw('attempts + 1'),
                'started_at' => now(),
                'last_error' => null,
            ]);

        if ($claimed === 0) {
            return;
        }

        $task = WhatsAppPatientTask::query()->findOrFail($this->taskId);

        try {
            $processor->process($task);
        } catch (Throwable $exception) {
            if ($task->attempts >= $task->max_attempts) {
                $task->update([
                    'status' => WhatsAppPatientTask::STATUS_FAILED,
                    'failed_at' => now(),
                    'last_error' => Str::limit($exception->getMessage(), 2000),
                ]);
                $this->fail($exception);

                return;
            }

            $task->update([
                'status' => WhatsAppPatientTask::STATUS_RETRYING,
                'available_at' => now()->addMinutes(5),
                'last_error' => Str::limit($exception->getMessage(), 2000),
            ]);

            throw $exception;
        }
    }

    public function failed(?Throwable $exception): void
    {
        WhatsAppPatientTask::query()->find($this->taskId)?->update([
            'status' => WhatsAppPatientTask::STATUS_FAILED,
            'failed_at' => now(),
            'last_error' => $exception === null ? 'Falha desconhecida.' : Str::limit($exception->getMessage(), 2000),
        ]);
    }
}
