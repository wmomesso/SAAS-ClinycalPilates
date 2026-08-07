<?php

namespace App\Http\Controllers\Clinics\Clinic;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppPatientTask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppPatientTaskController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAccess($request);

        $status = $request->string('status')->toString();
        $tasks = WhatsAppPatientTask::query()
            ->with(['patient:id,full_name', 'appointment:id,start_time'])
            ->where('clinic_id', $request->user()->clinic_id)
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $counts = WhatsAppPatientTask::query()
            ->where('clinic_id', $request->user()->clinic_id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('clinic.whatsapp-patient-tasks.index', compact('tasks', 'counts', 'status'));
    }

    public function complete(Request $request, WhatsAppPatientTask $task): RedirectResponse
    {
        $this->authorizeAccess($request);
        abort_unless($task->clinic_id === $request->user()->clinic_id, 404);
        abort_unless($task->status === WhatsAppPatientTask::STATUS_AWAITING_STAFF, 422);

        $task->update([
            'status' => WhatsAppPatientTask::STATUS_COMPLETED,
            'result' => array_merge($task->result ?? [], [
                'resolved_by' => $request->user()->id,
                'resolved_at' => now()->toIso8601String(),
            ]),
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Solicitação marcada como resolvida.');
    }

    private function authorizeAccess(Request $request): void
    {
        abort_unless(
            $request->user()->clinic_id !== null
            && $request->user()->hasAnyRole(['admin-clinica', 'recepcionista']),
            403,
        );
    }
}
