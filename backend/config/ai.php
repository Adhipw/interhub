<?php

return [
    'default' => env('AI_PROVIDER', 'fake'),

    'providers' => [
        'gemini' => [
            'key' => env('GEMINI_API_KEY'),
            'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
        ],
        'local' => [
            'base_url' => env('LOCAL_LLM_URL', 'http://localhost:11434'),
            'model' => env('LOCAL_LLM_MODEL', 'llama3'),
        ],
    ],

    'rate_limiting' => [
        'enabled' => true,
        'max_requests_per_hour' => env('AI_MAX_REQUESTS_PER_HOUR', 50),
    ],

    'safety' => [
        'enabled' => true,
        'blocked_keywords' => [
            'password', 'secret', 'token', 'key', 'credential',
        ],
    ],
];
