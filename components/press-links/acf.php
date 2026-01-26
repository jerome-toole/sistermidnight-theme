<?php

acf_register_block_type([
    'name' => 'press-links',
    'title' => 'Press Links',
    'category' => 'theme-blocks',
    'icon' => 'media-document',
    'keywords' => [
        'press',
        'media',
        'articles',
        'coverage',
        'news',
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

        echo \Geum\Components\PressLinks::make($args);
    },
]);
