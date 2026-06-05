<?php

/**
 * إعدادات مزوّد التوليد النصي (Gemini) للمساعد التعليمي.
 */
return [
    'enabled' => filter_var(env('GEMINI_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    'api_key' => env('GEMINI_API_KEY'),

    'model' => env('GEMINI_MODEL', 'gemini-flash-latest'),

    'base_url' => rtrim((string) env('GEMINI_API_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'), '/'),

    'http_timeout' => (int) env('GEMINI_HTTP_TIMEOUT', 60),

    'max_output_tokens' => (int) env('GEMINI_MAX_OUTPUT_TOKENS', 8192),

    'max_output_tokens_educational_game' => (int) env('GEMINI_MAX_OUTPUT_TOKENS_GAME', 16384),
];
