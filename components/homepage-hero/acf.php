<?php

acf_register_block_type([
    'name' => 'homepage-hero',
    'title' => 'Homepage Hero',
    'category' => 'theme-blocks',
    'icon' => 'star-filled',
    'keywords' => [
        'hero',
        'home',
        'landing',
    ],
    'post_types' => [
        'page',
        'geum-template',
    ],
    'mode' => 'preview',
    'align' => 'full',
    'supports' => [
        'anchor' => true,
        'align' => false,
        'color' => [
            'background' => false,
            'text' => false,
            'gradients' => false,
        ],
        'multiple' => false,
    ],
    'render_callback' => function ($block, $content, $is_preview, $post_id) {
        $args = \Geum\Component::generateArgsFromBlock($block, get_fields());

        echo \Geum\Components\HomepageHero::make($args);
    },
]);
