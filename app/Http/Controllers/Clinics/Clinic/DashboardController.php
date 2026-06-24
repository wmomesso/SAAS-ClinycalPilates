<?php

namespace App\Http\Controllers\Clinics\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Clinics\Clinic\Appointment\Appointment;
use App\Models\Clinics\Clinic\Patient\Patient;
use App\Models\Clinics\Clinic\Room\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $today = Carbon::today();

        // 1. Atendimentos agendados no dia (Eager load relationships for the list)
        $appointmentsToday = Appointment::whereDate('start_time', $today)
            ->with(['patient', 'room', 'serviceType'])
            ->get();
        $totalAppointmentsToday = $appointmentsToday->count();

        // Agrupar atendimentos por sala para a seção de próximos atendimentos
        $appointmentsByRoom = $appointmentsToday
            ->sortBy('start_time')
            ->groupBy(function ($appointment) {
                return $appointment->room->name ?? 'Sem Sala';
            });

        // 2. Percentual de uso das salas no dia
        // Lógica: (Salas com pelo menos um agendamento hoje / Total de salas ativas) * 100
        $totalRooms = Room::where('is_active', true)->count();
        $occupiedRooms = $appointmentsToday->pluck('room_id')->unique()->count();
        $roomUsagePercentage = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        // 3. Atendimentos por convênio e Particular
        $appointmentsByInsurance = $appointmentsToday->where('is_insurance', true)->count();
        $appointmentsPrivate = $appointmentsToday->where('is_insurance', false)->count();

        // Detalhado por Convênio (se houver relacionamento ou nome gravado)
        // Como o modelo Appointment tem insurance_guide_id, podemos agrupar por ele ou pelo nome do convênio via relação
        $insuranceStats = Appointment::whereDate('start_time', $today)
            ->where('is_insurance', true)
            ->with('insuranceGuide.healthInsurance')
            ->get()
            ->groupBy(function ($appointment) {
                return $appointment->insuranceGuide->healthInsurance->name ?? 'Outros';
            })
            ->map(fn ($group) => $group->count());

        // 4. Quantidade de atendimento por tipo de atendimento
        $appointmentsByType = $appointmentsToday->groupBy(function ($appointment) {
            return $appointment->serviceType->name ?? 'Não Definido';
        })->map(fn ($group) => $group->count());

        // 5. Aniversariantes do dia
        $birthdaysToday = Patient::whereMonth('birth_date', $today->month)
            ->whereDay('birth_date', $today->day)
            ->get();

        return view('saas.dashboard', compact(
            'totalAppointmentsToday',
            'roomUsagePercentage',
            'appointmentsByInsurance',
            'appointmentsPrivate',
            'insuranceStats',
            'appointmentsByType',
            'birthdaysToday',
            'appointmentsToday',
            'appointmentsByRoom'
        ));
    }
}
