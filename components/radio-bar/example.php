<?php

/**
 * RadioBar Component Examples
 *
 * The previews pass `enabled: true` and `refresh: false` so they render
 * regardless of the Radio options and keep their sample text.
 */

use Gust\Components\RadioBar;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Radio Bar - Now playing</h2>
    <p class="component-example-section__description">Station online with a track. Resize below md for the two-row layout.</p>
    <div class="component-example-section__preview">
        <?= RadioBar::make(online: true, show: 'Moonbeam Levels w/ Amar Patel', enabled: true, refresh: false); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Radio Bar - Show with track</h2>
    <p class="component-example-section__description">Show, artist and title joined with en dashes; overflow is clipped with an ellipsis.</p>
    <div class="component-example-section__preview">
        <?= RadioBar::make(online: true, show: 'Social Records Society', artist: 'Amar Patel', title: 'A very long track title that keeps going to show the ellipsis in action on narrow screens', enabled: true, refresh: false); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Radio Bar - Online, no track</h2>
    <p class="component-example-section__description">Falls back to "Live now".</p>
    <div class="component-example-section__preview">
        <?= RadioBar::make(online: true, enabled: true, refresh: false); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Radio Bar - Off air</h2>
    <p class="component-example-section__description">The default playlist is playing: the line reads "Off air"; brand and Listen remain.</p>
    <div class="component-example-section__preview">
        <?= RadioBar::make(online: false, enabled: true, refresh: false); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Radio Bar - Live</h2>
    <p class="component-example-section__description">What the front page renders: Radiocult data via RadioService, refreshed every 60s. Nothing renders when radio_enabled is off.</p>
    <div class="component-example-section__preview">
        <?= RadioBar::fromService(); ?>
    </div>
</section>
