<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Clinics\Clinic\Clinic;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. ATUALIZAMOS A VALIDAÇÃO PARA INCLUIR O NOME DA CLÍNICA
        $request->validate([
            'clinic_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. ADICIONAMOS NOSSA LÓGICA DE TRANSACTION
        DB::beginTransaction();
        try {
            // Criar a Clínica (Tenant)
            $clinic = Clinic::create([
                'name' => $request->clinic_name,
            ]);

            // Criar o Usuário (Admin da Clínica)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'clinic_id' => $clinic->id, // Associa o usuário à nova clínica
            ]);

            // Atualizar a clínica com o ID do proprietário
            $clinic->owner_id = $user->id;
            $clinic->save();

            // Atribuir o perfil 'admin-clinica' ao novo usuário
            $adminRole = Role::firstOrCreate(['name' => 'admin-clinica']);
            $user->assignRole($adminRole);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            // Em caso de erro, retorna com uma mensagem de falha.
            return back()->withInput()->withErrors(['error' => 'Não foi possível completar o registro. Por favor, tente novamente.']);
        }

        // Esta parte do Breeze permanece a mesma.
        event(new Registered($user));
        Auth::login($user);

        //return redirect(RouteServiceProvider::HOME);
        return redirect(route('saas.dashboard', absolute: false));
    }
}
