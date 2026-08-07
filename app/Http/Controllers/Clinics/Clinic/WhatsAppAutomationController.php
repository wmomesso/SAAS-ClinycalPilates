<?php

namespace App\Http\Controllers\Clinics\Clinic;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppIntegration;
use App\Services\WhatsApp\WhatsAppActivationService;
use App\Services\WhatsApp\WhatsAppPhoneNormalizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppAutomationController extends Controller
{
    public function index(Request $request, WhatsAppPhoneNormalizer $phones): View
    {
        $user = $request->user();
        abort_if($user->clinic_id === null || $user->hasRole('super-admin'), 403);

        $binding = $user->whatsAppPhoneBinding()->where('is_active', true)->first();
        $publicNumber = WhatsAppIntegration::query()
            ->where('provider', 'uazapi')
            ->where('is_active', true)
            ->value('public_number') ?: config('whatsapp.public_number');

        return view('clinic.whatsapp-automation.index', [
            'binding' => $binding,
            'maskedPhone' => $binding === null ? null : $phones->mask($binding->phone),
            'publicNumber' => $publicNumber,
            'activationCode' => session('whatsapp_activation_code'),
            'expiresAt' => session('whatsapp_activation_expires_at'),
        ]);
    }

    public function store(Request $request, WhatsAppActivationService $activations): RedirectResponse
    {
        $user = $request->user();
        abort_if($user->clinic_id === null || $user->hasRole('super-admin'), 403);

        $code = $activations->generate($user);
        $ttl = (int) config('whatsapp.activation_code_ttl_minutes', 10);

        return redirect()->route('whatsapp-automation.index')->with([
            'whatsapp_activation_code' => $code,
            'whatsapp_activation_expires_at' => now()->addMinutes($ttl)->toIso8601String(),
            'success' => 'Código temporário gerado. Envie-o pelo seu telefone ao WhatsApp do SaaS.',
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->user()->whatsAppPhoneBinding()->delete();

        return redirect()->route('whatsapp-automation.index')
            ->with('success', 'Telefone desvinculado das automações do WhatsApp.');
    }
}
