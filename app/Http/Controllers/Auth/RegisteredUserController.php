<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Clinics\Clinic\Clinic;
use App\Models\User;
use App\Notifications\Auth\CompleteRegistration;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    private const REGISTRATION_LINK_EXPIRATION_MINUTES = 60;

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
        $validated = $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        ]);

        $url = URL::temporarySignedRoute(
            'register.complete',
            now()->addMinutes(self::REGISTRATION_LINK_EXPIRATION_MINUTES),
            ['email' => $validated['email']],
        );

        Notification::route('mail', $validated['email'])
            ->notify(new CompleteRegistration($url));

        return back()->with('status', 'Enviamos um link de cadastro para o seu e-mail. Acesse o link recebido para criar sua senha e iniciar o teste grátis.');
    }

    public function complete(Request $request): View|RedirectResponse
    {
        $email = (string) $request->query('email');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            abort(404);
        }

        if (User::where('email', $email)->exists()) {
            return redirect()
                ->route('login')
                ->with('status', 'Este e-mail já possui cadastro. Faça login para continuar.');
        }

        return view('auth.complete-registration', [
            'email' => $email,
        ]);
    }

    public function completeStore(Request $request): RedirectResponse
    {
        $email = (string) $request->query('email');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            abort(404);
        }

        $request->merge(['email' => $email]);

        $request->validate([
            'clinic_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::beginTransaction();
        try {
            // Criar a Clínica (Tenant)
            $clinic = Clinic::create([
                'name' => $request->clinic_name ?: $request->name,
                'trial_ends_at' => now()->addDays(Clinic::DEFAULT_TRIAL_DAYS),
            ]);

            // Criar o Usuário (Admin da Clínica)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'email_verified_at' => now(),
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

        return redirect(route('dashboard', absolute: false));
    }
}
