<?php

/**
 * Gallery Examples
 *
 * Swiper-powered image carousel.
 * Captions from attachment caption field, credits from 'credit' meta.
 */

use Gust\Components\Gallery;

// Fetch latest 4 and 5 image IDs from the media library
$latest_4 = get_posts([
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'posts_per_page' => 4,
    'orderby' => 'date',
    'order' => 'DESC',
    'fields' => 'ids',
]);
$latest_5 = get_posts([
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'posts_per_page' => 5,
    'orderby' => 'date',
    'order' => 'DESC',
    'fields' => 'ids',
]);

// Basic usage with heading
echo Gallery::make(
    heading: 'Gallery',
    images: array_map(fn ($id) => ['image' => ['ID' => $id]], $latest_4),
);

// Without heading (nav still shows if multiple images)
echo Gallery::make(
    images: array_map(fn ($id) => ['image' => ['ID' => $id]], $latest_5),
);
