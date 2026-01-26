<?php

/**
 * Registers an ACF block for this component.
 *
 * @link https://www.advancedcustomfields.com/resources/acf_register_block_type/
 */
acf_register_block_type([
    'name' => 'gallery',
    'title' => 'Gallery',
    'description' => 'Image carousel/slider with captions and credits',
    'category' => 'theme-blocks',
    'icon' => 'format-gallery',
    'mode' => 'auto',
    'align' => 'full',
    'supports' => [
        'anchor' => true,
        'align' => false,
    ],
    'render_callback' => function ($block, $content, $is_preview, $post_id) {
        $args = \Geum\Component::generateArgsFromBlock($block, get_fields());
        echo \Geum\Components\Gallery::make(...$args);
    },
]);
