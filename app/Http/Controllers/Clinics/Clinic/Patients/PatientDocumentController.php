<?php

namespace App\Http\Controllers\Clinics\Clinic\Patients;

use App\Http\Controllers\Controller;
use App\Models\Clinics\Clinic\Patient\Patient;
use App\Models\Clinics\Clinic\Patient\PatientDocument;
use App\Models\SecurityAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PatientDocumentController extends Controller
{
    public function store(Request $request, Patient $patient)
    {
        $this->authorize('update', $patient);

        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store("patients/{$patient->id}/documents", 'local');

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

    public function download(PatientDocument $document): StreamedResponse
    {
        $patient = $document->patient;
        abort_unless($patient, 404);
        $this->authorize('view', $patient);
        abort_unless(Storage::disk('local')->exists($document->file_path), 404);
        SecurityAuditLog::record('downloaded_patient_document', $document);

        return Storage::disk('local')->download(
            $document->file_path,
            $document->name,
            ['Content-Type' => $document->mime_type],
        );
    }

    public function destroy(PatientDocument $document)
    {
        $patient = $document->patient;
        abort_unless($patient, 404);
        abort_unless(auth()->user()->hasAnyRole(['admin-clinica', 'super-admin']), 403);
        $this->authorize('update', $patient);

        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return redirect()->back()->with('success', 'Documento removido.');
    }
}
