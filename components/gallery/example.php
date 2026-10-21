<?php

/**
 * Gallery Component Examples
 *
 * Latest eight images from the media library, in both block styles.
 */

use Gust\Components\Gallery;

$images = get_posts([
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'posts_per_page' => 8,
    'orderby' => 'date',
    'order' => 'DESC',
    'fields' => 'ids',
]);
?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Masonry (default)</h2>
    <p class="component-example-section__description">Justified rows at a fixed height; widths follow each image's aspect ratio and every row is filled.</p>
    <div class="component-example-section__preview">
        <?= Gallery::make(label: 'Gallery', images: $images); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Carousel</h2>
    <p class="component-example-section__description">Native horizontal scroller with prev/next arrows; Blossom is loaded only on this style.</p>
    <div class="component-example-section__preview">
        <?= Gallery::make(label: 'Slider', images: $images, style: 'carousel'); ?>
    </div>
</section>
