<?php

use App\Services\WhatsApp\PatientWhatsAppIntentParser;

test('negative appointment responses are never interpreted as confirmations', function (string $message) {
    $intent = app(PatientWhatsAppIntentParser::class)->parse($message);

    expect($intent)->toBe(PatientWhatsAppIntentParser::CANCELLATION_REQUEST);
})->with([
    'Não vou comparecer',
    'Não confirmar',
    'Quero cancelar',
]);

test('positive appointment responses are interpreted as confirmations', function (string $message) {
    $intent = app(PatientWhatsAppIntentParser::class)->parse($message);

    expect($intent)->toBe(PatientWhatsAppIntentParser::APPOINTMENT_CONFIRMATION);
})->with([
    'Confirmar',
    'Confirmo minha presença',
    'Vou comparecer',
]);
