<?php

/**
 * Event Card Component Examples
 */

use Gust\Components\EventCard;

$events = get_posts([
    'post_type' => 'event',
    'posts_per_page' => 3,
    'meta_key' => 'start',
    'orderby' => 'meta_value',
    'order' => 'ASC',
]);

if (! $events) {
    echo '<p>No events found. Seed events first.</p>';

    return;
}
?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Large (front page Upcoming grid)</h2>
    <p class="component-example-section__description">Poster capped at 410px, presenter, title, excerpt, meta row.</p>
    <div class="component-example-section__preview" style="max-width: 680px">
        <?= EventCard::make(event: $events[0]); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Small (What's On featured)</h2>
    <p class="component-example-section__description">Poster fills the card. Second card without the presenter eyebrow.</p>
    <div class="component-example-section__preview flex flex-wrap items-start gap-10">
        <?php foreach ($events as $i => $event) { ?>
            <div style="width: 416px; max-width: 100%"><?= EventCard::make(event: $event, size: 'sm', show_presenter: $i !== 1); ?></div>
        <?php } ?>
    </div>
</section>
