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
        <?= RadioBar::make(online: true, title: 'Moonbeam Levels w/ Amar Patel', enabled: true, refresh: false); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Radio Bar - Artist and long title</h2>
    <p class="component-example-section__description">Artist and title joined with an en dash; overflow is clipped with an ellipsis.</p>
    <div class="component-example-section__preview">
        <?= RadioBar::make(online: true, artist: 'Social Records Society', title: 'A very long show title that keeps going to show the ellipsis in action on narrow screens', enabled: true, refresh: false); ?>
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
    <h2 class="component-example-section__title">Radio Bar - Offline</h2>
    <p class="component-example-section__description">The track line is hidden; brand and Listen remain.</p>
    <div class="component-example-section__preview">
        <?= RadioBar::make(online: false, enabled: true, refresh: false); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Radio Bar - Live</h2>
    <p class="component-example-section__description">What the front page renders: RadioService data, refreshed every 60s. Nothing renders when radio_enabled is off.</p>
    <div class="component-example-section__preview">
        <?= RadioBar::fromService(); ?>
    </div>
</section>
