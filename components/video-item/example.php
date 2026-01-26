<?php

/**
 * VideoItem Component Examples
 *
 * Displays video with poster image that lazy-loads on click.
 */

use Geum\Components\VideoItem;

$placeholder_image = [
    'url' => 'https://placehold.co/800x450/1a1a2e/ffffff?text=Video+Poster',
    'alt' => 'Video poster placeholder',
];

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Video with Poster Image</h2>
    <p class="component-example-section__description">Poster displays until play clicked, then YouTube loads.</p>
    <div class="component-example-section__preview">
        <?= VideoItem::make(
            video: '<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>',
            image: $placeholder_image,
        ); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Vimeo Embed</h2>
    <p class="component-example-section__description">Works with Vimeo embeds too.</p>
    <div class="component-example-section__preview">
        <?= VideoItem::make(
            video: '<iframe src="https://player.vimeo.com/video/76979871" width="640" height="360" frameborder="0" allowfullscreen></iframe>',
            image: $placeholder_image,
        ); ?>
    </div>
</section>
