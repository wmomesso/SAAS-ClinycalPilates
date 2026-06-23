<?php

namespace App\Http\Controllers\Clinics\Clinic\Appointments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinics\Clinic\Appointment\StoreAppointmentRequest;
use App\Models\Clinics\Clinic\Appointment\Appointment;
use App\Models\Clinics\Clinic\Room\Room;
use App\Models\Clinics\Clinic\Services\ServiceType;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Appointment::class, 'appointment');
    }

    /**
     * Exibe a Agenda (Vista de Calendário).
     */
    public function index(Request $request)
    {
        // Filtros para a agenda (por profissional, data ou sala)
        $query = Appointment::with(['patient', 'professional', 'room', 'serviceType']);

        if ($request->has('professional_id') && $request->professional_id) {
            $query->where('professional_id', $request->professional_id);
        }

        if ($request->has('room_id') && $request->room_id) {
            $query->where('room_id', $request->room_id);
        }

        // Se for JSON (FullCalendar), pegamos um intervalo maior ou filtramos por data
        if ($request->wantsJson()) {
            if ($request->has('start') && $request->has('end')) {
                $query->whereBetween('start_time', [$request->start, $request->end]);
            }

            $appointments = $query->get();

            $events = $appointments->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'title' => $appointment->patient->full_name,
                    'start' => $appointment->start_time->toIso8601String(),
                    'end' => $appointment->end_time->toIso8601String(),
                    'extendedProps' => [
                        'patient_id' => $appointment->patient_id,
                        'patient_name' => $appointment->patient->full_name,
                        'professional_id' => $appointment->professional_id,
                        'professional_name' => $appointment->professional->name,
                        'room_id' => $appointment->room_id,
                        'room_name' => $appointment->room->name,
                        'service_type_id' => $appointment->service_type_id,
                        'service_name' => $appointment->serviceType->name,
                        'status' => $appointment->status,
                        'notes' => $appointment->notes,
                    ],
                    'backgroundColor' => $appointment->status === 'canceled' ? '#ef4444' : ($appointment->professional->calendar_color ?? '#3b82f6'),
                    'borderColor' => 'transparent',
                ];
            });

            return response()->json($events);
        }

        if ($request->has('date') && $request->date) {
            $query->whereDate('start_time', $request->date);
        } else {
            $query->whereDate('start_time', now()->toDateString());
        }

        $appointments = $query->orderBy('start_time')->get();

        // Dados auxiliares para os filtros da agenda
        $professionals = User::role('profissional')->get();
        $rooms = Room::where('is_active', true)->get();
        $patients = \App\Models\Clinics\Clinic\Patient\Patient::all();
        $serviceTypes = ServiceType::all();

        return view('clinic.appointments.index', compact('appointments', 'professionals', 'rooms', 'patients', 'serviceTypes'));
    }

    /**
     * Exibe o formulário de criação de agendamento.
     */
    public function create()
    {
        $patients = \App\Models\Clinics\Clinic\Patient\Patient::orderBy('full_name')->get();
        $professionals = User::role('profissional')->get();
        $rooms = Room::where('is_active', true)->get();
        $serviceTypes = ServiceType::all();

        return view('clinic.appointments.create', compact('patients', 'professionals', 'rooms', 'serviceTypes'));
    }

    /**
     * Armazena um novo agendamento.
     */
    public function store(StoreAppointmentRequest $request)
    {
        $data = $request->validated();

        return \Illuminate\Support\Facades\DB::transaction(function () use ($data, $request) {
            // Lógica simples para calcular o end_time se não for enviado
            if (! isset($data['end_time']) || empty($data['end_time'])) {
                $service = ServiceType::find($data['service_type_id']);
                $start = new \Carbon\Carbon($data['start_time']);
                $data['end_time'] = (clone $start)->addMinutes($service->duration_in_minutes ?? 60);
            }

            // Verificação de conflito de horário para o primeiro agendamento
            if ($this->hasConflict($data)) {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Já existe um agendamento para este profissional ou sala neste horário.'], 422);
                }

                return back()->withErrors(['start_time' => 'Já existe um agendamento para este profissional ou sala neste horário.'])
                    ->withInput();
            }

            $mainAppointment = Appointment::create($data);

            // Gerar recorrências se necessário
            if (isset($data['recurrence_rule']) && $data['recurrence_rule'] !== 'none') {
                $this->generateRecurrences($mainAppointment, $data);
            }

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Agendamento(s) realizado(s) com sucesso.', 'appointment' => $mainAppointment]);
            }

            return redirect()->route('appointments.index')
                ->with('success', 'Agendamento(s) realizado(s) com sucesso.');
        });
    }

    public function update(Request $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment);

        $data = $request->validate([
            'patient_id' => 'sometimes|required|exists:patients,id',
            'professional_id' => 'sometimes|required|exists:users,id',
            'room_id' => 'sometimes|required|exists:rooms,id',
            'service_type_id' => 'sometimes|required|exists:service_types,id',
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
            'notes' => 'nullable|string',
        ]);

        // Verificar conflito excluindo o atual
        // Carregamos o objeto atual para garantir que temos os dados necessários para a verificação de conflito
        $conflictData = [
            'professional_id' => $data['professional_id'] ?? $appointment->professional_id,
            'room_id' => $data['room_id'] ?? $appointment->room_id,
            'start_time' => $data['start_time'] ?? $appointment->start_time->format('Y-m-d H:i:s'),
            'end_time' => $data['end_time'] ?? $appointment->end_time->format('Y-m-d H:i:s'),
        ];

        if ($this->hasConflict($conflictData, $appointment->id)) {
            \Illuminate\Support\Facades\Log::warning('Conflito de horário detectado ao atualizar agendamento.', [
                'appointment_id' => $appointment->id,
                'data' => $data,
                'conflictData' => $conflictData,
            ]);
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Conflito de horário detectado.'], 422);
            }

            return back()->with('error', 'Conflito de horário detectado.');
        }

        if ($request->has('notes') && $request->notes != $appointment->notes) {
            $data['notes'] = $appointment->notes ? $appointment->notes."\n".$request->notes : $request->notes;
        }

        \Illuminate\Support\Facades\Log::info('Atualizando agendamento:', [
            'appointment_id' => $appointment->id,
            'data_to_update' => $data,
        ]);

        $appointment->update($data);

        // Se o horário mudou, podemos querer disparar algum evento ou lógica adicional aqui futuramente.

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Agendamento atualizado com sucesso.',
                'appointment' => $appointment,
            ]);
        }

        return redirect()->route('appointments.index')
            ->with('success', 'Agendamento reagendado com sucesso.');
    }

    protected function hasConflict(array $data, $excludeId = null): bool
    {
        $query = Appointment::where(function ($query) use ($data) {
            $query->where('professional_id', $data['professional_id'])
                ->orWhere('room_id', $data['room_id']);
        })
            ->where(function ($query) use ($data) {
                $query->where(function ($q) use ($data) {
                    $q->where('start_time', '<', $data['end_time'])
                        ->where('end_time', '>', $data['start_time']);
                });
            })
            ->whereIn('status', ['scheduled', 'confirmed', 'completed']);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    protected function generateRecurrences(Appointment $mainAppointment, array $data): void
    {
        $until = new \Carbon\Carbon($data['recurrence_until']);
        $currentStart = new \Carbon\Carbon($data['start_time']);
        $currentEnd = new \Carbon\Carbon($data['end_time']);
        $rule = $data['recurrence_rule'];

        $intervalDays = match ($rule) {
            'daily' => 1,
            'weekly' => 7,
            '2x_weekly' => 3, // Simplificado: a cada 3 e 4 dias (ex: Seg e Qui)
            '3x_weekly' => 2, // Simplificado: a cada 2 dias (ex: Seg, Qua, Sex)
            default => null,
        };

        if ($intervalDays === null) {
            return;
        }

        while (true) {
            if ($rule === '2x_weekly') {
                // Alternar entre 3 e 4 dias para 2x na semana
                $currentStart->addDays($currentStart->dayOfWeek === 1 || $currentStart->dayOfWeek === 2 ? 3 : 4);
            } elseif ($rule === '3x_weekly') {
                // Alternar para 3x na semana (Seg, Qua, Sex)
                $currentStart->addDays(2);
            } else {
                $currentStart->addDays($intervalDays);
            }

            $currentEnd = (clone $currentStart)->addMinutes($mainAppointment->start_time->diffInMinutes($mainAppointment->end_time));

            if ($currentStart->gt($until)) {
                break;
            }

            $recurrenceData = array_merge($data, [
                'start_time' => $currentStart->toDateTimeString(),
                'end_time' => $currentEnd->toDateTimeString(),
                'parent_appointment_id' => $mainAppointment->id,
            ]);

            // Se houver conflito na recorrência, apenas pulamos ou registramos log?
            // Para gestão de clínicas, geralmente se evita criar o conflito.
            if (! $this->hasConflict($recurrenceData)) {
                Appointment::create($recurrenceData);
            }
        }
    }

    /**
     * Atualiza o status do agendamento (Confirmado, Cancelado, etc).
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        // Verificar se é uma requisição de cancelamento com notes
        if ($request->has('status') && $request->status === 'canceled') {
            $this->authorize('update', $appointment);

            $request->validate([
                'notes' => 'required|string|min:3',
            ], [
                'notes.required' => 'O motivo do cancelamento é obrigatório.',
            ]);

            $appointment->update([
                'status' => 'canceled',
                'notes' => $appointment->notes ? $appointment->notes."\nCancelado: ".$request->notes : 'Cancelado: '.$request->notes,
            ]);

            return response()->json(['message' => 'Agendamento cancelado com sucesso.']);
        }

        $this->authorize('update', $appointment);

        $request->validate([
            'status' => 'required|string|in:scheduled,confirmed,completed,canceled,no_show',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $appointment->status;
        $newStatus = $request->status;

        \Illuminate\Support\Facades\DB::transaction(function () use ($appointment, $oldStatus, $newStatus, $request) {
            $updateData = ['status' => $newStatus];
            if ($request->has('notes')) {
                $updateData['notes'] = $appointment->notes ? $appointment->notes."\n".$request->notes : $request->notes;
            }
            $appointment->update($updateData);

            // Lógica de baixa de sessão em pacote se o status for alterado para 'completed'
            if ($newStatus === 'completed' && $oldStatus !== 'completed') {
                $package = \App\Models\Clinics\Clinic\Finance\PatientPackage::where('patient_id', $appointment->patient_id)
                    ->where('status', 'active')
                    ->where('used_sessions', '<', \Illuminate\Support\Facades\DB::raw('total_sessions'))
                    ->orderBy('start_date', 'asc')
                    ->first();

                if ($package) {
                    $package->increment('used_sessions');

                    // Se atingiu o limite, marca como concluído
                    if ($package->used_sessions >= $package->total_sessions) {
                        $package->update(['status' => 'completed']);
                    }
                }
            }

            // Lógica de estorno de sessão se o status 'completed' for revertido
            if ($oldStatus === 'completed' && $newStatus !== 'completed') {
                $package = \App\Models\Clinics\Clinic\Finance\PatientPackage::where('patient_id', $appointment->patient_id)
                    ->where('used_sessions', '>', 0)
                    ->orderBy('updated_at', 'desc')
                    ->first();

                if ($package) {
                    $package->decrement('used_sessions');
                    if ($package->status === 'completed') {
                        $package->update(['status' => 'active']);
                    }
                }
            }
        });

        return response()->json(['message' => 'Status atualizado e sessões processadas.']);
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('appointments.index')
            ->with('success', 'Agendamento removido.');
    }
}
