<?php

/**
 * Image Pair Component Examples
 *
 * Latest two images from the media library on both backgrounds.
 */

use Gust\Components\ImagePair;

$images = get_posts([
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'posts_per_page' => 2,
    'orderby' => 'date',
    'order' => 'DESC',
    'fields' => 'ids',
]);
?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Dark Umber (default)</h2>
    <p class="component-example-section__description">Two portrait images side by side; stacked below md.</p>
    <div class="component-example-section__preview">
        <?= ImagePair::make(images: $images); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Stone Beige</h2>
    <div class="component-example-section__preview">
        <?= ImagePair::make(images: $images, background: 'stone-beige'); ?>
    </div>
</section>
