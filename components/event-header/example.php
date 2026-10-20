<?php

/**
 * Event Header Component Examples
 */

use Gust\Components\EventHeader;
use Theme\Modules\Events\Event;

$now = Event::now()->format('Y-m-d H:i:s');

$upcoming = get_posts([
    'post_type' => 'event',
    'posts_per_page' => 1,
    'meta_key' => 'start',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'meta_query' => [['key' => 'start', 'value' => $now, 'compare' => '>=', 'type' => 'DATETIME']],
]);

$past = get_posts([
    'post_type' => 'event',
    'posts_per_page' => 1,
    'meta_key' => 'start',
    'orderby' => 'meta_value',
    'order' => 'DESC',
    'meta_query' => [['key' => 'start', 'value' => $now, 'compare' => '<', 'type' => 'DATETIME']],
]);

if (! $upcoming && ! $past) {
    echo '<p>No events found. Seed events first.</p>';

    return;
}
?>

<?php if ($upcoming) { ?>
    <section class="component-example-section">
        <h2 class="component-example-section__title">Upcoming event</h2>
        <p class="component-example-section__description">Poster, presenter, title, meta row, excerpt and the ticket CTA from ticket_type.</p>
        <div class="component-example-section__preview">
            <?= EventHeader::make(event: $upcoming[0]); ?>
        </div>
    </section>
<?php } ?>

<?php if ($past) { ?>
    <section class="component-example-section">
        <h2 class="component-example-section__title">Past event</h2>
        <p class="component-example-section__description">"This event has passed" note in place of the button.</p>
        <div class="component-example-section__preview">
            <?= EventHeader::make(event: $past[0]); ?>
        </div>
    </section>
<?php } ?>
