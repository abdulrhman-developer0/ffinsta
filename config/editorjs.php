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
        'heading1' => [
            'inlineToolbar' => true,
        ],
        'heading2' => [
            'inlineToolbar' => true,
        ],
        'heading3' => [
            'inlineToolbar' => true,
        ],
        'heading4' => [
            'inlineToolbar' => true,
        ],
        'heading5' => [
            'inlineToolbar' => true,
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

