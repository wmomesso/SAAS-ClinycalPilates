<?php

namespace App\Services\WhatsApp;

use App\Models\User;
use App\Models\WhatsAppActivationCode;
use App\Models\WhatsAppPhoneBinding;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WhatsAppActivationService
{
    public function __construct(private readonly WhatsAppPhoneNormalizer $phones) {}

    public function generate(User $user): string
    {
        if ($user->clinic_id === null) {
            throw new RuntimeException('O usuário precisa pertencer a uma clínica para ativar o WhatsApp.');
        }

        WhatsAppActivationCode::query()
            ->where('user_id', $user->id)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $code = 'FIN-'.str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            try {
                WhatsAppActivationCode::query()->create([
                    'user_id' => $user->id,
                    'clinic_id' => $user->clinic_id,
                    'code_hash' => $this->hashCode($code),
                    'expires_at' => now()->addMinutes((int) config('whatsapp.activation_code_ttl_minutes', 10)),
                ]);

                return $code;
            } catch (UniqueConstraintViolationException) {
                // Tenta outro código sem expor ou reutilizar a colisão.
            }
        }

        throw new RuntimeException('Não foi possível gerar um código de ativação. Tente novamente.');
    }

    public function codeFromMessage(?string $message): ?string
    {
        if ($message === null || ! preg_match('/\bFIN-[0-9]{6}\b/i', $message, $matches)) {
            return null;
        }

        return strtoupper($matches[0]);
    }

    public function activate(string $normalizedPhone, string $code): ?WhatsAppPhoneBinding
    {
        return DB::transaction(function () use ($normalizedPhone, $code): ?WhatsAppPhoneBinding {
            $activation = WhatsAppActivationCode::query()
                ->where('code_hash', $this->hashCode($code))
                ->whereNull('consumed_at')
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->first();

            if ($activation === null) {
                return null;
            }

            $phoneHash = $this->phones->hash($normalizedPhone);

            WhatsAppPhoneBinding::query()
                ->where('user_id', $activation->user_id)
                ->where('phone_hash', '!=', $phoneHash)
                ->delete();

            $binding = WhatsAppPhoneBinding::query()->updateOrCreate(
                ['phone_hash' => $phoneHash],
                [
                    'user_id' => $activation->user_id,
                    'clinic_id' => $activation->clinic_id,
                    'phone' => $normalizedPhone,
                    'is_active' => true,
                    'bound_at' => now(),
                ]
            );

            $activation->update(['consumed_at' => now()]);

            return $binding;
        }, 3);
    }

    private function hashCode(string $code): string
    {
        return hash_hmac('sha256', strtoupper(trim($code)), (string) config('app.key'));
    }
}
