<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ollama Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for Ollama API integration.
    |
    */

    'url' => env('OLLAMA_URL', 'http://ollama:11434/api/generate'),

    'model' => env('OLLAMA_MODEL', 'llama3'),

];
