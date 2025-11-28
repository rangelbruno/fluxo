<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI API
    |--------------------------------------------------------------------------
    */
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Groq API (fallback)
    |--------------------------------------------------------------------------
    */
    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | OCR.space API (gratuito para OCR de imagens)
    |--------------------------------------------------------------------------
    */
    'ocr_space' => [
        'api_key' => env('OCR_SPACE_API_KEY', 'helloworld'),
    ],

    /*
    |--------------------------------------------------------------------------
    | n8n Integration
    |--------------------------------------------------------------------------
    |
    | Configurações para integração com n8n workflows.
    | O api_token é usado para autenticar requisições vindas do n8n.
    | allowed_ips é uma lista de IPs que podem acessar a API sem token.
    |
    */
    'n8n' => [
        'api_token' => env('N8N_API_TOKEN'),
        'api_key' => env('N8N_API_KEY'),
        'api_url' => env('N8N_API_URL', 'http://fluxo-n8n:5679'),
        'webhook_url' => env('N8N_WEBHOOK_URL', 'http://fluxo-n8n:5678'),
        'external_url' => env('N8N_EXTERNAL_URL', 'http://localhost:5679'),
        'allowed_ips' => array_filter(explode(',', env('N8N_ALLOWED_IPS', '127.0.0.1,::1,172.16.0.0/12,192.168.0.0/16,10.0.0.0/8'))),
    ],

];
