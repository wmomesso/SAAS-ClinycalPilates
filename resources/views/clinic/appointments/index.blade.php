@extends('layouts.saas')

@section('title', 'Agenda')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-emerald-600 dark:text-gray-400 dark:hover:text-white transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-emerald-600 dark:text-emerald-400 font-bold">Agenda</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    Agenda da Clínica
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gerencie os horários de atendimentos e profissionais.</p>
            </div>
            <button type="button" onclick="openNewAppointmentModal()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-[1.02]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Agendamento
            </button>
        </div>

        <!-- Filtros por Sala -->
        <div class="flex flex-wrap gap-2 mb-6 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-700">
            <button type="button" onclick="filterRoom(null)"
                    class="room-filter-btn px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 bg-emerald-600 text-white shadow-sm hover:shadow-md"
                    data-room-id="all">
                <svg class="w-4 h-4 inline mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Todas as Salas
            </button>
            @foreach($rooms as $room)
                <button type="button" onclick="filterRoom({{ $room->id }})"
                        class="room-filter-btn px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-emerald-300 dark:hover:border-emerald-700"
                        data-room-id="{{ $room->id }}">
                    {{ $room->name }}
                </button>
            @endforeach
        </div>

        <!-- Calendário -->
        <div id="calendar-container" class="min-h-[600px] mb-8 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
            <div id="calendar" class="p-2"></div>
        </div>

        <!-- Legenda -->
        <div class="mt-8 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Legenda de Profissionais
            </h3>
            <div class="flex flex-wrap gap-4">
                @foreach($professionals as $prof)
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 rounded-full shadow-sm border border-gray-200 dark:border-gray-600" style="background-color: {{ $prof->calendar_color ?? '#3b82f6' }}"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $prof->name }}</span>
                    </div>
                @endforeach
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 rounded-full shadow-sm border border-gray-200 dark:border-gray-600 bg-[#ef4444]"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Cancelado</span>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
        <!-- Modal -->
        <div id="appointment-modal" data-modal-placement="center" tabindex="-1" aria-hidden="true"
             class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-[70] justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full">
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200 dark:border-gray-700 rounded-t-2xl bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20">
                        <h3 id="modal-title" class="text-xl font-bold text-gray-900 dark:text-white">
                            Agendamento
                        </h3>
                        <button type="button" onclick="closeModal()"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center dark:hover:bg-gray-700 dark:hover:text-white transition-colors duration-200">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                        </button>
                    </div>

                    <form id="appointment-form" method="POST">
                        @csrf
                        <div id="modal-form-content" class="p-4 md:p-5 space-y-4"></div>

                        <!-- Footer -->
                        <div class="flex flex-wrap items-center justify-end p-4 md:p-5 border-t border-gray-200 dark:border-gray-700 rounded-b-2xl gap-2">
                            <button id="submit-btn" type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-[1.02]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Salvar
                            </button>
                            <button id="cancel-appointment-btn" type="button"
                                    class="hidden inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-[1.02]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancelar Agendamento
                            </button>
                            <button type="button" onclick="closeModal()"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-xl text-sm font-semibold transition-colors duration-200">
                                Voltar
                            </button>
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
                const $modalElement = document.getElementById('appointment-modal');
                if (window.Modal) {
                    modal = new Modal($modalElement, {
                        backdrop: 'static',
                        closable: true,
                        onHide: () => {
                            const backdrop = document.querySelector('[modal-backdrop]');
                            if (backdrop) backdrop.remove();
                        },
                        onShow: () => {
                            setTimeout(() => {
                                const backdrop = document.querySelector('[modal-backdrop]');
                                if (backdrop) backdrop.classList.add('z-[65]');
                            }, 50);
                        },
                    });
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
                    buttonText: {
                        today: 'Hoje',
                        month: 'Mês',
                        week: 'Semana',
                        day: 'Dia',
                        list: 'Lista'
                    },
                    slotMinTime: '06:00:00',
                    slotMaxTime: '22:00:00',
                    height: 'auto',
                    expandRows: false,

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

                document.getElementById('appointment-form').addEventListener('submit', function(e) {
                    e.preventDefault();
                    const submitBtn = document.getElementById('submit-btn');
                    const originalText = submitBtn.innerText;
                    submitBtn.disabled = true;
                    submitBtn.innerText = 'Salvando...';

                    const formData = new FormData(this);
                    const action = this.action;

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
                            if (response.ok) {
                                if (modal) modal.hide();
                                calendar.refetchEvents();
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
                                    confirmButtonColor: '#10b981',
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: 'Erro na comunicação com o servidor.',
                                confirmButtonColor: '#10b981',
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
                                confirmButtonColor: '#10b981',
                            });
                            calendar.refetchEvents();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Erro na comunicação com o servidor.',
                            confirmButtonColor: '#10b981',
                        });
                        calendar.refetchEvents();
                    });
            }

            function filterRoom(roomId) {
                selectedRoomId = roomId;
                document.querySelectorAll('.room-filter-btn').forEach(btn => {
                    if (btn.dataset.roomId == (roomId || 'all')) {
                        btn.classList.add('bg-emerald-600', 'text-white');
                        btn.classList.remove('bg-white', 'text-gray-700', 'dark:bg-gray-800', 'dark:text-gray-300', 'border', 'border-gray-200');
                    } else {
                        btn.classList.remove('bg-emerald-600', 'text-white');
                        btn.classList.add('bg-white', 'text-gray-700', 'dark:bg-gray-800', 'dark:text-gray-300', 'border', 'border-gray-200');
                    }
                });
                calendar.refetchEvents();
            }

            function openNewAppointmentModal(start, end) {
                document.getElementById('modal-title').innerText = 'Novo Agendamento';
                document.getElementById('appointment-form').action = '{{ route('appointments.store') }}';

                const existingMethod = document.getElementById('appointment-form').querySelector('input[name="_method"]');
                if (existingMethod) existingMethod.remove();

                document.getElementById('cancel-appointment-btn').classList.add('hidden');

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
                    <div class="md:col-span-2 relative">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase">Paciente <span class="text-red-500">*</span></label>
                        <input type="text" id="patient_search" placeholder="Digite pelo menos 3 caracteres para buscar..." autocomplete="off"
                               class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <input type="hidden" name="patient_id" id="selected_patient_id" required>
                        <div id="patient_results" class="hidden absolute z-[80] w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg max-h-48 overflow-y-auto"></div>
                    </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase">Início <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="start_time" value="${startVal}" step="900" required class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase">Fim <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="end_time" value="${endVal}" step="900" required class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase">Profissional <span class="text-red-500">*</span></label>
                        <select name="professional_id" required class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            @foreach($professionals as $prof)
                <option value="{{ $prof->id }}">{{ $prof->name }}</option>
                            @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase">Sala <span class="text-red-500">*</span></label>
                <select name="room_id" required class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
@foreach($rooms as $room)
                <option value="{{ $room->id }}" ${selectedRoomId == {{ $room->id }} ? 'selected' : ''}>{{ $room->name }}</option>
                            @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase">Serviço <span class="text-red-500">*</span></label>
                <select name="service_type_id" required class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
@foreach($serviceTypes as $service)
                <option value="{{ $service->id }}">{{ $service->name }} ({{ $service->duration_in_minutes }} min)</option>
                            @endforeach
                </select>
            </div>
        </div>
`;
                document.getElementById('modal-form-content').innerHTML = content;
                if (modal) modal.show();

                // Inicializar busca de pacientes
                initPatientSearch();
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

                const startStr = event.start ? new Date(event.start.getTime() - event.start.getTimezoneOffset() * 60000).toISOString().substring(0, 16) : '';
                const endStr = event.end ? new Date(event.end.getTime() - event.end.getTimezoneOffset() * 60000).toISOString().substring(0, 16) : '';

                const content = `
                <div class="space-y-4">
                    <div class="p-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 rounded-xl border border-gray-200 dark:border-gray-600">
                        <p class="text-sm font-bold text-gray-900 dark:text-white">${props.patient_name}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">${props.service_name} com ${props.professional_name}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Sala: ${props.room_name} | Status: ${props.status}</p>
                    </div>

                    <input type="hidden" name="professional_id" value="${props.professional_id}">
                    <input type="hidden" name="room_id" value="${props.room_id}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase">Novo Horário Início</label>
                            <input type="datetime-local" name="start_time" value="${startStr}" step="900" required class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase">Novo Horário Fim</label>
                            <input type="datetime-local" name="end_time" value="${endStr}" step="900" required class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase">Observações / Motivo</label>
                        <textarea name="notes" rows="3" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm resize-y">${props.notes || ''}</textarea>
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
                                if (!value) return 'Você precisa fornecer um motivo!';
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

            function initPatientSearch() {
                const searchInput = document.getElementById('patient_search');
                const resultsDiv = document.getElementById('patient_results');
                const patientIdInput = document.getElementById('selected_patient_id');
                let timeout = null;

                searchInput.addEventListener('input', function() {
                    clearTimeout(timeout);
                    const query = this.value;

                    if (query.length < 3) {
                        resultsDiv.innerHTML = '';
                        resultsDiv.classList.add('hidden');
                        return;
                    }

                    timeout = setTimeout(() => {
                        fetch(`{{ route('patients.search') }}?q=${query}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                        })
                        .then(response => response.json())
                        .then(data => {
                            resultsDiv.innerHTML = '';
                            if (data.length > 0) {
                                data.forEach(patient => {
                                    const div = document.createElement('div');
                                    div.className = 'p-3 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-0 text-sm dark:text-gray-300';
                                    div.textContent = patient.full_name;
                                    div.onclick = () => {
                                        searchInput.value = patient.full_name;
                                        patientIdInput.value = patient.id;
                                        resultsDiv.classList.add('hidden');
                                    };
                                    resultsDiv.appendChild(div);
                                });
                                resultsDiv.classList.remove('hidden');
                            } else {
                                resultsDiv.innerHTML = '<div class="p-3 text-sm text-gray-500 italic">Nenhum paciente encontrado</div>';
                                resultsDiv.classList.remove('hidden');
                            }
                        });
                    }, 300);
                });

                // Fechar resultados ao clicar fora
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
                        resultsDiv.classList.add('hidden');
                    }
                });
            }
        </script>

        <style>
            /* FullCalendar - Tema Moderno */
            :root {
                --fc-border-color: #e5e7eb;
                --fc-daygrid-event-dot-width: 8px;
                --fc-button-bg-color: #10b981;
                --fc-button-border-color: #10b981;
                --fc-button-hover-bg-color: #059669;
                --fc-button-hover-border-color: #059669;
                --fc-button-active-bg-color: #047857;
                --fc-button-active-border-color: #047857;
            }

            .dark {
                --fc-border-color: #374151;
                --fc-page-bg-color: #1f2937;
                --fc-neutral-bg-color: #374151;
                --fc-list-event-hover-bg-color: #4b5563;
            }

            .fc {
                font-family: 'Inter', system-ui, -apple-system, sans-serif;
            }

            .fc .fc-toolbar-title {
                font-size: 1.25rem;
                font-weight: 700;
                color: #1f2937;
            }

            .dark .fc .fc-toolbar-title {
                color: #f9fafb;
            }

            .fc .fc-button-primary {
                background-color: var(--fc-button-bg-color);
                border-color: var(--fc-button-border-color);
                border-radius: 0.75rem;
                font-weight: 600;
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                padding: 0.5rem 1rem;
                transition: all 0.2s ease;
                box-shadow: 0 1px 3px rgba(16, 185, 129, 0.2);
            }

            .fc .fc-button-primary:hover {
                background-color: var(--fc-button-hover-bg-color);
                border-color: var(--fc-button-hover-border-color);
                transform: scale(1.02);
                box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
            }

            .fc .fc-button-primary:disabled {
                background-color: #9ca3af;
                border-color: #9ca3af;
            }

            .fc .fc-button-primary:focus {
                box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.4);
            }

            .fc .fc-button-primary.fc-button-active {
                background-color: var(--fc-button-active-bg-color);
                border-color: var(--fc-button-active-border-color);
            }

            .fc-theme-standard td,
            .fc-theme-standard th,
            .fc-theme-standard .fc-scrollgrid {
                border-color: var(--fc-border-color);
            }

            .fc-theme-standard .fc-list {
                border-color: var(--fc-border-color);
            }

            .fc-list-day-cushion {
                background-color: #f3f4f6;
            }

            .dark .fc-list-day-cushion {
                background-color: #374151;
            }

            .fc-col-header-cell-cushion,
            .fc-daygrid-day-number,
            .fc-timegrid-slot-label-cushion,
            .fc-list-day-text,
            .fc-list-day-side-text {
                color: #4b5563;
                font-weight: 500;
            }

            .dark .fc-col-header-cell-cushion,
            .dark .fc-daygrid-day-number,
            .dark .fc-timegrid-slot-label-cushion,
            .dark .fc-list-day-text,
            .dark .fc-list-day-side-text {
                color: #d1d5db;
            }

            .fc-day-today {
                background-color: #f0fdf4 !important;
            }

            .dark .fc-day-today {
                background-color: #064e3b !important;
            }

            .fc-timegrid-event {
                border-radius: 0.75rem;
                padding: 2px 4px;
                border: none;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .fc-timegrid-event .fc-event-title {
                font-weight: 600;
                font-size: 0.75rem;
            }

            .fc-timegrid-event .fc-event-time {
                font-size: 0.65rem;
                opacity: 0.9;
            }

            .fc-daygrid-event {
                border-radius: 0.5rem;
                border: none;
                padding: 2px 6px;
                font-weight: 500;
                font-size: 0.75rem;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            }

            .fc-event {
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .fc-event:hover {
                transform: scale(1.02);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            }

            .fc-event.fc-event-canceled {
                background-color: #ef4444 !important;
                border-color: #ef4444 !important;
                opacity: 0.8;
            }

            .fc-event.fc-event-canceled .fc-event-title {
                text-decoration: line-through;
            }

            .fc-popover {
                border-radius: 0.75rem;
                border: none;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            }

            .fc-popover .fc-popover-header {
                background-color: #f3f4f6;
                border-radius: 0.75rem 0.75rem 0 0;
                padding: 0.75rem 1rem;
            }

            .dark .fc-popover .fc-popover-header {
                background-color: #374151;
            }

            .fc-popover .fc-popover-body {
                padding: 1rem;
            }

            /* Scrollbar personalizada para o calendário */
            .fc-scroller::-webkit-scrollbar {
                width: 4px;
                height: 4px;
            }

            .fc-scroller::-webkit-scrollbar-track {
                background: transparent;
            }

            .fc-scroller::-webkit-scrollbar-thumb {
                background: #d1d5db;
                border-radius: 20px;
            }

            .fc-scroller::-webkit-scrollbar-thumb:hover {
                background: #9ca3af;
            }

            .dark .fc-scroller::-webkit-scrollbar-thumb {
                background: #4b5563;
            }

            .dark .fc-scroller::-webkit-scrollbar-thumb:hover {
                background: #6b7280;
            }

            /* Indicador de now */
            .fc-timegrid-now-indicator-line {
                border-color: #ef4444;
                border-width: 2px;
            }

            .fc-timegrid-now-indicator-arrow {
                border-color: #ef4444;
            }
        </style>
    @endpush
@endsection
