<?php

/**
 * Hero Component Examples
 */

use Gust\Components\Hero;

$attachments = get_posts([
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'posts_per_page' => 1,
    'post_status' => 'inherit',
]);
$sample_poster = ! empty($attachments) ? $attachments[0]->ID : null;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Hero - Poster only</h2>
    <p class="component-example-section__description">Poster image with an illustration, no video: no toggle is rendered.</p>
    <div class="component-example-section__preview">
        <?= Hero::make(poster: $sample_poster, illustration: 'dancers'); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Hero - From Header options</h2>
    <p class="component-example-section__description">What the front page renders: hero_poster, hero_video and hero_illustration from the Header options page. Nothing renders until a poster is set.</p>
    <div class="component-example-section__preview">
        <?= Hero::fromOptions(); ?>
    </div>
</section>
