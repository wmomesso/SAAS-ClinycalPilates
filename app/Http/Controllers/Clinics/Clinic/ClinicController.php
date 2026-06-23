<?php

namespace App\Http\Controllers\Clinics\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Clinics\Clinic\Clinic;
use App\Rules\Cpf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

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

        if (! $clinic && Auth::user()->hasRole('super-admin')) {
            return redirect()->route('admin.dashboard');
        }

        // Aplica um policy para garantir que apenas o admin da clínica pode ver as configurações
        $this->authorize('update', $clinic);

        $roles = Role::whereIn('name', ['admin-clinica', 'profissional', 'recepcionista', 'paciente'])->get();

        return view('saas.clinics.clinic.settings', compact('clinic', 'roles'));
    }

    /**
     * Atualiza as informações da clínica.
     */
    public function update(Request $request)
    {
        $clinic = Auth::user()->clinic;

        if (! $clinic) {
            abort(404, 'Clínica não encontrada.');
        }

        $this->authorize('update', $clinic);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:255|unique:clinics,subdomain,'.$clinic->id,
            'document' => ['nullable', 'string', 'max:20', new Cpf],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $validatedData['logo_path'] = $path;
        }

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
