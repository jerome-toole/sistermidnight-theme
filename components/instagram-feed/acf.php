<?php

acf_register_block_type([
    'name' => 'instagram-feed',
    'title' => 'Instagram Feed',
    'category' => 'theme-blocks',
    'icon' => 'instagram',
    'keywords' => [
        'instagram',
        'social',
        'feed',
        'grid',
        'images',
        'posts',
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

        echo \Geum\Components\InstagramFeed::make($args);
    },
]);
