<?php

namespace App\Http\Controllers\Clinics\RolesPermissions;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class ClinicUserController extends Controller
{
    private const MANAGEABLE_ROLES = ['admin-clinica', 'profissional', 'recepcionista', 'paciente'];

    private const ROLE_LIMITS = [
        'profissional' => 'limit_professionals',
        'recepcionista' => 'limit_secretaries',
    ];

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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', 'string', Rule::in(self::MANAGEABLE_ROLES)],
            'calendar_color' => 'nullable|string|max:7',
        ]);

        if ($this->roleLimitReached($validated['role'])) {
            return back()
                ->withErrors(['role' => 'O limite desse perfil no plano contratado foi atingido.'])
                ->withInput();
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'clinic_id' => Auth::user()->clinic_id, // Associa à clínica do admin logado
            'calendar_color' => $validated['calendar_color'] ?? null,
        ]);

        $user->assignRole($validated['role']);
        event(new Registered($user));

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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$clinic_user->id,
            'password' => 'nullable|string|min:8',
            'role' => ['required', 'string', Rule::in(self::MANAGEABLE_ROLES)],
            'calendar_color' => 'nullable|string|max:7',
            'is_active' => ['required', 'boolean'],
        ]);

        if (! $clinic_user->hasRole($validated['role']) && $this->roleLimitReached($validated['role'])) {
            return back()
                ->withErrors(['role' => 'O limite desse perfil no plano contratado foi atingido.'])
                ->withInput();
        }

        $clinic_user->name = $validated['name'];
        $clinic_user->email = $validated['email'];
        $clinic_user->calendar_color = $validated['calendar_color'] ?? null;
        $clinic_user->is_active = $validated['is_active'];

        // Só atualiza a senha se foi fornecida
        if (! empty($validated['password'])) {
            $clinic_user->password = bcrypt($validated['password']);
        }

        $clinic_user->save();
        $clinic_user->syncRoles([$validated['role']]);

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

    private function roleLimitReached(string $role): bool
    {
        $limitColumn = self::ROLE_LIMITS[$role] ?? null;
        $clinic = Auth::user()->clinic;

        if (! $limitColumn || ! $clinic) {
            return false;
        }

        $currentCount = User::where('clinic_id', $clinic->id)
            ->role($role)
            ->count();

        return $clinic->hasReachedSubscriptionLimit($limitColumn, $currentCount);
    }
}
