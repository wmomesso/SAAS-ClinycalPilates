<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Informações do Perfil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Atualize as informações de perfil e o endereço de e-mail da sua conta.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        {{-- Upload de Avatar --}}
        <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 border-b border-gray-100 dark:border-gray-700">
            <div class="relative group">
                <div class="w-24 h-24 rounded-3xl overflow-hidden ring-4 ring-primary-500/10 transition-all duration-300 group-hover:ring-primary-500/30">
                    <img id="avatar-preview"
                         src="{{ $user->avatar_url }}"
                         alt="{{ $user->name }}"
                         class="w-full h-full object-cover">
                </div>
                <label for="avatar" class="absolute -bottom-2 -right-2 bg-primary-600 hover:bg-primary-700 text-white p-2 rounded-xl shadow-lg cursor-pointer transition-all duration-300 hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*" onchange="previewAvatar(this)">
                </label>
            </div>
            <div class="flex-1 text-center sm:text-left">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Foto de Perfil</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    JPG, GIF ou PNG. Tamanho máximo de 2MB.
                </p>
                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-input-label for="name" :value="__('Nome')" class="text-xs font-bold uppercase tracking-wider text-gray-500" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-2xl border-gray-200 dark:border-gray-700 focus:ring-primary-500 dark:focus:ring-primary-400" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" class="text-xs font-bold uppercase tracking-wider text-gray-500" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-2xl border-gray-200 dark:border-gray-700 focus:ring-primary-500 dark:focus:ring-primary-400" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2">
                        <p class="text-sm text-gray-800 dark:text-gray-200 bg-amber-50 dark:bg-amber-900/20 p-3 rounded-xl border border-amber-100 dark:border-amber-900/30">
                            {{ __('Seu endereço de e-mail não foi verificado.') }}

                            <button form="send-verification" class="ml-1 underline text-sm text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 font-bold focus:outline-none">
                                {{ __('Clique aqui para reenviar o e-mail de verificação.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                {{ __('Um novo link de verificação foi enviado para o seu endereço de e-mail.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4">
            <x-primary-button class="rounded-2xl px-8 py-3 bg-primary-600 hover:bg-primary-700 transition-all duration-300 shadow-lg shadow-primary-500/20">
                {{ __('Salvar Alterações') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-medium text-green-600 dark:text-green-400 flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ __('Atualizado com sucesso.') }}
                </p>
            @endif
        </div>
    </form>

    <script>
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</section>
