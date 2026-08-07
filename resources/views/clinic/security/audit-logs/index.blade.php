@extends('layouts.saas')

@section('title', 'Auditoria de Segurança')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Auditoria de Segurança</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Histórico imutável de alterações e acessos sensíveis. Os valores clínicos não são copiados para o log.</p>
        </div>

        <div class="overflow-hidden rounded-2xl bg-white shadow dark:bg-gray-800">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th class="px-5 py-3">Data</th>
                            <th class="px-5 py-3">Usuário</th>
                            <th class="px-5 py-3">Evento</th>
                            <th class="px-5 py-3">Registro</th>
                            <th class="px-5 py-3">IP</th>
                            <th class="px-5 py-3">Campos</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($logs as $log)
                            <tr>
                                <td class="whitespace-nowrap px-5 py-4">{{ $log->created_at?->format('d/m/Y H:i:s') }}</td>
                                <td class="px-5 py-4">{{ $log->user?->name ?? 'Sistema' }}</td>
                                <td class="px-5 py-4 font-medium">{{ $log->event }}</td>
                                <td class="px-5 py-4">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</td>
                                <td class="px-5 py-4">{{ $log->ip_address ?? '—' }}</td>
                                <td class="px-5 py-4">{{ implode(', ', $log->metadata['fields'] ?? []) ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">Nenhum evento registrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $logs->links() }}</div>
        </div>
    </div>
@endsection
