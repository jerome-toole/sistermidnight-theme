<?php

/**
 * Upcoming Events Component Examples
 */

use Gust\Components\UpcomingEvents;

$announcement = [
    'heading' => 'Venue launch: 27 June 2027',
    'text' => '<p>We’re amet, consectetur adipiscing elit. Sed id nisi vitae mi gravida porta at eget ligula. Maecenas orci tellus, posuere sed metus dapibus, laoreet tincidunt est.</p>',
    'illustration' => 'record-face',
];
?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Grid with announcement (front page)</h2>
    <p class="component-example-section__description">Two staggered columns, the announcement card first in the right column, a large illustration bottom-left.</p>
    <div class="component-example-section__preview">
        <?= UpcomingEvents::make(announcement: $announcement, illustration: 'flower-roots'); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Grid without announcement</h2>
    <p class="component-example-section__description">The right column starts 300px down.</p>
    <div class="component-example-section__preview">
        <?= UpcomingEvents::make(label: 'Next up', count: 3); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Featured (What's On)</h2>
    <p class="component-example-section__description">Three small cards in a row, the middle one offset. No label.</p>
    <div class="component-example-section__preview">
        <?= UpcomingEvents::make(label: '', variant: 'featured'); ?>
    </div>
</section>
