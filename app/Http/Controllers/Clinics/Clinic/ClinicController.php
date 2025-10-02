<?php

namespace App\Http\Controllers\Clinics\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Clinics\Clinic\Clinic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ClinicController extends Controller
{
    use AuthorizesRequests;
    /**
     * Exibe a página de configurações da clínica do usuário logado.
     * Acessível pelo 'admin-clinica'.
     */
    public function settings()
    {
        $clinic = Auth::user()->clinic;

        // Aplica um policy para garantir que apenas o admin da clínica pode ver as configurações
        $this->authorize('update', $clinic);

        return view('saas.clinics.clinic.settings', compact('clinic'));
    }

    /**
     * Atualiza as informações da clínica.
     */
    public function update(Request $request, Clinic $clinic)
    {
        $this->authorize('update', $clinic);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $clinic->update($validatedData);

        return redirect()->route('clinic.settings')->with('success', 'Informações da clínica atualizadas.');
    }

    /**
     * Exemplo de método para o Super Admin listar todas as clínicas.
     */
    public function index()
    {
        // Garante que apenas o super-admin possa acessar esta rota/método
        $this->authorize('viewAny', Clinic::class);

        $clinics = Clinic::with('owner')->get();

        return view('admin.clinics.index', compact('clinics'));
    }
}
