<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsAppIntegration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class UazapiMediaDownloader
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array{contents: string, mime: string, extension: string}
     */
    public function audio(WhatsAppIntegration $integration, array $payload, UazapiWebhookParser $parser): array
    {
        $contents = $this->contents($integration, $payload, $parser);
        $maxBytes = (int) config('transcription.max_audio_bytes');

        if (strlen($contents) > $maxBytes) {
            throw new RuntimeException("O áudio recebido excede o limite de {$maxBytes} bytes.");
        }

        $detectedMime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($contents) ?: null;
        $declaredMime = $parser->mediaMime($payload);
        $mime = Str::lower((string) ($detectedMime ?: $declaredMime ?: 'application/octet-stream'));

        if (! Str::startsWith($mime, 'audio/') && $mime !== 'application/octet-stream') {
            throw new RuntimeException("O conteúdo recebido não é um áudio permitido [{$mime}].");
        }

        return [
            'contents' => $contents,
            'mime' => $mime,
            'extension' => $this->extensionFor($mime),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function contents(WhatsAppIntegration $integration, array $payload, UazapiWebhookParser $parser): string
    {
        if ($base64 = $parser->mediaBase64($payload)) {
            if (Str::startsWith($base64, 'data:')) {
                $base64 = Str::after($base64, ',');
            }

            $decoded = base64_decode($base64, true);
            if ($decoded === false) {
                throw new RuntimeException('O áudio em base64 recebido é inválido.');
            }

            return $decoded;
        }

        if ($messageId = $parser->messageId($payload)) {
            $download = Http::accept('*/*')
                ->asJson()
                ->withHeaders(['token' => $integration->token])
                ->withOptions(['allow_redirects' => false])
                ->timeout((int) config('whatsapp.uazapi.webhook.http_timeout', 30))
                ->post(rtrim($integration->base_url, '/').'/message/download', [
                    'id' => $messageId,
                    'transcribe' => false,
                ]);

            if ($download->successful()) {
                $contentType = Str::lower((string) $download->header('Content-Type'));
                if (Str::startsWith($contentType, 'audio/')) {
                    return $download->body();
                }

                $downloadPayload = $download->json();
                if (is_array($downloadPayload)) {
                    if ($downloadBase64 = $parser->mediaBase64($downloadPayload)) {
                        if (Str::startsWith($downloadBase64, 'data:')) {
                            $downloadBase64 = Str::after($downloadBase64, ',');
                        }

                        $decoded = base64_decode($downloadBase64, true);
                        if ($decoded !== false) {
                            return $decoded;
                        }
                    }

                    if ($downloadUrl = $parser->mediaUrl($downloadPayload)) {
                        return $this->downloadUrl($integration, $downloadUrl);
                    }
                }
            }
        }

        $url = $parser->mediaUrl($payload);
        if ($url === null) {
            throw new RuntimeException('A Uazapi não forneceu URL nem base64 para baixar o áudio.');
        }

        return $this->downloadUrl($integration, $url);
    }

    private function downloadUrl(WhatsAppIntegration $integration, string $url): string
    {
        $this->assertUrlAllowed($integration, $url);

        $response = Http::accept('*/*')
            ->withHeaders(['token' => $integration->token])
            ->withOptions(['allow_redirects' => false])
            ->timeout((int) config('whatsapp.uazapi.webhook.http_timeout', 30))
            ->get($url);

        if (! $response->successful()) {
            throw new RuntimeException("Falha ao baixar o áudio da Uazapi (HTTP {$response->status()}).");
        }

        return $response->body();
    }

    private function assertUrlAllowed(WhatsAppIntegration $integration, string $url): void
    {
        $parts = parse_url($url);
        if (! is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            throw new RuntimeException('A URL do áudio recebida é inválida.');
        }

        $scheme = Str::lower((string) $parts['scheme']);
        if ($scheme !== 'https' && ! (bool) config('whatsapp.uazapi.webhook.allow_http_media')) {
            throw new RuntimeException('A URL do áudio precisa usar HTTPS.');
        }

        if (isset($parts['user']) || isset($parts['pass'])) {
            throw new RuntimeException('A URL do áudio não pode conter credenciais.');
        }

        $baseHost = Str::lower((string) parse_url($integration->base_url, PHP_URL_HOST));
        $allowedHosts = array_map(
            static fn (string $host): string => Str::lower($host),
            (array) config('whatsapp.uazapi.webhook.allowed_media_hosts', [])
        );
        $allowedHosts[] = $baseHost;

        if (! in_array(Str::lower((string) $parts['host']), array_filter($allowedHosts), true)) {
            throw new RuntimeException('O host da URL do áudio não está na lista permitida.');
        }
    }

    private function extensionFor(string $mime): string
    {
        return match ($mime) {
            'audio/ogg', 'audio/opus' => 'ogg',
            'audio/mpeg', 'audio/mp3' => 'mp3',
            'audio/mp4', 'audio/x-m4a' => 'm4a',
            'audio/wav', 'audio/x-wav' => 'wav',
            'audio/webm' => 'webm',
            default => 'bin',
        };
    }
}
