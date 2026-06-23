<?php

namespace App\Http\Controllers\Clinics\Clinic\Patients;

use App\Http\Controllers\Controller;
use App\Models\Clinics\Clinic\Patient\Patient;
use App\Models\Clinics\Clinic\Patient\PatientDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PatientDocumentController extends Controller
{
    public function store(Request $request, Patient $patient)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // max 10MB
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store("patients/{$patient->id}/documents", 'public');

            $patient->documents()->create([
                'uploaded_by_id' => Auth::id(),
                'name' => $request->name,
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        }

        return redirect()->back()->with('success', 'Documento anexado com sucesso.');
    }

    public function destroy(PatientDocument $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->back()->with('success', 'Documento removido.');
    }
}
