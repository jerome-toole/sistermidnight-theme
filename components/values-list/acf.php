<?php

acf_register_block_type([
    'name' => 'values-list',
    'title' => 'Values List',
    'description' => 'Display numbered values or principles',
    'category' => 'theme-blocks',
    'icon' => 'editor-ol',
    'keywords' => [
        'values',
        'principles',
        'manifesto',
        'numbered',
        'list',
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

        echo \Geum\Components\ValuesList::make($args);
    },
]);
