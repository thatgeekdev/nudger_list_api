<?php

return [

    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'Nudger List API',
            ],

            'routes' => [
                'api' => 'api/documentation',
                'docs_json' => 'api-docs.json',
                'docs' => 'docs',
            ],

            'paths' => [
                'docs' => storage_path('api-docs'), // Directory where docs are stored
                'docs_json' => 'api-docs.json',     // JSON file name, relative to 'docs'
                'annotations' => [
                    base_path('app/Http/Controllers/API'),
                    base_path('app/Swagger'),
                ],
            ],
        ],
    ],


];
