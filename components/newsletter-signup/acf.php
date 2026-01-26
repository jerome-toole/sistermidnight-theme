<?php

acf_register_block_type([
    'name' => 'newsletter-signup',
    'title' => 'Newsletter Signup',
    'category' => 'theme-blocks',
    'icon' => 'email-alt',
    'keywords' => [
        'newsletter',
        'email',
        'subscribe',
        'brevo',
        'mailing list',
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

        echo \Geum\Components\NewsletterSignup::make($args);
    },
]);
