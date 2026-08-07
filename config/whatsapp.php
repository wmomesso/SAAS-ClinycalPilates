<?php

return [
    'provider' => env('WHATSAPP_DRIVER', env('WHATSAPP_PROVIDER', 'uazapi')),

    'enabled' => env('WHATSAPP_ENABLED', true),
    'log_only' => env('WHATSAPP_LOG_ONLY', false),
    'default_country_code' => env('WHATSAPP_DEFAULT_COUNTRY_CODE', '55'),
    'public_number' => env('WHATSAPP_PUBLIC_NUMBER'),
    'alert_email' => env('WHATSAPP_ALERT_EMAIL'),
    'webhook_secret' => env('WHATSAPP_WEBHOOK_SECRET'),
    'activation_code_ttl_minutes' => (int) env('WHATSAPP_ACTIVATION_CODE_TTL_MINUTES', 10),
    'activation_max_attempts' => (int) env('WHATSAPP_ACTIVATION_MAX_ATTEMPTS', 10),

    'patients' => [
        'enabled' => env('WHATSAPP_PATIENT_AUTOMATION_ENABLED', false),
        'reminder_hours_before' => (int) env('WHATSAPP_PATIENT_REMINDER_HOURS_BEFORE', 24),
        'reminder_repeat_minutes' => (int) env('WHATSAPP_PATIENT_REMINDER_REPEAT_MINUTES', 180),
        'reminder_max_attempts' => (int) env('WHATSAPP_PATIENT_REMINDER_MAX_ATTEMPTS', 3),
        'reminder_stop_minutes_before' => (int) env('WHATSAPP_PATIENT_REMINDER_STOP_MINUTES_BEFORE', 60),
        'recent_reminder_hours' => (int) env('WHATSAPP_PATIENT_RECENT_REMINDER_HOURS', 48),
        'dispatch_batch_size' => (int) env('WHATSAPP_PATIENT_TASK_BATCH_SIZE', 100),
        'stale_task_minutes' => (int) env('WHATSAPP_PATIENT_TASK_STALE_MINUTES', 10),
        'max_requests_per_ten_minutes' => (int) env('WHATSAPP_PATIENT_MAX_REQUESTS_PER_10_MINUTES', 30),
    ],

    'uazapi' => [
        'base_url' => env('UAZAPI_BASE_URL'),
        'token' => env('UAZAPI_TOKEN'),
        'instance_id' => env('UAZAPI_INSTANCE_ID'),
        'webhook' => [
            'max_payload_bytes' => (int) env('UAZAPI_WEBHOOK_MAX_PAYLOAD_BYTES', 25 * 1024 * 1024),
            'events' => array_values(array_filter(array_map('trim', explode(',', (string) env(
                'UAZAPI_WEBHOOK_EVENTS',
                'messages,messages_update,connection'
            ))))),
            'exclude_messages' => array_values(array_filter(array_map('trim', explode(',', (string) env(
                'UAZAPI_WEBHOOK_EXCLUDE_MESSAGES',
                'wasSentByApi,fromMeYes,isGroupYes'
            ))))),
            'allowed_media_hosts' => array_values(array_filter(array_map('trim', explode(',', (string) env(
                'UAZAPI_ALLOWED_MEDIA_HOSTS',
                ''
            ))))),
            'allow_http_media' => env('UAZAPI_ALLOW_HTTP_MEDIA', false),
            'http_timeout' => (int) env('UAZAPI_HTTP_TIMEOUT', 30),
        ],
    ],
];
