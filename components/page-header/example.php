<?php

/**
 * PageHeader Component Examples
 */

use Gust\Components\PageHeader;

$intro = '<p>Sister Midnight is an upcoming community-owned music venue and radio station based in Catford. We’re on a mission to elevate artists around Lewisham and become a creative hub for all across SE London.</p><p>Want to help reimagine what a music venue could be? <a href="#">Become a member</a></p>';

$image = get_posts(['post_type' => 'attachment', 'post_mime_type' => 'image/jpeg', 'numberposts' => 1, 'fields' => 'ids']);
?>

<section class="component-example-section">
    <h2 class="component-example-section__title">With illustration</h2>
    <p class="component-example-section__description">Vertical label, 420px illustration in the left half, intro in the right half.</p>
    <div class="component-example-section__preview">
        <?= PageHeader::make(label: 'About us', intro: $intro, media: 'stars'); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">With image</h2>
    <p class="component-example-section__description">410×607 image in the left half (first JPEG in the media library).</p>
    <div class="component-example-section__preview">
        <?= PageHeader::make(label: 'Venue', intro: $intro, media: $image[0] ?? null); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Label only</h2>
    <p class="component-example-section__description">No intro or media, as on a search or 404 page without fields.</p>
    <div class="component-example-section__preview">
        <?= PageHeader::make(label: '404'); ?>
    </div>
</section>
