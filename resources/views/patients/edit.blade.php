@extends('layouts.saas')

@section('title', 'Editar Paciente')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-white">
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('patients.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Pacientes</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Editar Cadastro</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Editar: {{ $patient->full_name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Atualize as informações do cadastro do paciente.</p>
                </div>
                <div class="flex items-center gap-2">
                    <form action="{{ route('patients.destroy', $patient) }}" method="POST" onsubmit="return confirmDelete(this, 'Tem certeza que deseja remover este paciente? Esta ação não pode ser desfeita.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-50 border border-red-200 rounded-xl font-semibold text-xs text-red-600 uppercase tracking-widest hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 transition ease-in-out duration-150">
                            Excluir Paciente
                        </button>
                    </form>
                </div>
            </div>

            <form action="{{ route('patients.update', $patient) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nome Completo --}}
                    <div class="col-span-1 md:col-span-2">
                        <x-input-label for="full_name" :value="__('Nome Completo')" />
                        <x-text-input id="full_name" name="full_name" type="text" class="mt-1 block w-full" :value="old('full_name', $patient->full_name)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('full_name')" />
                    </div>

                    {{-- CPF --}}
                    <div>
                        <x-input-label for="document_cpf" :value="__('CPF')" />
                        <x-text-input id="document_cpf" name="document_cpf" type="text" class="mt-1 block w-full" :value="old('document_cpf', $patient->document_cpf)" placeholder="000.000.000-00" data-mask="cpf" />
                        <x-input-error class="mt-2" :messages="$errors->get('document_cpf')" />
                    </div>

                    {{-- Data de Nascimento --}}
                    <div>
                        <x-input-label for="birth_date" :value="__('Data de Nascimento')" />
                        <x-text-input id="birth_date" name="birth_date" type="date" class="mt-1 block w-full" :value="old('birth_date', $patient->birth_date ? $patient->birth_date->format('Y-m-d') : '')" />
                        <x-input-error class="mt-2" :messages="$errors->get('birth_date')" />
                    </div>

                    {{-- Email --}}
                    <div>
                        <x-input-label for="email" :value="__('E-mail')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $patient->email)" />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    {{-- Telefone --}}
                    <div>
                        <x-input-label for="phone" :value="__('Telefone / WhatsApp')" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $patient->phone)" placeholder="+55 (00) 00000-0000" data-mask="phone" />
                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                    </div>

                    {{-- Contato de Emergência --}}
                    <div>
                        <x-input-label for="emergency_contact_name" :value="__('Nome Contato Emergência')" />
                        <x-text-input id="emergency_contact_name" name="emergency_contact_name" type="text" class="mt-1 block w-full" :value="old('emergency_contact_name', $patient->emergency_contact_name)" />
                        <x-input-error class="mt-2" :messages="$errors->get('emergency_contact_name')" />
                    </div>

                    {{-- Telefone de Emergência --}}
                    <div>
                        <x-input-label for="emergency_contact_phone" :value="__('Telefone Emergência')" />
                        <x-text-input id="emergency_contact_phone" name="emergency_contact_phone" type="text" class="mt-1 block w-full" :value="old('emergency_contact_phone', $patient->emergency_contact_phone)" placeholder="+55 (00) 00000-0000" data-mask="phone" />
                        <x-input-error class="mt-2" :messages="$errors->get('emergency_contact_phone')" />
                    </div>

                    {{-- Status --}}
                    <div>
                        <x-input-label for="is_active" :value="__('Status')" />
                        <select id="is_active" name="is_active" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-xl shadow-sm">
                            <option value="1" {{ old('is_active', $patient->is_active) == 1 ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ old('is_active', $patient->is_active) == 0 ? 'selected' : '' }}>Inativo</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                    </div>
                </div>

                {{-- Histórico Médico --}}
                <div class="mt-6">
                    <x-input-label for="medical_history" :value="__('Observações Iniciais / Histórico Breve')" />
                    <textarea id="medical_history" name="medical_history" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-xl shadow-sm">{{ old('medical_history', $patient->medical_history) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('medical_history')" />
                </div>

                <div class="flex items-center justify-end mt-8 gap-4">
                    <a href="{{ route('patients.show', $patient) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 underline">
                        Ver Prontuário
                    </a>
                    <x-primary-button class="bg-primary-600 hover:bg-primary-700 shadow-lg shadow-primary-500/30">
                        Atualizar Cadastro
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Máscara para CPF
            document.querySelectorAll('[data-mask="cpf"]').forEach(function(el) {
                IMask(el, {
                    mask: '000.000.000-00'
                });
            });

            // Máscara para Telefone (Dinâmica: Aceita DDI opcional e se adapta a 8 ou 9 dígitos)
            document.querySelectorAll('[data-mask="phone"]').forEach(function(el) {
                IMask(el, {
                    mask: [
                        { mask: '(00) 0000-0000' },
                        { mask: '(00) 00000-0000' },
                        { mask: '+00 (00) 0000-0000' },
                        { mask: '+00 (00) 00000-0000' },
                        { mask: '+000 (00) 0000-0000' },
                        { mask: '+000 (00) 00000-0000' },
                        { mask: /^\+?[0-9]*$/ } // Fallback para qualquer número com + opcional
                    ]
                });
            });
        });
    </script>
@endpush
