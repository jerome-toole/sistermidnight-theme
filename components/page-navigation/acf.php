<?php

acf_register_block_type([
    'name' => 'page-navigation',
    'title' => 'Page Navigation',
    'category' => 'theme-blocks',
    'icon' => 'list-view',
    'keywords' => [
        'navigation',
        'anchor',
        'jump',
        'links',
        'toc',
        'table of contents',
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

        echo \Geum\Components\PageNavigation::make($args);
    },
]);
