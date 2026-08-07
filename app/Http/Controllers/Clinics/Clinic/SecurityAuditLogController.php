<?php

namespace App\Http\Controllers\Clinics\Clinic;

use App\Http\Controllers\Controller;
use App\Models\SecurityAuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SecurityAuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', SecurityAuditLog::class);

        $logs = SecurityAuditLog::query()
            ->with('user:id,name')
            ->when($request->filled('event'), fn ($query) => $query->where('event', $request->string('event')))
            ->latest('created_at')
            ->paginate(30)
            ->withQueryString();

        return view('clinic.security.audit-logs.index', compact('logs'));
    }
}
