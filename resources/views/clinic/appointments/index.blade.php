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

        <!-- Agenda -->
        <div id="schedule-container" class="mb-8 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" id="prev-day-btn" class="schedule-nav-btn" aria-label="Dia anterior">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button type="button" id="today-btn" class="schedule-action-btn">Hoje</button>
                    <button type="button" id="next-day-btn" class="schedule-nav-btn" aria-label="Próximo dia">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <input type="date" id="schedule-date-input" class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                </div>

                <div>
                    <h3 id="schedule-title" class="text-lg font-bold text-gray-900 dark:text-white"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Clique em um horário para agendar, ou arraste na mesma sala para selecionar um período.</p>
                </div>
            </div>

            <div id="week-strip" class="grid grid-cols-7 gap-1 p-3 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800"></div>

            <div id="schedule-loading" class="hidden p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Carregando agenda...
            </div>
            <div id="schedule-empty" class="hidden p-8 text-center text-sm text-gray-500 dark:text-gray-400">
                Nenhuma sala ativa encontrada para montar a agenda.
            </div>
            <div id="schedule-grid-wrapper" class="overflow-auto max-h-[70vh]">
                <div id="schedule-grid" class="schedule-grid"></div>
            </div>
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
        @php
            $scheduleRooms = $rooms->map(function ($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->name,
                    'capacity' => $room->capacity ?? 1,
                ];
            })->values();

            $scheduleProfessionals = $professionals->map(function ($professional) {
                return [
                    'id' => $professional->id,
                    'name' => $professional->name,
                    'calendar_color' => $professional->calendar_color ?? '#3b82f6',
                ];
            })->values();

            $scheduleServiceTypes = $serviceTypes->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'duration_in_minutes' => $service->duration_in_minutes ?? 60,
                ];
            })->values();
        @endphp

        <script>
            const rooms = @json($scheduleRooms);
            const professionals = @json($scheduleProfessionals);
            const serviceTypes = @json($scheduleServiceTypes);

            const slotStepMinutes = 30;
            const scheduleStartMinutes = 6 * 60;
            const scheduleEndMinutes = 22 * 60;
            const activeStatuses = ['scheduled', 'confirmed', 'completed'];
            const statusLabels = {
                scheduled: 'Agendado',
                confirmed: 'Confirmado',
                completed: 'Concluído',
                canceled: 'Cancelado',
                no_show: 'Faltou',
            };

            let selectedRoomId = null;
            let selectedDate = new Date();
            let modal;
            let appointments = [];
            let dragSelection = null;

            document.addEventListener('DOMContentLoaded', function() {
                selectedDate = stripTime(selectedDate);
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

                document.getElementById('prev-day-btn').addEventListener('click', () => changeSelectedDate(addDays(selectedDate, -1)));
                document.getElementById('next-day-btn').addEventListener('click', () => changeSelectedDate(addDays(selectedDate, 1)));
                document.getElementById('today-btn').addEventListener('click', () => changeSelectedDate(new Date()));
                document.getElementById('schedule-date-input').addEventListener('change', (event) => {
                    if (event.target.value) {
                        changeSelectedDate(parseDateKey(event.target.value));
                    }
                });

                bindScheduleGridEvents();
                bindAppointmentForm();
                loadAppointments();
            });

            function bindAppointmentForm() {
                document.getElementById('appointment-form').addEventListener('submit', function(e) {
                    e.preventDefault();
                    const submitBtn = document.getElementById('submit-btn');
                    const originalText = submitBtn.innerText;
                    submitBtn.disabled = true;
                    submitBtn.innerText = 'Salvando...';

                    const formData = new FormData(this);
                    const action = this.action;

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
                                this.reset();
                                await loadAppointments();
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
            }

            function bindScheduleGridEvents() {
                const grid = document.getElementById('schedule-grid');

                grid.addEventListener('click', (event) => {
                    const appointmentButton = event.target.closest('[data-appointment-id]');
                    if (appointmentButton) {
                        const appointment = appointments.find(item => item.id === Number(appointmentButton.dataset.appointmentId));
                        if (appointment) openEditAppointmentModal(appointment);
                        return;
                    }

                    const addButton = event.target.closest('.schedule-add-btn');
                    if (addButton) {
                        const slot = getSlotByIndex(Number(addButton.dataset.slotIndex));
                        const roomId = Number(addButton.dataset.roomId);
                        const end = getDefaultEnd(slot.start);
                        if (!canAddToRange(roomId, slot.start, end)) {
                            showCapacityAlert();
                            return;
                        }
                        openNewAppointmentModal(slot.start, end, roomId);
                    }
                });

                grid.addEventListener('pointerdown', (event) => {
                    if (event.target.closest('[data-appointment-id], .schedule-add-btn')) return;

                    const cell = event.target.closest('.schedule-cell');
                    if (!cell) return;

                    dragSelection = {
                        roomId: Number(cell.dataset.roomId),
                        startIndex: Number(cell.dataset.slotIndex),
                        endIndex: Number(cell.dataset.slotIndex),
                    };
                    grid.setPointerCapture?.(event.pointerId);
                    paintSelection();
                });

                grid.addEventListener('pointerover', (event) => {
                    if (!dragSelection) return;

                    const cell = event.target.closest('.schedule-cell');
                    if (!cell || Number(cell.dataset.roomId) !== dragSelection.roomId) return;

                    dragSelection.endIndex = Number(cell.dataset.slotIndex);
                    paintSelection();
                });

                grid.addEventListener('pointerup', () => finishSelection());
                grid.addEventListener('pointercancel', () => clearSelection());
            }

            function finishSelection() {
                if (!dragSelection) return;

                const startIndex = Math.min(dragSelection.startIndex, dragSelection.endIndex);
                const endIndex = Math.max(dragSelection.startIndex, dragSelection.endIndex);
                const startSlot = getSlotByIndex(startIndex);
                const endSlot = getSlotByIndex(endIndex);
                const start = startSlot.start;
                const end = startIndex === endIndex ? getDefaultEnd(start) : endSlot.end;
                const roomId = dragSelection.roomId;

                clearSelection();

                if (!canAddToRange(roomId, start, end)) {
                    showCapacityAlert();
                    return;
                }

                openNewAppointmentModal(start, end, roomId);
            }

            function changeSelectedDate(date) {
                selectedDate = stripTime(date);
                loadAppointments();
            }

            async function loadAppointments() {
                setLoading(true);

                const start = `${dateKey(selectedDate)}T00:00:00`;
                const end = `${dateKey(addDays(selectedDate, 1))}T00:00:00`;
                let url = `{{ route('appointments.index') }}?start=${encodeURIComponent(start)}&end=${encodeURIComponent(end)}`;
                if (selectedRoomId) url += `&room_id=${selectedRoomId}`;

                try {
                    const response = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    const data = await response.json();
                    appointments = data.map(normalizeAppointment);
                    renderSchedule();
                } catch (error) {
                    console.error('Error fetching appointments:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Não foi possível carregar a agenda.',
                        confirmButtonColor: '#10b981',
                    });
                } finally {
                    setLoading(false);
                }
            }

            function renderSchedule() {
                renderScheduleHeader();
                renderWeekStrip();

                const grid = document.getElementById('schedule-grid');
                const visibleRooms = getVisibleRooms();

                document.getElementById('schedule-empty').classList.toggle('hidden', visibleRooms.length > 0);
                document.getElementById('schedule-grid-wrapper').classList.toggle('hidden', visibleRooms.length === 0);

                if (visibleRooms.length === 0) {
                    grid.innerHTML = '';
                    return;
                }

                grid.style.gridTemplateColumns = `76px repeat(${visibleRooms.length}, minmax(220px, 1fr))`;

                const slots = getSlots();
                let html = '<div class="schedule-corner schedule-sticky-cell"></div>';
                visibleRooms.forEach(room => {
                    html += `
                        <div class="schedule-room-header schedule-sticky-cell">
                            <span class="schedule-room-name">${escapeHtml(room.name)}</span>
                            <span class="schedule-room-capacity">cap. ${room.capacity}</span>
                        </div>
                    `;
                });

                slots.forEach((slot, slotIndex) => {
                    html += `<div class="schedule-time-cell">${formatTime(slot.start)}</div>`;

                    visibleRooms.forEach(room => {
                        const overlapCount = countRoomAppointments(room.id, slot.start, slot.end);
                        const isFull = overlapCount >= Number(room.capacity || 1);
                        const slotAppointments = appointments
                            .filter(appointment => appointment.room_id === room.id && appointment.start >= slot.start && appointment.start < slot.end)
                            .sort((a, b) => a.start - b.start);

                        html += `
                            <div class="schedule-cell ${isFull ? 'is-full' : ''}" data-room-id="${room.id}" data-slot-index="${slotIndex}">
                                <div class="schedule-cell-meta">
                                    <span class="capacity-badge ${isFull ? 'is-full' : ''}">${overlapCount}/${room.capacity}</span>
                                    ${isFull ? '' : `
                                        <button type="button" class="schedule-add-btn" data-room-id="${room.id}" data-slot-index="${slotIndex}" title="Adicionar paciente neste horário">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                    `}
                                </div>
                                <div class="schedule-appointments">
                                    ${slotAppointments.map(renderAppointmentChip).join('')}
                                </div>
                            </div>
                        `;
                    });
                });

                grid.innerHTML = html;
            }

            function renderScheduleHeader() {
                document.getElementById('schedule-title').innerText = selectedDate.toLocaleDateString('pt-BR', {
                    weekday: 'long',
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                });
                document.getElementById('schedule-date-input').value = dateKey(selectedDate);
            }

            function renderWeekStrip() {
                const strip = document.getElementById('week-strip');
                const weekStart = addDays(selectedDate, -((selectedDate.getDay() + 6) % 7));
                let html = '';

                for (let index = 0; index < 7; index++) {
                    const day = addDays(weekStart, index);
                    const isSelected = dateKey(day) === dateKey(selectedDate);
                    const isToday = dateKey(day) === dateKey(new Date());
                    html += `
                        <button type="button" class="week-day-btn ${isSelected ? 'is-selected' : ''} ${isToday ? 'is-today' : ''}" data-date="${dateKey(day)}">
                            <span>${day.toLocaleDateString('pt-BR', { weekday: 'short' })}</span>
                            <strong>${day.toLocaleDateString('pt-BR', { day: '2-digit' })}</strong>
                        </button>
                    `;
                }

                strip.innerHTML = html;
                strip.querySelectorAll('[data-date]').forEach(button => {
                    button.addEventListener('click', () => changeSelectedDate(parseDateKey(button.dataset.date)));
                });
            }

            function renderAppointmentChip(appointment) {
                const color = appointment.status === 'canceled'
                    ? '#ef4444'
                    : (appointment.status === 'no_show' ? '#f97316' : sanitizeColor(appointment.backgroundColor));
                const status = statusLabels[appointment.status] || appointment.status;

                return `
                    <button type="button" class="appointment-chip ${appointment.status === 'canceled' ? 'is-canceled' : ''}"
                            style="--appointment-color: ${color}" data-appointment-id="${appointment.id}">
                        <span class="appointment-chip-main">${escapeHtml(appointment.patient_name)}</span>
                        <span class="appointment-chip-detail">${formatTime(appointment.start)}-${formatTime(appointment.end)} · ${escapeHtml(appointment.professional_name)}</span>
                        <span class="appointment-chip-status">${escapeHtml(status)}</span>
                    </button>
                `;
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
                loadAppointments();
            }

            function openNewAppointmentModal(start, end, roomId = null) {
                const visibleRooms = getVisibleRooms();
                const resolvedRoomId = roomId || selectedRoomId || visibleRooms[0]?.id || rooms[0]?.id || '';
                const startDate = start ? new Date(start) : getDefaultStart();
                const endDate = end ? new Date(end) : getDefaultEnd(startDate);
                const selectedRoom = rooms.find(room => room.id === Number(resolvedRoomId));

                document.getElementById('modal-title').innerText = 'Novo Agendamento';
                document.getElementById('appointment-form').action = '{{ route('appointments.store') }}';
                removeMethodInput();
                document.getElementById('submit-btn').classList.remove('hidden');
                document.getElementById('cancel-appointment-btn').classList.add('hidden');

                const content = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800">
                            <p class="text-sm font-semibold text-emerald-900 dark:text-emerald-100">${escapeHtml(selectedRoom?.name || 'Sala')}</p>
                            <p id="appointment-range-preview" data-room-suffix="${selectedRoom ? ` · capacidade ${selectedRoom.capacity}` : ''}" class="text-xs text-emerald-700 dark:text-emerald-300">${formatDateTimeRange(startDate, endDate)}${selectedRoom ? ` · capacidade ${selectedRoom.capacity}` : ''}</p>
                        </div>
                        <div class="md:col-span-2 relative">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase">Paciente <span class="text-red-500">*</span></label>
                            <input type="text" id="patient_search" placeholder="Digite pelo menos 3 caracteres para buscar..." autocomplete="off"
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                            <input type="hidden" name="patient_id" id="selected_patient_id" required>
                            <div id="patient_results" class="hidden absolute z-[80] w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg max-h-48 overflow-y-auto"></div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase">Profissional <span class="text-red-500">*</span></label>
                            <select name="professional_id" required class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                ${professionals.map(professional => `<option value="${professional.id}">${escapeHtml(professional.name)}</option>`).join('')}
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase">Sala <span class="text-red-500">*</span></label>
                            <select name="room_id" required class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                ${rooms.map(room => `<option value="${room.id}" ${Number(resolvedRoomId) === room.id ? 'selected' : ''}>${escapeHtml(room.name)} · cap. ${room.capacity}</option>`).join('')}
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase">Serviço <span class="text-red-500">*</span></label>
                            <select name="service_type_id" required class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                ${serviceTypes.map(service => `<option value="${service.id}" data-duration="${service.duration_in_minutes}">${escapeHtml(service.name)} (${service.duration_in_minutes} min)</option>`).join('')}
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase">Início <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="start_time" value="${toLocalInputValue(startDate)}" step="900" required class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase">Fim <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="end_time" value="${toLocalInputValue(endDate)}" step="900" required class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>
                    </div>
                `;
                document.getElementById('modal-form-content').innerHTML = content;
                bindServiceDurationSync();
                initPatientSearch();
                if (modal) modal.show();
            }

            function openEditAppointmentModal(appointment) {
                const props = appointment.extendedProps || appointment;
                document.getElementById('modal-title').innerText = 'Detalhes do Agendamento';
                document.getElementById('appointment-form').action = '{{ url('appointments') }}/' + appointment.id;
                removeMethodInput();
                document.getElementById('submit-btn').classList.add('hidden');
                document.getElementById('cancel-appointment-btn').classList.add('hidden');

                const content = `
                    <div class="space-y-4">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">${escapeHtml(props.patient_name)}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">${escapeHtml(props.service_name)} com ${escapeHtml(props.professional_name)}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Sala: ${escapeHtml(props.room_name)} · ${formatDateTimeRange(appointment.start, appointment.end)} · ${escapeHtml(statusLabels[props.status] || props.status)}</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <button type="button" onclick="markAppointmentStatus(${appointment.id}, 'completed')" class="inline-flex justify-center items-center gap-2 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold uppercase">
                                Presença
                            </button>
                            <button type="button" onclick="markAppointmentStatus(${appointment.id}, 'no_show')" class="inline-flex justify-center items-center gap-2 px-3 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold uppercase">
                                Falta
                            </button>
                            <button type="button" onclick="removeAppointment(${appointment.id})" class="inline-flex justify-center items-center gap-2 px-3 py-2 rounded-xl bg-gray-700 hover:bg-gray-800 text-white text-xs font-bold uppercase">
                                Desmarcar
                            </button>
                        </div>

                        ${props.notes ? `
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/60 border border-gray-200 dark:border-gray-600">
                                <p class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400 mb-1">Observações registradas</p>
                                <p class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-line">${escapeHtml(props.notes)}</p>
                            </div>
                        ` : ''}
                    </div>
                `;
                document.getElementById('modal-form-content').innerHTML = content;
                if (modal) modal.show();
            }

            function markAppointmentStatus(id, status) {
                const statusTitle = status === 'completed' ? 'Marcar presença?' : 'Registrar falta?';
                const statusText = status === 'completed'
                    ? 'Esta ação irá consumir uma sessão realizada do pacote do paciente, quando houver pacote ativo.'
                    : 'Esta ação irá consumir uma sessão perdida do pacote do paciente, quando houver pacote ativo.';

                Swal.fire({
                    title: statusTitle,
                    text: statusText,
                    icon: status === 'completed' ? 'question' : 'warning',
                    input: status === 'no_show' ? 'textarea' : undefined,
                    inputPlaceholder: 'Observação opcional...',
                    showCancelButton: true,
                    confirmButtonColor: status === 'completed' ? '#059669' : '#ea580c',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Confirmar',
                    cancelButtonText: 'Voltar',
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    const payload = { status };
                    if (typeof result.value === 'string' && result.value.trim() !== '') {
                        payload.notes = result.value.trim();
                    }

                    fetch('/appointments/' + id + '/status', {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                        .then(async response => {
                            const data = await response.json();
                            if (!response.ok) {
                                throw new Error(data.message || 'Não foi possível atualizar o status.');
                            }

                            if (modal) modal.hide();
                            await loadAppointments();
                            Swal.fire('Atualizado!', data.message || 'Status atualizado com sucesso.', 'success');
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: error.message,
                                confirmButtonColor: '#10b981',
                            });
                        });
                });
            }

            function removeAppointment(id) {
                Swal.fire({
                    title: 'Desmarcar este agendamento?',
                    text: 'O horário será removido da agenda. Se já consumiu pacote, a sessão será estornada.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#374151',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Desmarcar',
                    cancelButtonText: 'Voltar',
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    fetch('/appointments/' + id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                        .then(async response => {
                            const data = await response.json();
                            if (!response.ok) {
                                throw new Error(data.message || 'Não foi possível desmarcar o agendamento.');
                            }

                            if (modal) modal.hide();
                            await loadAppointments();
                            Swal.fire('Desmarcado!', data.message || 'Agendamento desmarcado com sucesso.', 'success');
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: error.message,
                                confirmButtonColor: '#10b981',
                            });
                        });
                });
            }

            function cancelAppointment(id) {
                Swal.fire({
                    title: 'Deseja realmente cancelar este agendamento?',
                    text: 'Esta ação não pode ser revertida!',
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
                                    .then(async () => {
                                        if (modal) modal.hide();
                                        await loadAppointments();
                                        Swal.fire('Cancelado!', 'O agendamento foi cancelado com sucesso.', 'success');
                                    });
                            }
                        });
                    }
                });
            }

            function initPatientSearch() {
                const searchInput = document.getElementById('patient_search');
                const resultsDiv = document.getElementById('patient_results');
                const patientIdInput = document.getElementById('selected_patient_id');
                if (!searchInput || !resultsDiv || !patientIdInput) return;

                let timeout = null;

                searchInput.addEventListener('input', function() {
                    clearTimeout(timeout);
                    const query = this.value.trim();
                    patientIdInput.value = '';

                    if (query.length < 3) {
                        resultsDiv.innerHTML = '';
                        resultsDiv.classList.add('hidden');
                        return;
                    }

                    timeout = setTimeout(() => {
                        fetch(`{{ route('patients.search') }}?q=${encodeURIComponent(query)}`, {
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
            }

            document.addEventListener('click', function(e) {
                const searchInput = document.getElementById('patient_search');
                const resultsDiv = document.getElementById('patient_results');
                if (searchInput && resultsDiv && !searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
                    resultsDiv.classList.add('hidden');
                }
            });

            function bindServiceDurationSync() {
                const serviceSelect = document.querySelector('select[name="service_type_id"]');
                const startInput = document.querySelector('input[name="start_time"]');
                const endInput = document.querySelector('input[name="end_time"]');
                const rangePreview = document.getElementById('appointment-range-preview');
                if (!serviceSelect || !startInput || !endInput) return;

                let endWasEdited = false;

                const updateRangePreview = () => {
                    if (!rangePreview || !startInput.value || !endInput.value) return;
                    rangePreview.innerText = `${formatDateTimeRange(new Date(startInput.value), new Date(endInput.value))}${rangePreview.dataset.roomSuffix || ''}`;
                };

                const updateEndFromService = (force = false) => {
                    if (endWasEdited && !force) {
                        updateRangePreview();
                        return;
                    }

                    const duration = Number(serviceSelect.selectedOptions[0]?.dataset.duration || 60);
                    if (startInput.value) {
                        endInput.value = toLocalInputValue(addMinutes(new Date(startInput.value), duration));
                    }
                    updateRangePreview();
                };

                serviceSelect.addEventListener('change', () => {
                    endWasEdited = false;
                    updateEndFromService(true);
                });

                startInput.addEventListener('change', () => {
                    updateEndFromService(false);
                });

                endInput.addEventListener('change', () => {
                    endWasEdited = true;
                    updateRangePreview();
                });
            }

            function normalizeAppointment(item) {
                const props = item.extendedProps || {};

                return {
                    id: Number(item.id),
                    title: item.title,
                    start: new Date(item.start),
                    end: new Date(item.end),
                    backgroundColor: item.backgroundColor || '#3b82f6',
                    patient_id: Number(props.patient_id),
                    patient_name: props.patient_name || item.title,
                    professional_id: Number(props.professional_id),
                    professional_name: props.professional_name || '',
                    room_id: Number(props.room_id),
                    room_name: props.room_name || '',
                    service_type_id: Number(props.service_type_id),
                    service_name: props.service_name || '',
                    status: props.status || 'scheduled',
                    notes: props.notes || '',
                };
            }

            function countRoomAppointments(roomId, start, end) {
                return appointments.filter(appointment => (
                    appointment.room_id === Number(roomId) &&
                    activeStatuses.includes(appointment.status) &&
                    appointment.start < end &&
                    appointment.end > start
                )).length;
            }

            function canAddToRange(roomId, start, end) {
                const room = rooms.find(item => item.id === Number(roomId));
                if (!room) return false;

                return countRoomAppointments(room.id, start, end) < Number(room.capacity || 1);
            }

            function getVisibleRooms() {
                if (!selectedRoomId) return rooms;
                return rooms.filter(room => room.id === Number(selectedRoomId));
            }

            function getSlots() {
                const slots = [];
                for (let minutes = scheduleStartMinutes; minutes < scheduleEndMinutes; minutes += slotStepMinutes) {
                    const start = dateAtMinutes(selectedDate, minutes);
                    slots.push({
                        start,
                        end: addMinutes(start, slotStepMinutes),
                    });
                }
                return slots;
            }

            function getSlotByIndex(index) {
                return getSlots()[index];
            }

            function paintSelection() {
                document.querySelectorAll('.schedule-cell.is-selecting').forEach(cell => cell.classList.remove('is-selecting'));
                if (!dragSelection) return;

                const startIndex = Math.min(dragSelection.startIndex, dragSelection.endIndex);
                const endIndex = Math.max(dragSelection.startIndex, dragSelection.endIndex);
                document.querySelectorAll(`.schedule-cell[data-room-id="${dragSelection.roomId}"]`).forEach(cell => {
                    const cellIndex = Number(cell.dataset.slotIndex);
                    if (cellIndex >= startIndex && cellIndex <= endIndex) {
                        cell.classList.add('is-selecting');
                    }
                });
            }

            function clearSelection() {
                dragSelection = null;
                document.querySelectorAll('.schedule-cell.is-selecting').forEach(cell => cell.classList.remove('is-selecting'));
            }

            function setLoading(isLoading) {
                document.getElementById('schedule-loading').classList.toggle('hidden', !isLoading);
            }

            function showCapacityAlert() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sala sem vaga nesse período',
                    text: 'Escolha outro horário, reduza o período ou selecione uma sala com capacidade disponível.',
                    confirmButtonColor: '#10b981',
                });
            }

            function closeModal() {
                if (modal) modal.hide();
            }

            function removeMethodInput() {
                const existingMethod = document.getElementById('appointment-form').querySelector('input[name="_method"]');
                if (existingMethod) existingMethod.remove();
            }

            function getDefaultStart() {
                const now = new Date();
                const base = dateKey(now) === dateKey(selectedDate) ? now : dateAtMinutes(selectedDate, 7 * 60);
                const roundedMinutes = Math.ceil((base.getHours() * 60 + base.getMinutes()) / slotStepMinutes) * slotStepMinutes;
                return dateAtMinutes(selectedDate, Math.min(Math.max(roundedMinutes, scheduleStartMinutes), scheduleEndMinutes - slotStepMinutes));
            }

            function getDefaultEnd(start) {
                const duration = Number(serviceTypes[0]?.duration_in_minutes || 60);
                return addMinutes(start, duration);
            }

            function formatDateToSQL(dateStr) {
                if (!dateStr) return '';
                return dateStr.length === 16 ? `${dateStr.replace('T', ' ')}:00` : dateStr.replace('T', ' ');
            }

            function formatDateTimeRange(start, end) {
                return `${start.toLocaleDateString('pt-BR')} · ${formatTime(start)}-${formatTime(end)}`;
            }

            function formatTime(date) {
                return date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
            }

            function toLocalInputValue(date) {
                const offset = date.getTimezoneOffset() * 60000;
                return new Date(date.getTime() - offset).toISOString().substring(0, 16);
            }

            function dateKey(date) {
                const normalized = stripTime(date);
                const year = normalized.getFullYear();
                const month = String(normalized.getMonth() + 1).padStart(2, '0');
                const day = String(normalized.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            function parseDateKey(value) {
                const [year, month, day] = value.split('-').map(Number);
                return new Date(year, month - 1, day);
            }

            function stripTime(date) {
                return new Date(date.getFullYear(), date.getMonth(), date.getDate());
            }

            function dateAtMinutes(date, minutes) {
                return new Date(date.getFullYear(), date.getMonth(), date.getDate(), Math.floor(minutes / 60), minutes % 60);
            }

            function addMinutes(date, minutes) {
                return new Date(date.getTime() + minutes * 60000);
            }

            function addDays(date, days) {
                const next = new Date(date);
                next.setDate(next.getDate() + days);
                return stripTime(next);
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function sanitizeColor(value) {
                return /^#[0-9a-f]{3,8}$/i.test(value) ? value : '#3b82f6';
            }
        </script>

        <style>
            .schedule-nav-btn,
            .schedule-action-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 2.5rem;
                border-radius: 0.75rem;
                border: 1px solid #d1d5db;
                background: #ffffff;
                color: #374151;
                font-size: 0.875rem;
                font-weight: 600;
                transition: border-color 0.2s ease, background-color 0.2s ease, color 0.2s ease;
            }

            .schedule-nav-btn {
                width: 2.5rem;
            }

            .schedule-action-btn {
                padding: 0 1rem;
            }

            .schedule-nav-btn:hover,
            .schedule-action-btn:hover {
                border-color: #10b981;
                color: #047857;
            }

            .dark .schedule-nav-btn,
            .dark .schedule-action-btn {
                border-color: #4b5563;
                background: #1f2937;
                color: #d1d5db;
            }

            .week-day-btn {
                display: flex;
                min-width: 0;
                flex-direction: column;
                align-items: center;
                gap: 0.125rem;
                border-radius: 0.75rem;
                border: 1px solid transparent;
                padding: 0.5rem 0.25rem;
                color: #6b7280;
                transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
            }

            .week-day-btn span {
                font-size: 0.7rem;
                text-transform: uppercase;
            }

            .week-day-btn strong {
                color: #111827;
                font-size: 0.95rem;
            }

            .week-day-btn.is-selected {
                border-color: #10b981;
                background: #ecfdf5;
                color: #047857;
            }

            .week-day-btn.is-today:not(.is-selected) {
                background: #f3f4f6;
            }

            .dark .week-day-btn {
                color: #9ca3af;
            }

            .dark .week-day-btn strong {
                color: #f9fafb;
            }

            .dark .week-day-btn.is-selected {
                border-color: #34d399;
                background: rgba(6, 78, 59, 0.45);
                color: #a7f3d0;
            }

            .dark .week-day-btn.is-today:not(.is-selected) {
                background: #374151;
            }

            .schedule-grid {
                display: grid;
                min-width: 760px;
                user-select: none;
            }

            .schedule-sticky-cell {
                position: sticky;
                top: 0;
                z-index: 20;
            }

            .schedule-corner,
            .schedule-room-header,
            .schedule-time-cell,
            .schedule-cell {
                border-right: 1px solid #e5e7eb;
                border-bottom: 1px solid #e5e7eb;
            }

            .dark .schedule-corner,
            .dark .schedule-room-header,
            .dark .schedule-time-cell,
            .dark .schedule-cell {
                border-color: #374151;
            }

            .schedule-corner,
            .schedule-room-header {
                min-height: 4rem;
                background: #f9fafb;
            }

            .dark .schedule-corner,
            .dark .schedule-room-header {
                background: #111827;
            }

            .schedule-room-header {
                display: flex;
                flex-direction: column;
                justify-content: center;
                gap: 0.25rem;
                padding: 0.75rem;
            }

            .schedule-room-name {
                color: #111827;
                font-size: 0.9rem;
                font-weight: 700;
                line-height: 1.1;
            }

            .schedule-room-capacity {
                color: #6b7280;
                font-size: 0.72rem;
                font-weight: 600;
                text-transform: uppercase;
            }

            .dark .schedule-room-name {
                color: #f9fafb;
            }

            .dark .schedule-room-capacity {
                color: #9ca3af;
            }

            .schedule-time-cell {
                position: sticky;
                left: 0;
                z-index: 10;
                display: flex;
                align-items: flex-start;
                justify-content: center;
                min-height: 5.5rem;
                padding-top: 0.75rem;
                background: #ffffff;
                color: #6b7280;
                font-size: 0.72rem;
                font-weight: 700;
            }

            .dark .schedule-time-cell {
                background: #1f2937;
                color: #9ca3af;
            }

            .schedule-cell {
                position: relative;
                min-height: 5.5rem;
                padding: 0.5rem;
                background: #ffffff;
                cursor: crosshair;
                transition: background-color 0.15s ease, box-shadow 0.15s ease;
            }

            .schedule-cell:hover {
                background: #f0fdf4;
                box-shadow: inset 0 0 0 1px #34d399;
            }

            .schedule-cell.is-selecting {
                background: #d1fae5;
                box-shadow: inset 0 0 0 2px #10b981;
            }

            .schedule-cell.is-full {
                background: #f9fafb;
                cursor: not-allowed;
            }

            .schedule-cell.is-full:hover {
                box-shadow: inset 0 0 0 1px #d1d5db;
            }

            .dark .schedule-cell {
                background: #1f2937;
            }

            .dark .schedule-cell:hover {
                background: rgba(6, 78, 59, 0.32);
            }

            .dark .schedule-cell.is-selecting {
                background: rgba(16, 185, 129, 0.28);
            }

            .dark .schedule-cell.is-full {
                background: #111827;
            }

            .schedule-cell-meta {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.5rem;
                min-height: 1.5rem;
            }

            .capacity-badge {
                display: inline-flex;
                align-items: center;
                min-height: 1.25rem;
                border-radius: 999px;
                background: #ecfdf5;
                padding: 0 0.45rem;
                color: #047857;
                font-size: 0.68rem;
                font-weight: 800;
            }

            .capacity-badge.is-full {
                background: #fee2e2;
                color: #b91c1c;
            }

            .dark .capacity-badge {
                background: rgba(16, 185, 129, 0.16);
                color: #a7f3d0;
            }

            .dark .capacity-badge.is-full {
                background: rgba(239, 68, 68, 0.18);
                color: #fecaca;
            }

            .schedule-add-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 1.5rem;
                height: 1.5rem;
                border-radius: 999px;
                background: #10b981;
                color: #ffffff;
                opacity: 0.92;
                transition: background-color 0.15s ease, transform 0.15s ease;
            }

            .schedule-add-btn:hover {
                background: #059669;
                transform: scale(1.04);
            }

            .schedule-appointments {
                display: flex;
                flex-direction: column;
                gap: 0.375rem;
                margin-top: 0.375rem;
            }

            .appointment-chip {
                display: grid;
                width: 100%;
                gap: 0.125rem;
                border-left: 4px solid var(--appointment-color);
                border-radius: 0.5rem;
                background: #f8fafc;
                padding: 0.45rem 0.5rem;
                text-align: left;
                box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
                transition: transform 0.15s ease, box-shadow 0.15s ease;
            }

            .appointment-chip:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 10px rgba(15, 23, 42, 0.12);
            }

            .appointment-chip-main {
                overflow: hidden;
                color: #111827;
                font-size: 0.78rem;
                font-weight: 800;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .appointment-chip-detail,
            .appointment-chip-status {
                overflow: hidden;
                color: #6b7280;
                font-size: 0.68rem;
                font-weight: 600;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .appointment-chip.is-canceled {
                opacity: 0.72;
            }

            .appointment-chip.is-canceled .appointment-chip-main {
                text-decoration: line-through;
            }

            .dark .appointment-chip {
                background: #111827;
                box-shadow: none;
            }

            .dark .appointment-chip-main {
                color: #f9fafb;
            }

            .dark .appointment-chip-detail,
            .dark .appointment-chip-status {
                color: #9ca3af;
            }

            #schedule-grid-wrapper::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }

            #schedule-grid-wrapper::-webkit-scrollbar-track {
                background: transparent;
            }

            #schedule-grid-wrapper::-webkit-scrollbar-thumb {
                background: #d1d5db;
                border-radius: 20px;
            }

            #schedule-grid-wrapper::-webkit-scrollbar-thumb:hover {
                background: #9ca3af;
            }

            .dark #schedule-grid-wrapper::-webkit-scrollbar-thumb {
                background: #4b5563;
            }
        </style>
    @endpush
@endsection
