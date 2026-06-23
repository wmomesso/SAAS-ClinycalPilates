<?php

namespace App\Http\Controllers\Clinics\Clinic\Patients;

use App\Http\Controllers\Controller;
use App\Models\Clinics\Clinic\Patient\Anamnesis;
use App\Models\Clinics\Clinic\Patient\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnamnesisController extends Controller
{
    public function store(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'main_complaint' => 'required|string',
            'history_of_current_illness' => 'required|string',
            'dynamic_form' => 'nullable|array',
        ]);

        $patient->anamneses()->create([
            'professional_id' => Auth::id(),
            'main_complaint' => $validated['main_complaint'],
            'history_of_current_illness' => $validated['history_of_current_illness'],
            'dynamic_form' => $validated['dynamic_form'] ?? [],
        ]);

        return redirect()->back()->with('success', 'Anamnese registrada com sucesso.');
    }

    public function show(Anamnesis $anamnesis)
    {
        return response()->json($anamnesis->load('professional'));
    }

    public function compare(Request $request, Patient $patient)
    {
        $ids = $request->validate([
            'ids' => 'required|array|min:2|max:2',
            'ids.*' => 'exists:anamneses,id',
        ]);

        $anamneses = Anamnesis::whereIn('id', $ids['ids'])
            ->with('professional')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('patients.anamnesis.compare', compact('patient', 'anamneses'));
    }
}
