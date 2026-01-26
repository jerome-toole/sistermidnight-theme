<?php

acf_register_block_type([
    'name' => 'stats-display',
    'title' => 'Stats Display',
    'category' => 'theme-blocks',
    'icon' => 'chart-bar',
    'keywords' => [
        'stats',
        'numbers',
        'fundraising',
        'counter',
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

        echo \Geum\Components\StatsDisplay::make($args);
    },
]);
