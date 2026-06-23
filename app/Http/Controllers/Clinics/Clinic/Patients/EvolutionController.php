<?php

namespace App\Http\Controllers\Clinics\Clinic\Patients;

use App\Http\Controllers\Controller;
use App\Models\Clinics\Clinic\Patient\Evolution;
use App\Models\Clinics\Clinic\Patient\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvolutionController extends Controller
{
    public function store(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'type' => 'required|in:routine,emergency,evaluation,other',
            'description' => 'required|string',
        ]);

        $patient->evolutions()->create([
            'professional_id' => Auth::id(),
            'title' => $validated['title'],
            'type' => $validated['type'],
            'description' => $validated['description'],
        ]);

        return redirect()->back()->with('success', 'Evolução registrada com sucesso.');
    }

    public function destroy(Evolution $evolution)
    {
        $evolution->delete();

        return redirect()->back()->with('success', 'Evolução removida.');
    }
}
