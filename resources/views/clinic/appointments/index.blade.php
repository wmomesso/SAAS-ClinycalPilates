@extends('layouts.saas')

@section('title', 'Agenda')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-white">
                    Dashboard
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Agenda</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Agenda da Clínica</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gerencie os horários de atendimentos e profissionais.</p>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="openNewAppointmentModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 transition ease-in-out duration-150 shadow-lg shadow-primary-500/30">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Novo Agendamento
                </button>
            </div>
        </div>

        {{-- Filtros Rápidos por Sala --}}
        <div class="flex flex-wrap gap-2 mb-6 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-700">
            <button type="button" onclick="filterRoom(null)" class="room-filter-btn px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-primary-600 text-white" data-room-id="all">
                Todas as Salas
            </button>
            @foreach($rooms as $room)
                <button type="button" onclick="filterRoom({{ $room->id }})" class="room-filter-btn px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700" data-room-id="{{ $room->id }}">
                    {{ $room->name }}
                </button>
            @endforeach
        </div>

        {{-- Calendário --}}
        <div id="calendar-container" class="min-h-[600px] mb-8">
            <div id="calendar"></div>
        </div>

        {{-- Legenda de Profissionais --}}
        <div class="mt-8 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Legenda de Profissionais
            </h3>
            <div class="flex flex-wrap gap-4">
                @foreach($professionals as $prof)
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 rounded shadow-sm border border-gray-200 dark:border-gray-600" style="background-color: {{ $prof->calendar_color ?? '#3b82f6' }}"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $prof->name }}</span>
                    </div>
                @endforeach
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 rounded shadow-sm border border-gray-200 dark:border-gray-600 bg-[#ef4444]"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Cancelado</span>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
    {{-- Modal para Novo Agendamento / Detalhes --}}
    <div id="appointment-modal" data-modal-placement="center" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-[70] justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-2xl shadow dark:bg-gray-800">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 id="modal-title" class="text-xl font-semibold text-gray-900 dark:text-white">
                        Agendamento
                    </h3>
                    <button type="button" onclick="closeModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
                <form id="appointment-form" method="POST">
                    @csrf
                    <div id="modal-form-content" class="p-4 md:p-5 space-y-4">
                        {{-- O conteúdo será injetado via JS --}}
                    </div>
                    <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600 gap-2">
                        <button id="submit-btn" type="submit" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-xl text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Salvar</button>
                        <button id="cancel-appointment-btn" type="button" class="hidden text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-xl text-sm px-5 py-2.5 text-center dark:bg-red-500 dark:hover:bg-red-600">Cancelar Agendamento</button>
                        <button type="button" onclick="closeModal()" class="ms-3 text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 rounded-xl border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Voltar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endpush

    @push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script>
        let calendar;
        let selectedRoomId = null;
        let modal;

        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar Modal do Flowbite
            const $modalElement = document.getElementById('appointment-modal');
            if (window.Modal) {
                modal = new Modal($modalElement, {
                    backdrop: 'static',
                    closable: true,
                    onHide: () => {
                        console.log('modal is hidden');
                        // Remover backdrop residual se houver erro no Flowbite
                        const backdrop = document.querySelector('[modal-backdrop]');
                        if (backdrop) backdrop.remove();
                    },
                    onShow: () => {
                        console.log('modal is shown');
                        // Forçar o z-index do backdrop do Flowbite se ele for criado
                        setTimeout(() => {
                            const backdrop = document.querySelector('[modal-backdrop]');
                            if (backdrop) {
                                backdrop.classList.add('z-[65]');
                            }
                        }, 50);
                    },
                });
            } else {
                console.error('Flowbite Modal class not found. Make sure app.js is compiled.');
            }

            const calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridDay',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                locale: 'pt-br',
                slotMinTime: '06:00:00',
                slotMaxTime: '22:00:00',
                allDaySlot: false,
                editable: true,
                selectable: true,
                nowIndicator: true,
                businessHours: {
                    daysOfWeek: [1, 2, 3, 4, 5, 6],
                    startTime: '07:00',
                    endTime: '21:00',
                },
                events: function(fetchInfo, successCallback, failureCallback) {
                    let url = '{{ route('appointments.index') }}?start=' + fetchInfo.startStr + '&end=' + fetchInfo.endStr;
                    if (selectedRoomId) url += '&room_id=' + selectedRoomId;

                    fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    })
                    .then(response => response.json())
                    .then(data => successCallback(data))
                    .catch(error => {
                        console.error('Error fetching events:', error);
                        failureCallback(error);
                    });
                },
                select: function(info) {
                    openNewAppointmentModal(info.startStr, info.endStr);
                },
                eventClick: function(info) {
                    openEditAppointmentModal(info.event);
                },
                eventDrop: function(info) {
                    updateAppointmentTime(info.event);
                },
                eventResize: function(info) {
                    updateAppointmentTime(info.event);
                }
            });
            calendar.render();

            // Interceptar o submit do form para usar Fetch e dar feedback
            document.getElementById('appointment-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = document.getElementById('submit-btn');
                const originalText = submitBtn.innerText;
                submitBtn.disabled = true;
                submitBtn.innerText = 'Salvando...';

                const formData = new FormData(this);
                const action = this.action;

            // Formatar datas para o SQL sem conversão de fuso horário (preserva o que está no input)
            const formatDateToSQL = (dateStr) => {
                if (!dateStr) return '';
                return dateStr.replace('T', ' ') + ':00';
            };

            if (formData.has('start_time')) {
                formData.set('start_time', formatDateToSQL(formData.get('start_time')));
            }
            if (formData.has('end_time')) {
                formData.set('end_time', formatDateToSQL(formData.get('end_time')));
            }

                fetch(action, {
                    method: 'POST', // Always POST for Laravel with _method field
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(async response => {
                    const data = await response.json();
                    if (response.ok) {
                        if (modal) modal.hide();
                        calendar.refetchEvents();
                        // Reset form
                        this.reset();
                    } else {
                        let errorMessage = data.message || 'Erro ao salvar agendamento.';
                        if (data.errors) {
                            errorMessage = Object.values(data.errors).flat().join('\n');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao salvar',
                            text: errorMessage,
                            confirmButtonColor: '#2563eb',
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Erro na comunicação com o servidor.',
                        confirmButtonColor: '#2563eb',
                    });
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;
                });
            });
        });

        function updateAppointmentTime(event) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PUT');

            // Usar o formato ISO Local para evitar mudanças de fuso horário
            const offset = event.start.getTimezoneOffset() * 60000;
            const localStart = new Date(event.start.getTime() - offset).toISOString().substring(0, 19).replace('T', ' ');
            const localEnd = event.end ? new Date(event.end.getTime() - offset).toISOString().substring(0, 19).replace('T', ' ') : '';

            formData.append('start_time', localStart);
            formData.append('end_time', localEnd);

            fetch('{{ url('appointments') }}/' + event.id, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    let errorMessage = data.message || 'Erro ao mover agendamento.';
                    if (data.errors) {
                        errorMessage = Object.values(data.errors).flat().join('\n');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao mover',
                        text: errorMessage,
                        confirmButtonColor: '#2563eb',
                    });
                    calendar.refetchEvents();
                } else {
                    console.log('Agendamento atualizado com sucesso:', data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Erro na comunicação com o servidor.',
                    confirmButtonColor: '#2563eb',
                });
                calendar.refetchEvents();
            });
        }

        function filterRoom(roomId) {
            selectedRoomId = roomId;
            document.querySelectorAll('.room-filter-btn').forEach(btn => {
                if (btn.dataset.roomId == (roomId || 'all')) {
                    btn.classList.add('bg-primary-600', 'text-white');
                    btn.classList.remove('bg-white', 'text-gray-700', 'dark:bg-gray-800', 'dark:text-gray-300');
                } else {
                    btn.classList.remove('bg-primary-600', 'text-white');
                    btn.classList.add('bg-white', 'text-gray-700', 'dark:bg-gray-800', 'dark:text-gray-300');
                }
            });
            calendar.refetchEvents();
        }

        function openNewAppointmentModal(start, end) {
            document.getElementById('modal-title').innerText = 'Novo Agendamento';
            document.getElementById('appointment-form').action = '{{ route('appointments.store') }}';

            // Limpar inputs hidden de método anteriores se houver
            const existingMethod = document.getElementById('appointment-form').querySelector('input[name="_method"]');
            if (existingMethod) existingMethod.remove();

            document.getElementById('cancel-appointment-btn').classList.add('hidden');

            // Garantir que temos o formato ISO local correto para o input datetime-local
            let startVal = '';
            let endVal = '';

            if (start) {
                const startDate = new Date(start);
                startVal = new Date(startDate.getTime() - startDate.getTimezoneOffset() * 60000).toISOString().substring(0, 16);
            }
            if (end) {
                const endDate = new Date(end);
                endVal = new Date(endDate.getTime() - endDate.getTimezoneOffset() * 60000).toISOString().substring(0, 16);
            }

            const content = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1 dark:text-gray-300">Paciente</label>
                        <select name="patient_id" required class="block w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                            <option value="">Selecione um paciente</option>
                            @foreach($patients ?? [] as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-gray-300">Início</label>
                        <input type="datetime-local" name="start_time" value="${startVal}" step="60" required class="block w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-gray-300">Fim</label>
                        <input type="datetime-local" name="end_time" value="${endVal}" step="60" required class="block w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-gray-300">Profissional</label>
                        <select name="professional_id" required class="block w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                            @foreach($professionals as $prof)
                                <option value="{{ $prof->id }}">{{ $prof->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-gray-300">Sala</label>
                        <select name="room_id" required class="block w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" ${selectedRoomId == {{ $room->id }} ? 'selected' : ''}>{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1 dark:text-gray-300">Serviço</label>
                        <select name="service_type_id" required class="block w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                            @foreach($serviceTypes as $service)
                                <option value="{{ $service->id }}">{{ $service->name }} ({{ $service->duration_in_minutes }} min)</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            `;
            document.getElementById('modal-form-content').innerHTML = content;
            if (modal) modal.show();
        }

        function openEditAppointmentModal(event) {
            const props = event.extendedProps;
            document.getElementById('modal-title').innerText = 'Detalhes / Reagendar';
            document.getElementById('appointment-form').action = '{{ url('appointments') }}/' + event.id;

            const existingMethod = document.getElementById('appointment-form').querySelector('input[name="_method"]');
            if (existingMethod) existingMethod.remove();

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            document.getElementById('appointment-form').appendChild(methodInput);

            document.getElementById('cancel-appointment-btn').classList.remove('hidden');
            document.getElementById('cancel-appointment-btn').onclick = () => cancelAppointment(event.id);

            // Garantir que temos o formato ISO local correto para o input datetime-local
            const startStr = event.start ? new Date(event.start.getTime() - event.start.getTimezoneOffset() * 60000).toISOString().substring(0, 16) : '';
            const endStr = event.end ? new Date(event.end.getTime() - event.end.getTimezoneOffset() * 60000).toISOString().substring(0, 16) : '';

            const content = `
                <div class="space-y-4">
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                        <p class="text-sm font-bold dark:text-white">${props.patient_name}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">${props.service_name} com ${props.professional_name}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Sala: ${props.room_name} | Status: ${props.status}</p>
                    </div>

                    <input type="hidden" name="professional_id" value="${props.professional_id}">
                    <input type="hidden" name="room_id" value="${props.room_id}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1 dark:text-gray-300">Novo Horário Início</label>
                            <input type="datetime-local" name="start_time" value="${startStr}" step="60" required class="block w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1 dark:text-gray-300">Novo Horário Fim</label>
                            <input type="datetime-local" name="end_time" value="${endStr}" step="60" required class="block w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1 dark:text-gray-300">Observações / Motivo</label>
                        <textarea name="notes" rows="3" class="block w-full rounded-xl border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">${props.notes || ''}</textarea>
                    </div>
                </div>
            `;
            document.getElementById('modal-form-content').innerHTML = content;
            if (modal) modal.show();
        }

        function cancelAppointment(id) {
            Swal.fire({
                title: 'Deseja realmente cancelar este agendamento?',
                text: "Esta ação não pode ser revertida!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, cancelar!',
                cancelButtonText: 'Não, manter'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Motivo do cancelamento',
                        input: 'textarea',
                        inputPlaceholder: 'Digite o motivo aqui...',
                        showCancelButton: true,
                        confirmButtonText: 'Confirmar Cancelamento',
                        cancelButtonText: 'Voltar',
                        confirmButtonColor: '#d33',
                        inputValidator: (value) => {
                            if (!value) {
                                return 'Você precisa fornecer um motivo!';
                            }
                        }
                    }).then((noteResult) => {
                        if (noteResult.isConfirmed) {
                            fetch('/appointments/' + id + '/status', {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ status: 'canceled', notes: noteResult.value })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (modal) modal.hide();
                                calendar.refetchEvents();
                                Swal.fire(
                                    'Cancelado!',
                                    'O agendamento foi cancelado com sucesso.',
                                    'success'
                                );
                            });
                        }
                    });
                }
            });
        }

        function closeModal() {
            if (modal) modal.hide();
        }
    </script>
    <style>
        /* Ajustes de cores para o FullCalendar */
        :root {
            --fc-border-color: #f3f4f6; /* cinza-100 mais suave */
            --fc-daygrid-event-dot-width: 8px;
        }

        .dark {
            --fc-border-color: #1f2937; /* cinza-800 mais suave para dark */
            --fc-page-bg-color: #111827;
            --fc-neutral-bg-color: #1f2937;
            --fc-list-event-hover-bg-color: #374151;
            --fc-button-text-color: #fff;
            --fc-button-bg-color: #2563eb;
            --fc-button-border-color: #2563eb;
            --fc-button-hover-bg-color: #1d4ed8;
            --fc-button-hover-border-color: #1d4ed8;
            --fc-button-active-bg-color: #1e40af;
            --fc-button-active-border-color: #1e40af;
        }

        .fc-theme-standard td, .fc-theme-standard th {
            border-color: #f3f4f6 !important; /* light */
        }

        .fc-theme-standard .fc-scrollgrid {
            border-color: #f3f4f6 !important; /* light */
        }

        .dark .fc-theme-standard td,
        .dark .fc-theme-standard th,
        .dark .fc-theme-standard .fc-scrollgrid {
            border-color: #1f2937 !important; /* dark */
        }

        .dark .fc-theme-standard .fc-list {
            border-color: #1f2937;
        }

        .dark .fc-list-day-cushion {
            background-color: #374151;
        }

        .dark .fc-timegrid-axis-cushion,
        .dark .fc-timegrid-slot-label-cushion {
            color: #9ca3af;
        }

        .fc .fc-toolbar-title {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .dark .fc {
            color: #f9fafb;
        }

        .dark .fc-col-header-cell-cushion,
        .dark .fc-daygrid-day-number,
        .dark .fc-timegrid-slot-label-cushion,
        .dark .fc-list-day-text,
        .dark .fc-list-day-side-text {
            color: #d1d5db;
        }

        .fc .fc-button-primary {
            background-color: #2563eb;
            border-color: transparent;
            border-radius: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 0.5rem 1rem;
        }

        .fc .fc-button-primary:hover {
            background-color: #1d4ed8;
        }

        .fc .fc-button-primary:disabled {
            background-color: #9ca3af;
        }
    </style>
    @endpush
@endsection
