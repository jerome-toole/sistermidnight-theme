<?php

acf_register_block_type([
    'name' => 'team-grid',
    'title' => 'Team Grid',
    'description' => 'Display team members in a responsive grid',
    'category' => 'theme-blocks',
    'icon' => 'groups',
    'keywords' => [
        'team',
        'people',
        'staff',
        'members',
        'grid',
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

        echo \Geum\Components\TeamGrid::make($args);
    },
]);
