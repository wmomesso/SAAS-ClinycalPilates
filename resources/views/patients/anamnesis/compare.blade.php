@extends('layouts.saas')

@section('title', 'Comparação de Anamneses: ' . $patient->full_name)

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Comparação de Anamneses</h2>
        <a href="{{ route('patients.show', $patient) }}" class="text-primary-600 hover:underline">Voltar ao prontuário</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($anamneses as $anamnesis)
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
                <div class="border-b border-gray-100 dark:border-gray-700 pb-4 mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Avaliação em {{ $anamnesis->created_at->format('d/m/Y') }}</h3>
                    <p class="text-sm text-gray-500">Profissional: {{ $anamnesis->professional->name }}</p>
                </div>

                <div class="space-y-6">
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Queixa Principal</h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-xl">
                            {{ $anamnesis->main_complaint }}
                        </p>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Histórico da Doença Atual (HDA)</h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-xl">
                            {{ $anamnesis->history_of_current_illness }}
                        </p>
                    </div>

                    @if($anamnesis->dynamic_form)
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Dados Adicionais</h4>
                            <div class="grid grid-cols-1 gap-2">
                                @foreach($anamnesis->dynamic_form as $key => $value)
                                    <div class="flex justify-between border-b border-gray-50 dark:border-gray-700 py-2">
                                        <span class="text-xs font-medium text-gray-500">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                                        <span class="text-xs text-gray-800 dark:text-gray-200">{{ is_array($value) ? implode(', ', $value) : $value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
