<?php

namespace App\Http\Controllers\Clinics\Finance;

use App\Http\Controllers\Controller;
use App\Models\Clinics\Clinic\Finance\Payable;
use App\Models\Clinics\Clinic\Finance\Receivable;
use Illuminate\Http\Request;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(
            auth()->user()->hasRole('admin-clinica')
            || auth()->user()->can('visualizar-financeiro')
            || auth()->user()->can('gerenciar-financeiro'),
            403
        );

        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->endOfMonth()->toDateString());

        $receivables = Receivable::query()
            ->whereBetween('due_date', [$dateFrom, $dateTo])
            ->where('status', '!=', 'canceled')
            ->get();

        $payables = Payable::query()
            ->whereBetween('due_date', [$dateFrom, $dateTo])
            ->where('status', '!=', 'canceled')
            ->get();

        $summary = [
            'receivable_total' => $receivables->sum('amount'),
            'received_total' => $receivables->sum('amount_received'),
            'payable_total' => $payables->sum('amount'),
            'paid_total' => $payables->sum('amount_paid'),
            'open_receivables' => $receivables->where('status', 'pending')->count(),
            'open_payables' => $payables->where('status', 'pending')->count(),
            'unreconciled_receivables' => $receivables->whereNull('reconciled_date')->count(),
            'unreconciled_payables' => $payables->whereNull('reconciled_date')->count(),
        ];

        $summary['projected_balance'] = $summary['receivable_total'] - $summary['payable_total'];
        $summary['realized_balance'] = $summary['received_total'] - $summary['paid_total'];

        return view('finance.reports.index', compact('dateFrom', 'dateTo', 'summary'));
    }
}
