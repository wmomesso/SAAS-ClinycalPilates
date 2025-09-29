<?php

namespace App\Http\Controllers\Clinics\RolesPermissions;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Role;

class ClinicUserController extends Controller
{
    /**
     * Exibe uma lista dos usuários da clínica logada.
     */
    public function index()
    {
        $clinicId = Auth::user()->clinic_id;

        // Garante que apenas usuários da mesma clínica sejam listados.
        $users = User::where('clinic_id', $clinicId)->get();

        return view('saas.clinics.users.index', compact('users'));
    }

    /**
     * Mostra o formulário para criar um novo usuário na clínica.
     */
    public function create()
    {
        // Pega apenas os perfis que podem ser atribuídos dentro de uma clínica
        $roles = Role::whereNotIn('name', ['super-admin', 'paciente'])->get();
        return view('saas.clinics.users.create', compact('roles'));
    }

    /**
     * Armazena um novo usuário no banco de dados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'clinic_id' => Auth::user()->clinic_id, // Associa à clínica do admin logado
        ]);

        $user->assignRole($request->role);

        Alert::success('Sucesso!', 'Usuário cadastrado com sucesso.');

        return redirect()->route('clinic-users.index')->with('success', 'Usuário criado com sucesso.');
    }

    // Implementar métodos edit, update, destroy seguindo a mesma lógica...
}
