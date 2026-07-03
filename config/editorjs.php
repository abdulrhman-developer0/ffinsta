<?php

return [
    /*
    |--------------------------------------------------------------------------
    | EditorJS Tools Configuration
    |--------------------------------------------------------------------------
    |
    | Define the tools and configurations that will be passed to EditorJS.
    | These configurations will be exposed to Javascript to initialize the editor.
    |
    */
    'tools' => [
        'header' => [
            'inlineToolbar' => true,
            'config' => [
                'placeholder' => 'Enter a heading',
                'levels' => [2, 3, 4, 5, 6],
                'defaultLevel' => 2,
            ]
        ],
        'quote' => [
            'inlineToolbar' => true,
        ],
        'delimiter' => [],
        'embed' => [
            'config' => [
                'services' => [
                    'youtube' => true,
                    'vimeo' => true,
                    'instagram' => true,
                    'twitter' => true,
                ]
            ]
        ],
        'list' => [
            'inlineToolbar' => true,
        ],
        'nestedList' => [
            'inlineToolbar' => true,
        ],
        'checklist' => [
            'inlineToolbar' => true,
        ],
        'marker' => [
            'shortcut' => 'CMD+SHIFT+M',
        ],
        'button' => [
            'inlineToolbar' => false,
        ]
    ]
];

