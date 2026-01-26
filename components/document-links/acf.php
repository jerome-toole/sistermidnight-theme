<?php

acf_register_block_type([
    'name' => 'document-links',
    'title' => 'Document Links',
    'category' => 'theme-blocks',
    'icon' => 'media-document',
    'keywords' => [
        'document',
        'download',
        'file',
        'pdf',
        'attachment',
    ],
    'post_types' => [
        'page',
        'geum-template',
    ],
    'mode' => 'preview',
    'align' => '',
    'supports' => [
        'anchor' => true,
        'align' => ['wide', 'full'],
        'color' => [
            'background' => true,
            'text' => false,
            'gradients' => false,
        ],
    ],
    'render_callback' => function ($block, $content, $is_preview, $post_id) {
        $args = \Geum\Component::generateArgsFromBlock($block, get_fields());

        echo \Geum\Components\DocumentLinks::make($args);
    },
]);
