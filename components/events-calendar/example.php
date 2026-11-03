<?php

/**
 * Events Calendar Component Examples
 */

use Gust\Components\EventsCalendar;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Calendar (current month)</h2>
    <p class="component-example-section__description">Server HTML holds the header and the fallback list; the script draws a week-snapping scroller from the feed (hover an event for its card; tap on touch for the swipeable sheet). Below md the list is forced.</p>
    <div class="component-example-section__preview">
        <?= EventsCalendar::make(); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">List view, November 2026</h2>
    <p class="component-example-section__description">Events from the selected month onwards, grouped by month.</p>
    <div class="component-example-section__preview">
        <?= EventsCalendar::make(month: '2026-11', view: 'list', count: 5); ?>
    </div>
</section>
