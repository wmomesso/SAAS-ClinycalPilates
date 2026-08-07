<?php

namespace App\Console\Commands;

use App\Models\Clinics\Clinic\Patient\PatientDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SecurePatientDocumentsCommand extends Command
{
    protected $signature = 'clinic:secure-patient-documents {--dry-run}';

    protected $description = 'Move documentos clínicos antigos do disco público para o armazenamento privado.';

    public function handle(): int
    {
        $moved = 0;
        $missing = 0;

        PatientDocument::query()->orderBy('id')->each(function (PatientDocument $document) use (&$moved, &$missing): void {
            if (Storage::disk('local')->exists($document->file_path)) {
                return;
            }

            if (! Storage::disk('public')->exists($document->file_path)) {
                $missing++;

                return;
            }

            if (! $this->option('dry-run')) {
                $contents = Storage::disk('public')->get($document->file_path);
                Storage::disk('local')->put($document->file_path, $contents);
                Storage::disk('public')->delete($document->file_path);
            }

            $moved++;
        });

        $verb = $this->option('dry-run') ? 'seriam movidos' : 'movidos';
        $this->info("{$moved} documento(s) {$verb}; {$missing} arquivo(s) não localizado(s).");

        return $missing === 0 ? self::SUCCESS : self::FAILURE;
    }
}
