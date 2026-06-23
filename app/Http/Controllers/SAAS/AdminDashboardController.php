<?php

namespace App\Http\Controllers\SAAS;

use App\Http\Controllers\Controller;
use App\Models\Clinics\Clinic\Clinic;
use App\Models\SAAS\SubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $totalClinics = Clinic::count();
        $totalPlans = SubscriptionPlan::count();

        // Adimplentes vs Inadimplentes
        $activeClinics = Clinic::whereHas('subscriptions', function ($query) {
            $query->where('stripe_status', 'active');
        })->count();

        $delinquentClinics = Clinic::whereHas('subscriptions', function ($query) {
            $query->whereIn('stripe_status', ['past_due', 'unpaid', 'incomplete']);
        })->count();

        $inactiveClinics = $totalClinics - $activeClinics;

        // Últimos clientes
        $latestClinics = Clinic::with('owner')->latest()->take(5)->get();

        // Faturamento últimos 3 meses (Exemplo com lógica de fallback)
        $revenueData = $this->getRevenueData();

        // Recorrência por plano
        $plansStats = SubscriptionPlan::withCount(['subscriptions' => function ($query) {
            $query->where('stripe_status', 'active');
        }])->get();

        return view('admin.dashboard', compact(
            'totalClinics',
            'totalPlans',
            'activeClinics',
            'inactiveClinics',
            'delinquentClinics',
            'latestClinics',
            'revenueData',
            'plansStats'
        ));
    }

    private function getRevenueData(): array
    {
        $months = collect();
        $revenue = collect();

        for ($i = 2; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->format('M/Y'));

            // Aqui buscaríamos faturas reais do Stripe se possível
            // Como é um dashboard admin global, precisaríamos iterar ou ter uma tabela local de faturas pagas
            // Por enquanto, usaremos dados simulados baseados nas assinaturas ativas para demonstração
            // No futuro, isso deve ser substituído por uma consulta à API do Stripe ou webhook que popula uma tabela local
            $simulatedRevenue = Clinic::whereHas('subscriptions', function ($query) use ($date) {
                $query->where('stripe_status', 'active')
                    ->where('created_at', '<=', $date->endOfMonth());
            })->count() * 99.90; // Exemplo: 99.90 por clínica

            $revenue->push($simulatedRevenue);
        }

        return [
            'labels' => $months->toArray(),
            'data' => $revenue->toArray(),
        ];
    }
}
