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
            'config' => [ 'placeholder' => 'Heading 1', 'levels' => [1], 'defaultLevel' => 1 ]
        ],
        'heading2' => [
            'inlineToolbar' => true,
            'config' => [ 'placeholder' => 'Heading 2', 'levels' => [2], 'defaultLevel' => 2 ]
        ],
        'heading3' => [
            'inlineToolbar' => true,
            'config' => [ 'placeholder' => 'Heading 3', 'levels' => [3], 'defaultLevel' => 3 ]
        ],
        'heading4' => [
            'inlineToolbar' => true,
            'config' => [ 'placeholder' => 'Heading 4', 'levels' => [4], 'defaultLevel' => 4 ]
        ],
        'heading5' => [
            'inlineToolbar' => true,
            'config' => [ 'placeholder' => 'Heading 5', 'levels' => [5], 'defaultLevel' => 5 ]
        ],
        'heading6' => [
            'inlineToolbar' => true,
            'config' => [ 'placeholder' => 'Heading 6', 'levels' => [6], 'defaultLevel' => 6 ]
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
