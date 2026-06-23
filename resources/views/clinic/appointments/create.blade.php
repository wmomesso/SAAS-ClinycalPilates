<div>
    <!-- Act only according to that maxim whereby you can, at the same time, will that it should become a universal law. - Immanuel Kant -->
</div>

@extends('layouts.saas')

@section('title', 'Novo Agendamento')

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
                    <a href="{{ route('appointments.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Agenda</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Novo Agendamento</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Criar Novo Agendamento</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Preencha os dados abaixo para agendar um novo atendimento.</p>
            </div>

            <form action="{{ route('appointments.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Paciente --}}
                    <div class="col-span-2 md:col-span-1">
                        <label for="patient_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Paciente</label>
                        <select name="patient_id" id="patient_id" required class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 rounded-xl shadow-sm">
                            <option value="">Selecione o paciente</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Profissional --}}
                    <div class="col-span-2 md:col-span-1">
                        <label for="professional_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Profissional</label>
                        <select name="professional_id" id="professional_id" required class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 rounded-xl shadow-sm">
                            <option value="">Selecione o profissional</option>
                            @foreach($professionals as $prof)
                                <option value="{{ $prof->id }}" {{ old('professional_id') == $prof->id ? 'selected' : '' }}>
                                    {{ $prof->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('professional_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Sala --}}
                    <div>
                        <label for="room_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sala</label>
                        <select name="room_id" id="room_id" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 rounded-xl shadow-sm">
                            <option value="">Selecione a sala (opcional)</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                    {{ $room->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('room_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tipo de Serviço --}}
                    <div>
                        <label for="service_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Serviço</label>
                        <select name="service_type_id" id="service_type_id" required class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 rounded-xl shadow-sm">
                            <option value="">Selecione o serviço</option>
                            @foreach($serviceTypes as $service)
                                <option value="{{ $service->id }}" {{ old('service_type_id') == $service->id ? 'selected' : '' }}>
                                    {{ $service->name }} ({{ $service->duration_in_minutes }} min)
                                </option>
                            @endforeach
                        </select>
                        @error('service_type_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Data e Hora de Início --}}
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Início</label>
                        <input type="datetime-local" name="start_time" id="start_time" required value="{{ old('start_time') }}" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 rounded-xl shadow-sm">
                        @error('start_time') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Data e Hora de Fim (Opcional) --}}
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fim (Opcional)</label>
                        <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time') }}" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 rounded-xl shadow-sm">
                        <p class="mt-1 text-[10px] text-gray-500">Se deixado em branco, será calculado com base na duração do serviço.</p>
                        @error('end_time') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Recorrência --}}
                    <div class="col-span-2 border-t border-gray-100 dark:border-gray-700 pt-4 mt-2">
                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4">Regras de Recorrência</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="recurrence_rule" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Frequência</label>
                                <select name="recurrence_rule" id="recurrence_rule" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 rounded-xl shadow-sm">
                                    <option value="none">Sem recorrência</option>
                                    <option value="daily" {{ old('recurrence_rule') == 'daily' ? 'selected' : '' }}>Diária</option>
                                    <option value="weekly" {{ old('recurrence_rule') == 'weekly' ? 'selected' : '' }}>Semanal (1x na semana)</option>
                                    <option value="2x_weekly" {{ old('recurrence_rule') == '2x_weekly' ? 'selected' : '' }}>2x na semana</option>
                                    <option value="3x_weekly" {{ old('recurrence_rule') == '3x_weekly' ? 'selected' : '' }}>3x na semana</option>
                                </select>
                                @error('recurrence_rule') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div id="recurrence_until_container" style="{{ old('recurrence_rule') && old('recurrence_rule') != 'none' ? '' : 'display: none;' }}">
                                <label for="recurrence_until" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Repetir até</label>
                                <input type="date" name="recurrence_until" id="recurrence_until" value="{{ old('recurrence_until') }}" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 rounded-xl shadow-sm">
                                @error('recurrence_until') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Observações --}}
                    <div class="col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observações</label>
                        <textarea name="notes" id="notes" rows="3" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 rounded-xl shadow-sm">{{ old('notes') }}</textarea>
                        @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <a href="{{ route('appointments.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="px-4 py-2 bg-primary-600 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-widest hover:bg-primary-700 transition ease-in-out duration-150 shadow-lg shadow-primary-500/30">
                        Confirmar Agendamento
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('recurrence_rule').addEventListener('change', function() {
            const container = document.getElementById('recurrence_until_container');
            if (this.value !== 'none') {
                container.style.display = 'block';
                document.getElementById('recurrence_until').setAttribute('required', 'required');
            } else {
                container.style.display = 'none';
                document.getElementById('recurrence_until').removeAttribute('required');
            }
        });
    </script>
@endsection
