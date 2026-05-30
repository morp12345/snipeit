<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Active AI Provider
    |--------------------------------------------------------------------------
    | Supported: "anthropic", "openai", "gemini", "ollama"
    */
    'provider' => env('AI_PROVIDER', 'anthropic'),

    'providers' => [

        'anthropic' => [
            'api_key' => env('ANTHROPIC_API_KEY'),
            'model'   => env('ANTHROPIC_MODEL', 'claude-sonnet-4-6'),
            'base_url' => 'https://api.anthropic.com/v1',
        ],

        'openai' => [
            'api_key'  => env('OPENAI_API_KEY'),
            'model'    => env('OPENAI_MODEL', 'gpt-4o'),
            // null = standard OpenAI; set to Azure endpoint URL for Azure OpenAI
            'base_url' => env('OPENAI_API_BASE', 'https://api.openai.com/v1'),
        ],

        'gemini' => [
            'api_key' => env('GEMINI_API_KEY'),
            'model'   => env('GEMINI_MODEL', 'gemini-2.0-flash'),
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
        ],

        'ollama' => [
            'api_key'  => null, // no auth needed for local Ollama
            'model'    => env('OLLAMA_MODEL', 'llama3.2'),
            'base_url' => env('OLLAMA_BASE_URL', 'http://127.0.0.1:11434'),
        ],

    ],

];
