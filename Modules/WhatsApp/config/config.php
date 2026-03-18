<?php

return [
    'name' => 'WhatsApp',

    'evolution_url'    => env('EVOLUTION_API_URL', 'http://localhost:8080'),
    'api_key'          => env('EVOLUTION_API_KEY', ''),
    'instance_name'    => env('EVOLUTION_INSTANCE_NAME', 'psiagenda'),
    'webhook_secret'   => env('EVOLUTION_WEBHOOK_SECRET', ''),
];
