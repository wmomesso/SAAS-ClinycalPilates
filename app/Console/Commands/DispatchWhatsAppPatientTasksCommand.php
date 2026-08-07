<?php

namespace App\Console\Commands;

use App\Models\WhatsAppPatientTask;
use App\Services\WhatsApp\WhatsAppPatientTaskService;
use Illuminate\Console\Command;

class DispatchWhatsAppPatientTasksCommand extends Command
{
    protected $signature = 'whatsapp:dispatch-patient-tasks {--limit= : Quantidade máxima a despachar}';

    protected $description = 'Despacha tarefas pendentes e recupera tarefas de WhatsApp interrompidas';

    public function handle(WhatsAppPatientTaskService $tasks): int
    {
        $staleMinutes = max((int) config('whatsapp.patients.stale_task_minutes', 10), 1);
        $limit = min(max((int) ($this->option('limit') ?: config('whatsapp.patients.dispatch_batch_size', 100)), 1), 1000);

        WhatsAppPatientTask::query()
            ->where('status', WhatsAppPatientTask::STATUS_PROCESSING)
            ->where('started_at', '<=', now()->subMinutes($staleMinutes))
            ->whereColumn('attempts', '>=', 'max_attempts')
            ->update([
                'status' => WhatsAppPatientTask::STATUS_FAILED,
                'failed_at' => now(),
                'last_error' => null,
            ]);

        WhatsAppPatientTask::query()
            ->where('status', WhatsAppPatientTask::STATUS_PROCESSING)
            ->where('started_at', '<=', now()->subMinutes($staleMinutes))
            ->whereColumn('attempts', '<', 'max_attempts')
            ->update([
                'status' => WhatsAppPatientTask::STATUS_RETRYING,
                'available_at' => now(),
                'last_error' => null,
            ]);

        WhatsAppPatientTask::query()
            ->whereIn('status', [WhatsAppPatientTask::STATUS_PENDING, WhatsAppPatientTask::STATUS_RETRYING])
            ->whereColumn('attempts', '>=', 'max_attempts')
            ->update([
                'status' => WhatsAppPatientTask::STATUS_FAILED,
                'failed_at' => now(),
                'last_error' => null,
            ]);

        $pending = WhatsAppPatientTask::query()
            ->whereIn('status', [WhatsAppPatientTask::STATUS_PENDING, WhatsAppPatientTask::STATUS_RETRYING])
            ->where('available_at', '<=', now())
            ->whereColumn('attempts', '<', 'max_attempts')
            ->orderBy('priority')
            ->orderBy('id')
            ->limit($limit)
            ->get();

        foreach ($pending as $task) {
            $tasks->dispatch($task);
        }

        $this->info("{$pending->count()} tarefa(s) despachada(s).");

        return self::SUCCESS;
    }
}
