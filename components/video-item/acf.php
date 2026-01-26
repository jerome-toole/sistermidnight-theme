<?php

/**
 * Registers an ACF block for this component. Autoloaded via the `acf/init` hook.
 */
acf_register_block_type([
    'name' => 'video-item',
    'title' => 'Video Item',
    'description' => 'Video with thumbnail overlay',
    'category' => 'theme-blocks',
    'icon' => 'video-alt3',

    'keywords' => [
        'video',
        'youtube',
        'vimeo',
        'embed',
    ],

    'mode' => 'auto',
    'align' => 'wide',

    'supports' => [
        'anchor' => true,
        'align' => ['wide', 'full'],
        'color' => [
            'text' => false,
            'gradients' => false,
        ],
    ],

    'render_callback' => function ($block, $content, $is_preview, $post_id) {
        $args = \Geum\Component::generateArgsFromBlock($block, get_fields());

        echo \Geum\Components\VideoItem::make(...$args);
    },
]);
