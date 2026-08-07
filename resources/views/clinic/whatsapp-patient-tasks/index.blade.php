@extends('layouts.saas')

@section('title', 'Solicitações pelo WhatsApp')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Solicitações pelo WhatsApp</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tarefas processadas em fila e solicitações que aguardam atendimento da clínica.</p>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-200">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-2 gap-3 md:grid-cols-5">
            @foreach([
                'awaiting_staff' => 'Aguardando equipe',
                'pending' => 'Pendentes',
                'processing' => 'Processando',
                'retrying' => 'Nova tentativa',
                'failed' => 'Falhas',
            ] as $value => $label)
                <a href="{{ route('whatsapp-patient-tasks.index', ['status' => $value]) }}" class="rounded-2xl border p-4 {{ $status === $value ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800' }}">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $label }}</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $counts[$value] ?? 0 }}</p>
                </a>
            @endforeach
        </div>

        <div class="overflow-hidden rounded-2xl bg-white shadow dark:bg-gray-800">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-5 py-3">Recebida</th>
                            <th class="px-5 py-3">Paciente</th>
                            <th class="px-5 py-3">Solicitação</th>
                            <th class="px-5 py-3">Agendamento</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Tentativas</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($tasks as $task)
                            @php
                                $typeLabels = [
                                    'appointment_reminder' => 'Lembrete de agendamento',
                                    'appointment_confirmation' => 'Confirmação',
                                    'cancellation_request' => 'Pedido de cancelamento',
                                    'financial_summary' => 'Consulta financeira',
                                    'appointment_list' => 'Consulta de agenda',
                                    'package_summary' => 'Consulta de aulas',
                                    'human_support' => 'Atendimento humano',
                                    'ambiguous_patient' => 'Identidade ambígua',
                                    'unidentified_patient' => 'Telefone não identificado',
                                    'missing_appointment_context' => 'Confirmação sem contexto',
                                    'menu' => 'Menu',
                                ];
                                $statusLabels = [
                                    'pending' => 'Pendente',
                                    'processing' => 'Processando',
                                    'retrying' => 'Nova tentativa',
                                    'completed' => 'Concluída',
                                    'awaiting_staff' => 'Aguardando equipe',
                                    'failed' => 'Falhou',
                                    'canceled' => 'Cancelada',
                                ];
                            @endphp
                            <tr>
                                <td class="whitespace-nowrap px-5 py-4">{{ $task->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">{{ $task->patient?->full_name ?? 'Não identificado' }}</td>
                                <td class="px-5 py-4">{{ $typeLabels[$task->type] ?? $task->type }}</td>
                                <td class="whitespace-nowrap px-5 py-4">{{ $task->appointment?->start_time?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="px-5 py-4">{{ $statusLabels[$task->status] ?? $task->status }}</td>
                                <td class="px-5 py-4">{{ $task->attempts }}/{{ $task->max_attempts }}</td>
                                <td class="px-5 py-4 text-right">
                                    @if($task->status === 'awaiting_staff')
                                        <form method="POST" action="{{ route('whatsapp-patient-tasks.complete', $task) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-lg bg-primary-600 px-3 py-2 text-xs font-semibold text-white hover:bg-primary-500">Marcar resolvida</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-5 py-10 text-center text-gray-500">Nenhuma solicitação encontrada.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $tasks->links() }}</div>
        </div>
    </div>
@endsection
