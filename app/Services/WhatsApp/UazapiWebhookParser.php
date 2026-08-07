<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Str;

class UazapiWebhookParser
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function eventName(array $payload): string
    {
        return Str::limit((string) $this->first($payload, [
            'event', 'eventType', 'EventType', 'type', 'data.event',
        ], 'unknown'), 80, '');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function instanceId(array $payload): ?string
    {
        $id = $this->first($payload, [
            'instance.id', 'instance.instanceId', 'instance.instance_id',
            'instanceId', 'instance_id', 'data.instance.id',
        ]);

        return $id === null ? null : (string) $id;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function connectionStatus(array $payload): ?string
    {
        $status = $this->first($payload, [
            'instance.status', 'instance.state', 'status', 'state',
            'data.status', 'data.state', 'data.instance.status',
        ]);

        return $status === null ? null : Str::lower((string) $status);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function eventId(array $payload): string
    {
        $id = $this->messageId($payload);

        return $id !== null
            ? Str::limit($id, 191, '')
            : 'sha256:'.hash('sha256', (string) json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function messageId(array $payload): ?string
    {
        $id = $this->first($payload, [
            'messageid', 'messageId', 'id',
            'data.messageid', 'data.messageId', 'data.id',
            'message.messageid', 'message.messageId', 'message.id',
            'message.key.id', 'data.message.key.id',
        ]);

        return $id !== null && (string) $id !== '' ? (string) $id : null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function messageType(array $payload): ?string
    {
        $type = $this->first($payload, [
            'messageType', 'messagetype', 'type',
            'data.messageType', 'data.messagetype', 'data.type',
            'message.messageType', 'message.type',
        ]);

        return $type === null ? null : Str::limit(Str::lower((string) $type), 50, '');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function isAudio(array $payload): bool
    {
        $type = $this->messageType($payload);

        if ($type !== null && (Str::contains($type, 'audio') || Str::contains($type, 'ptt'))) {
            return true;
        }

        return $this->mediaMime($payload) !== null
            && Str::startsWith($this->mediaMime($payload), 'audio/');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function isFromMe(array $payload): bool
    {
        return filter_var($this->first($payload, [
            'fromMe', 'fromme', 'data.fromMe', 'data.fromme',
            'message.fromMe', 'message.key.fromMe', 'data.message.key.fromMe',
        ], false), FILTER_VALIDATE_BOOL);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function sender(array $payload): ?string
    {
        $sender = $this->first($payload, [
            'chatid', 'chatId', 'phone',
            'data.chatid', 'data.chatId', 'data.phone',
            'message.chatid', 'message.chatId', 'message.phone',
            'message.key.remoteJid', 'data.message.key.remoteJid',
            'from', 'data.from', 'message.from',
            'sender', 'data.sender', 'message.sender',
        ]);

        return $sender === null ? null : (string) $sender;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function text(array $payload): ?string
    {
        $text = $this->first($payload, [
            'text', 'body', 'content',
            'data.text', 'data.body', 'data.content',
            'message.text', 'message.body', 'message.content', 'message.content.text',
            'message.conversation', 'message.extendedTextMessage.text',
            'data.message.conversation', 'data.message.extendedTextMessage.text',
        ]);

        return is_string($text) && trim($text) !== '' ? trim($text) : null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function isGroup(array $payload): bool
    {
        $explicit = $this->first($payload, [
            'isGroup', 'isgroup', 'data.isGroup', 'data.isgroup', 'message.isGroup',
        ]);

        if ($explicit !== null && filter_var($explicit, FILTER_VALIDATE_BOOL)) {
            return true;
        }

        return Str::contains(Str::lower((string) $this->sender($payload)), ['@g.us', '@broadcast', '@newsletter']);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function mediaUrl(array $payload): ?string
    {
        $url = $this->first($payload, [
            'fileURL', 'fileUrl', 'mediaUrl', 'mediaURL', 'url', 'file',
            'data.fileURL', 'data.fileUrl', 'data.mediaUrl', 'data.mediaURL', 'data.url', 'data.file',
            'message.fileURL', 'message.mediaUrl', 'message.url', 'message.file',
            'message.content.url', 'message.content.URL',
            'message.audioMessage.url', 'data.message.audioMessage.url',
        ]);

        return is_string($url) && $url !== '' ? $url : null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function mediaBase64(array $payload): ?string
    {
        $base64 = $this->first($payload, [
            'base64', 'mediaBase64', 'fileBase64',
            'data.base64', 'data.mediaBase64', 'data.fileBase64',
            'message.base64', 'message.mediaBase64', 'message.content.base64',
        ]);

        return is_string($base64) && $base64 !== '' ? $base64 : null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function mediaMime(array $payload): ?string
    {
        $mime = $this->first($payload, [
            'mimetype', 'mimeType', 'mediaMime',
            'data.mimetype', 'data.mimeType', 'data.mediaMime',
            'message.mimetype', 'message.mimeType',
            'message.content.mimetype', 'message.content.mimeType',
            'message.audioMessage.mimetype', 'data.message.audioMessage.mimetype',
        ]);

        if (! is_string($mime) || $mime === '') {
            return null;
        }

        return Str::lower(trim(Str::before($mime, ';')));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  list<string>  $paths
     */
    private function first(array $payload, array $paths, mixed $default = null): mixed
    {
        foreach ($paths as $path) {
            $value = data_get($payload, $path);
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return $default;
    }
}
