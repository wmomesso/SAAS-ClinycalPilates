<?php

namespace App\Http\Controllers\Clinics\Clinic\Appointments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinics\Clinic\Appointment\StoreAppointmentRequest;
use App\Models\Clinics\Clinic\Appointment\Appointment;
use App\Models\Clinics\Clinic\Room\Room;
use App\Models\Clinics\Clinic\Services\ServiceType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $professionals = User::role('profissional')->get();
        $rooms = Room::where('is_active', true)->get();
        $serviceTypes = ServiceType::all();

        return view('clinic.appointments.create', compact('professionals', 'rooms', 'serviceTypes'));
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
                $message = 'Conflito de horário detectado para o profissional, sala ou paciente.';
                if ($request->wantsJson()) {
                    return response()->json(['message' => $message], 422);
                }

                return back()->withErrors(['start_time' => $message])
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
        $clinicId = $request->user()->clinic_id;

        $data = $request->validate([
            'patient_id' => ['sometimes', 'required', Rule::exists('patients', 'id')->where(fn ($query) => $query->where('clinic_id', $clinicId)->whereNull('deleted_at'))],
            'professional_id' => ['sometimes', 'required', Rule::exists('users', 'id')->where(fn ($query) => $query->where('clinic_id', $clinicId)->where('is_active', true))],
            'room_id' => ['sometimes', 'nullable', Rule::exists('rooms', 'id')->where(fn ($query) => $query->where('clinic_id', $clinicId)->where('is_active', true))],
            'service_type_id' => ['sometimes', 'required', Rule::exists('service_types', 'id')->where(fn ($query) => $query->where('clinic_id', $clinicId)->where('is_active', true))],
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
            'notes' => 'nullable|string',
        ]);

        // Verificar conflito excluindo o atual
        // Carregamos o objeto atual para garantir que temos os dados necessários para a verificação de conflito
        $conflictData = [
            'patient_id' => $data['patient_id'] ?? $appointment->patient_id,
            'professional_id' => $data['professional_id'] ?? $appointment->professional_id,
            'room_id' => array_key_exists('room_id', $data) ? $data['room_id'] : $appointment->room_id,
            'start_time' => $data['start_time'] ?? $appointment->start_time->format('Y-m-d H:i:s'),
            'end_time' => $data['end_time'] ?? $appointment->end_time->format('Y-m-d H:i:s'),
        ];

        if ($this->hasConflict($conflictData, $appointment->id)) {
            \Illuminate\Support\Facades\Log::warning('Conflito de horário detectado ao atualizar agendamento.', [
                'appointment_id' => $appointment->id,
                'changed_fields' => array_keys($data),
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
            'changed_fields' => array_keys($data),
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
        // 1. Verificação de conflito para o PROFISSIONAL
        // Regra: Um profissional pode atender múltiplos pacientes se estiverem na MESMA SALA no MESMO HORÁRIO.
        // Se houver um agendamento do profissional em uma SALA DIFERENTE no mesmo horário, há conflito.
        $professionalConflict = Appointment::where('professional_id', $data['professional_id'])
            ->where('room_id', '!=', $data['room_id'])
            ->where(function ($query) use ($data) {
                $query->where('start_time', '<', $data['end_time'])
                    ->where('end_time', '>', $data['start_time']);
            })
            ->whereIn('status', ['scheduled', 'confirmed', 'completed']);

        if ($excludeId) {
            $professionalConflict->where('id', '!=', $excludeId);
        }

        if ($professionalConflict->exists()) {
            return true;
        }

        // 2. Verificação de conflito para a SALA
        // Regra: A sala não pode exceder sua capacidade de alunos simultâneos.
        $room = Room::find($data['room_id']);
        if ($room) {
            $capacity = $room->capacity ?? 1;

            $roomAppointmentsCount = Appointment::where('room_id', $data['room_id'])
                ->where(function ($query) use ($data) {
                    $query->where('start_time', '<', $data['end_time'])
                        ->where('end_time', '>', $data['start_time']);
                })
                ->whereIn('status', ['scheduled', 'confirmed', 'completed']);

            if ($excludeId) {
                $roomAppointmentsCount->where('id', '!=', $excludeId);
            }

            if ($roomAppointmentsCount->count() >= $capacity) {
                return true;
            }
        }

        // 3. Verificação de conflito para o PACIENTE
        // Regra: Um paciente não pode ter dois agendamentos sobrepostos.
        $patientConflict = Appointment::where('patient_id', $data['patient_id'])
            ->where(function ($query) use ($data) {
                $query->where('start_time', '<', $data['end_time'])
                    ->where('end_time', '>', $data['start_time']);
            })
            ->whereIn('status', ['scheduled', 'confirmed', 'completed']);

        if ($excludeId) {
            $patientConflict->where('id', '!=', $excludeId);
        }

        if ($patientConflict->exists()) {
            return true;
        }

        return false;
    }

    protected function generateRecurrences(Appointment $mainAppointment, array $data): void
    {
        $until = (new \Carbon\Carbon($data['recurrence_until']))->endOfDay();
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
        $this->authorize('update', $appointment);

        $request->validate([
            'status' => 'required|string|in:scheduled,confirmed,completed,canceled,no_show',
            'notes' => $request->status === 'canceled' ? 'required|string|min:3' : 'nullable|string',
        ], [
            'notes.required' => 'O motivo do cancelamento é obrigatório.',
        ]);

        $oldStatus = $appointment->status;
        $newStatus = $request->status;
        $packageWasProcessed = false;

        \Illuminate\Support\Facades\DB::transaction(function () use ($appointment, $oldStatus, $newStatus, $request, &$packageWasProcessed) {
            if ($oldStatus !== $newStatus && $this->isPackageBillableStatus($oldStatus)) {
                $this->releasePackageSession($appointment, $oldStatus);
            }

            $updateData = [
                'status' => $newStatus,
            ];

            if ($oldStatus !== $newStatus) {
                $updateData['patient_package_id'] = null;
            }

            if ($request->filled('notes')) {
                $prefix = match ($newStatus) {
                    'canceled' => 'Cancelado: ',
                    'no_show' => 'Falta: ',
                    default => '',
                };
                $note = $prefix.$request->notes;
                $updateData['notes'] = $appointment->notes ? $appointment->notes."\n".$note : $note;
            }

            if ($oldStatus !== $newStatus && $this->isPackageBillableStatus($newStatus)) {
                $package = $this->consumePackageSession($appointment, $newStatus);
                if ($package) {
                    $updateData['patient_package_id'] = $package->id;
                    $packageWasProcessed = true;
                }
            }

            $appointment->update($updateData);
        });

        $message = 'Status atualizado com sucesso.';

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'package_processed' => $packageWasProcessed,
            ]);
        }

        return back()->with('success', $message);
    }

    public function destroy(Appointment $appointment)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($appointment) {
            if ($this->isPackageBillableStatus($appointment->status)) {
                $this->releasePackageSession($appointment, $appointment->status);
            }

            $appointment->delete();
        });

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Agendamento desmarcado com sucesso.']);
        }

        return redirect()->route('appointments.index')
            ->with('success', 'Agendamento removido.');
    }

    protected function isPackageBillableStatus(?string $status): bool
    {
        return in_array($status, ['completed', 'no_show'], true);
    }

    protected function packageSessionColumn(string $status): string
    {
        return $status === 'no_show' ? 'missed_sessions' : 'used_sessions';
    }

    protected function consumePackageSession(Appointment $appointment, string $status): ?\App\Models\Clinics\Clinic\Finance\PatientPackage
    {
        $package = \App\Models\Clinics\Clinic\Finance\PatientPackage::query()
            ->where('patient_id', $appointment->patient_id)
            ->where('status', 'active')
            ->whereDate('start_date', '<=', $appointment->start_time->toDateString())
            ->where(function ($query) use ($appointment) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $appointment->start_time->toDateString());
            })
            ->whereRaw('(used_sessions + missed_sessions) < total_sessions')
            ->when($appointment->service_type_id, function ($query) use ($appointment) {
                $query->whereHas('package', function ($packageQuery) use ($appointment) {
                    $packageQuery->where('service_type_id', $appointment->service_type_id);
                });
            })
            ->orderBy('start_date')
            ->lockForUpdate()
            ->first();

        if (! $package) {
            return null;
        }

        $package->increment($this->packageSessionColumn($status));
        $package->refresh();
        $this->refreshPackageStatus($package);

        return $package;
    }

    protected function releasePackageSession(Appointment $appointment, string $status): void
    {
        $package = $appointment->patientPackage()
            ->lockForUpdate()
            ->first();

        if (! $package) {
            return;
        }

        $column = $this->packageSessionColumn($status);
        if ($package->{$column} > 0) {
            $package->decrement($column);
        }

        $package->refresh();
        $this->refreshPackageStatus($package);

        $appointment->forceFill(['patient_package_id' => null])->save();
    }

    protected function refreshPackageStatus(\App\Models\Clinics\Clinic\Finance\PatientPackage $package): void
    {
        $consumedSessions = (int) $package->used_sessions + (int) $package->missed_sessions;
        $status = $consumedSessions >= (int) $package->total_sessions ? 'completed' : 'active';

        if ($package->status !== $status) {
            $package->update(['status' => $status]);
        }
    }
}
