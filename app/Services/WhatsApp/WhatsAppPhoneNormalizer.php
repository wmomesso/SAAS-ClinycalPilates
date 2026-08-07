<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Str;

class WhatsAppPhoneNormalizer
{
    public function normalize(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $lower = Str::lower($value);
        if (Str::contains($lower, ['@g.us', '@broadcast', '@newsletter', '@lid'])) {
            return null;
        }

        $localPart = Str::before($value, '@');
        $localPart = Str::before($localPart, ':');
        $digits = preg_replace('/\D+/', '', $localPart) ?? '';
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        $countryCode = preg_replace('/\D+/', '', (string) config('whatsapp.default_country_code', '55')) ?: '55';
        if (in_array(strlen($digits), [10, 11], true)) {
            $digits = $countryCode.$digits;
        }

        return strlen($digits) >= 10 && strlen($digits) <= 15 ? $digits : null;
    }

    public function hash(string $normalizedPhone): string
    {
        return hash_hmac('sha256', $normalizedPhone, (string) config('app.key'));
    }

    public function mask(string $normalizedPhone): string
    {
        return str_repeat('*', max(strlen($normalizedPhone) - 4, 0)).substr($normalizedPhone, -4);
    }
}
