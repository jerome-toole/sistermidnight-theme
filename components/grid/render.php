<?php

/**
 * Grid — server-side render.
 *
 * @var array $attributes Block attributes.
 * @var string $content    Inner block content.
 * @var WP_Block $block      Block instance.
 */
$wrapper_attributes = [
    'class' => 'grid-block grid-simple alignwide',
    'data-cols' => (int) ($attributes['columns'] ?? 3),
    'data-stack' => $attributes['breakpoint'] ?? 'tablet',
];

if (! empty($attributes['anchor'])) {
    $wrapper_attributes['id'] = $attributes['anchor'];
}

printf(
    '<div %s>%s</div>',
    get_block_wrapper_attributes($wrapper_attributes),
    $content
);
