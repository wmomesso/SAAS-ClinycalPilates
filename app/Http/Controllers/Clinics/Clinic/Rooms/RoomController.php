<?php

namespace App\Http\Controllers\Clinics\Clinic\Rooms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinics\Clinic\Room\StoreRoomRequest;
use App\Models\Clinics\Clinic\Room\Room;
use RealRashid\SweetAlert\Facades\Alert;

class RoomController extends Controller
{
    public function __construct()
    {
        // Aplica a Policy automaticamente
        $this->authorizeResource(Room::class, 'room');
    }

    /**
     * Lista as salas da clínica.
     */
    public function index()
    {
        $rooms = Room::orderBy('name')
            ->get();

        return view('clinic.rooms.index', compact('rooms'));
    }

    /**
     * Exibe o formulário de criação.
     */
    public function create()
    {
        return view('clinic.rooms.create');
    }

    /**
     * Armazena uma nova sala.
     */
    public function store(StoreRoomRequest $request)
    {
        Room::create($request->validated());

        Alert::success('Sucesso', 'Sala cadastrada com sucesso.');

        return redirect()->route('rooms.index');
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit(Room $room)
    {
        return view('clinic.rooms.edit', compact('room'));
    }

    /**
     * Atualiza os dados da sala.
     */
    public function update(StoreRoomRequest $request, Room $room)
    {
        $room->update($request->validated());

        Alert::success('Sucesso', 'Sala atualizada com sucesso.');

        return redirect()->route('rooms.index');
    }

    /**
     * Remove a sala (ou desativa).
     */
    public function destroy(Room $room)
    {
        // Nota: Em sistemas de agendamento, o Soft Delete é essencial para não quebrar o histórico.
        $room->delete();

        Alert::success('Sucesso', 'Sala removida com sucesso.');

        return redirect()->route('rooms.index');
    }
}
