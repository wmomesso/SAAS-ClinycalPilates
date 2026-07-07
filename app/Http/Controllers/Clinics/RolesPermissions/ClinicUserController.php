<?php

namespace App\Http\Controllers\Clinics\RolesPermissions;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class ClinicUserController extends Controller
{
    private const MANAGEABLE_ROLES = ['admin-clinica', 'profissional', 'recepcionista', 'paciente'];

    public function __construct()
    {
        $this->authorizeResource(User::class, 'clinic_user');
    }

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
        // Pega os perfis permitidos para a clínica
        $roles = Role::whereIn('name', self::MANAGEABLE_ROLES)->get();

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
            'role' => ['required', 'string', Rule::in(self::MANAGEABLE_ROLES)],
            'calendar_color' => 'nullable|string|max:7',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'clinic_id' => Auth::user()->clinic_id, // Associa à clínica do admin logado
            'calendar_color' => $request->calendar_color,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('clinic-users.index')->with('success', 'Usuário criado com sucesso.');
    }

    // edit
    public function edit(User $clinic_user)
    {
        $user = $clinic_user;
        $roles = Role::whereIn('name', self::MANAGEABLE_ROLES)->get();

        return view('saas.clinics.users.edit', compact('user', 'roles'));
    }

    // update
    public function update(User $clinic_user, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$clinic_user->id,
            'password' => 'nullable|string|min:8',
            'role' => ['required', 'string', Rule::in(self::MANAGEABLE_ROLES)],
            'calendar_color' => 'nullable|string|max:7',
        ]);

        $clinic_user->name = $request->name;
        $clinic_user->email = $request->email;
        $clinic_user->calendar_color = $request->calendar_color;

        // Só atualiza a senha se foi fornecida
        if ($request->filled('password')) {
            $clinic_user->password = bcrypt($request->password);
        }

        $clinic_user->save();
        $clinic_user->syncRoles([$request->role]);

        return redirect()->route('clinic-users.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function show(User $clinic_user)
    {
        $user = $clinic_user;
        $roles = Role::whereIn('name', self::MANAGEABLE_ROLES)->get();

        return view('saas.clinics.users.show', compact('user', 'roles'));
    }

    public function destroy(User $clinic_user)
    {
        $clinic_user->delete();

        return redirect()->route('clinic-users.index')->with('success', 'Usuário removido com sucesso.');
    }
}
