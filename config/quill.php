<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Quill Theme
    |--------------------------------------------------------------------------
    |
    | The theme to use for the Quill editor. Available options: 'snow', 'bubble'
    |
    */
    'theme' => env('QUILL_THEME', 'snow'),

    /*
    |--------------------------------------------------------------------------
    | CDN Configuration
    |--------------------------------------------------------------------------
    |
    | Configure whether to use CDN for Quill assets or local files.
    |
    */
    'cdn' => [
        'enabled' => env('QUILL_CDN_ENABLED', true),
        'version' => env('QUILL_VERSION', '2.0.2'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Toolbar Configuration
    |--------------------------------------------------------------------------
    |
    | Define the default toolbar options for the editor.
    | Set to false to use Quill's default toolbar.
    |
    */
    'toolbar' => [
        ['header' => [1, 2, 3, 4, 5, 6, false]],
        ['font' => []],
        ['size' => ['small', false, 'large', 'huge']],
        'bold', 'italic', 'underline', 'strike',
        ['color' => []], ['background' => []],
        ['script' => 'sub'], ['script' => 'super'],
        ['list' => 'ordered'], ['list' => 'bullet'],
        ['indent' => '-1'], ['indent' => '+1'],
        ['direction' => 'rtl'],
        ['align' => []],
        'link', 'image', 'video', 'formula',
        'blockquote', 'code-block',
        'clean',
    ],

    /*
    |--------------------------------------------------------------------------
    | Toolbar Presets
    |--------------------------------------------------------------------------
    |
    | Define preset toolbar configurations for different use cases.
    |
    */
    'toolbars' => [
        'minimal' => [
            'bold', 'italic', 'underline',
            ['list' => 'ordered'], ['list' => 'bullet'],
            'link',
            'clean',
        ],
        'basic' => [
            ['header' => [1, 2, 3, false]],
            'bold', 'italic', 'underline', 'strike',
            ['list' => 'ordered'], ['list' => 'bullet'],
            'link', 'image',
            'clean',
        ],
        'full' => [
            ['header' => [1, 2, 3, 4, 5, 6, false]],
            ['font' => []],
            ['size' => ['small', false, 'large', 'huge']],
            'bold', 'italic', 'underline', 'strike',
            ['color' => []], ['background' => []],
            ['script' => 'sub'], ['script' => 'super'],
            ['list' => 'ordered'], ['list' => 'bullet'], ['list' => 'check'],
            ['indent' => '-1'], ['indent' => '+1'],
            ['direction' => 'rtl'],
            ['align' => []],
            'link', 'image', 'video', 'formula',
            'blockquote', 'code-block',
            'clean',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the routes for image uploads and other server-side features.
    |
    */
    'routes' => [
        'enabled' => true,
        'prefix' => 'quill',
        'middleware' => ['web'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the image upload settings for the editor.
    |
    */
    'uploads' => [
        'enabled' => true,
        'disk' => env('QUILL_UPLOAD_DISK', 'public'),
        'path' => env('QUILL_UPLOAD_PATH', 'quill-uploads'),
        'max_size' => env('QUILL_MAX_UPLOAD_SIZE', 2048), // in KB
        'allowed_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        'generate_thumbnails' => false,
        'thumbnail_sizes' => [
            'small' => [150, 150],
            'medium' => [300, 300],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Sanitization
    |--------------------------------------------------------------------------
    |
    | Configure HTML sanitization for editor content.
    |
    */
    'sanitize' => [
        'enabled' => true,
        'allowed_tags' => [
            'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'a', 'img',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'blockquote', 'pre', 'code',
            'ul', 'ol', 'li',
            'span', 'div',
            'sub', 'sup',
            'table', 'thead', 'tbody', 'tr', 'th', 'td',
            'iframe', 'video', 'source',
        ],
        'allowed_attributes' => [
            'a' => ['href', 'target', 'rel'],
            'img' => ['src', 'alt', 'width', 'height', 'class'],
            'iframe' => ['src', 'width', 'height', 'frameborder', 'allowfullscreen', 'class'],
            'video' => ['src', 'width', 'height', 'controls', 'class'],
            'source' => ['src', 'type'],
            '*' => ['class', 'style'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Editor Modules
    |--------------------------------------------------------------------------
    |
    | Configure additional Quill modules.
    |
    */
    'modules' => [
        'syntax' => false, // Requires highlight.js
        'table' => false,
        'imageResize' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Placeholder Text
    |--------------------------------------------------------------------------
    |
    | Default placeholder text for the editor.
    |
    */
    'placeholder' => 'Write something...',

    /*
    |--------------------------------------------------------------------------
    | Read Only Mode
    |--------------------------------------------------------------------------
    |
    | Set to true to make all editors read-only by default.
    |
    */
    'read_only' => false,

    /*
    |--------------------------------------------------------------------------
    | Bounds
    |--------------------------------------------------------------------------
    |
    | DOM element or CSS selector to constrain the editor to.
    |
    */
    'bounds' => null,

    /*
    |--------------------------------------------------------------------------
    | Scroll Container
    |--------------------------------------------------------------------------
    |
    | DOM element or CSS selector for the scroll container.
    |
    */
    'scroll_container' => null,
];
