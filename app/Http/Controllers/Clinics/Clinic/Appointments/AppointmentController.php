<?php

namespace App\Http\Controllers\Clinics\Clinic\Appointments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinics\Clinic\Appointment\StoreAppointmentRequest;
use App\Models\Clinics\Clinic\Appointment\Appointment;
use App\Models\Clinics\Clinic\Patient\Patient;
use App\Models\Clinics\Clinic\Room\Room;
use App\Models\Clinics\Clinic\Services\ServiceType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AppointmentController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Appointment::class, 'appointment');
    }

    /**
     * Exibe a Agenda (Vista de Calendário).
     */
    public function index(Request $request)
    {
        $clinicId = Auth::user()->clinic_id;

        // Filtros para a agenda (por profissional, data ou sala)
        $query = Appointment::where('clinic_id', $clinicId)
            ->with(['patient', 'professional', 'room', 'serviceType']);

        if ($request->has('professional_id')) {
            $query->where('professional_id', $request->professional_id);
        }

        $appointments = $query->get();

        // Dados auxiliares para os filtros da agenda
        $professionals = User::where('clinic_id', $clinicId)->role('profissional')->get();
        $rooms = Room::where('clinic_id', $clinicId)->where('is_active', true)->get();

        return view('clinic.appointments.index', compact('appointments', 'professionals', 'rooms'));
    }

    /**
     * Armazena um novo agendamento.
     */
    public function store(StoreAppointmentRequest $request)
    {
        $data = $request->validated();
        $data['clinic_id'] = Auth::user()->clinic_id;

        // Lógica simples para calcular o end_time se não for enviado
        if (!isset($data['end_time'])) {
            $service = ServiceType::find($data['service_type_id']);
            $start = new \Carbon\Carbon($data['start_time']);
            $data['end_time'] = $start->addMinutes($service->duration_in_minutes ?? 60);
        }

        // TODO: Implementar verificação de conflito de horário aqui

        Appointment::create($data);

        return redirect()->route('appointments.index')
            ->with('success', 'Agendamento realizado com sucesso.');
    }

    /**
     * Atualiza o status do agendamento (Confirmado, Cancelado, etc).
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment);

        $request->validate(['status' => 'required|string']);

        $appointment->update(['status' => $request->status]);

        return response()->json(['message' => 'Status atualizado.']);
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointments.index')
            ->with('success', 'Agendamento removido.');
    }
}
