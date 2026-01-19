<?php

namespace App\Http\Controllers\Clinics\Clinic\Rooms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinics\Clinic\Room\StoreRoomRequest;
use App\Models\Clinics\Clinic\Room\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoomController extends Controller
{
    use AuthorizesRequests;

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
        $rooms = Room::where('clinic_id', Auth::user()->clinic_id)
            ->orderBy('name')
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
        $data = $request->validated();
        $data['clinic_id'] = Auth::user()->clinic_id;

        Room::create($data);

        return redirect()->route('rooms.index')
            ->with('success', 'Sala cadastrada com sucesso.');
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

        return redirect()->route('rooms.index')
            ->with('success', 'Sala atualizada com sucesso.');
    }

    /**
     * Remove a sala (ou desativa).
     */
    public function destroy(Room $room)
    {
        // Nota: Em sistemas de agendamento, o Soft Delete é essencial para não quebrar o histórico.
        $room->delete();

        return redirect()->route('rooms.index')
            ->with('success', 'Sala removida com sucesso.');
    }
}
