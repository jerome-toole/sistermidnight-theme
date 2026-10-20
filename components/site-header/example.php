<?php

/**
 * SiteHeader Component Examples
 *
 * Note: the nav needs a menu assigned to the 'header' location. Resize below
 * md to see the "Menu" button and dialog.
 */

use Gust\Components\SiteHeader;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Default</h2>
    <p class="component-example-section__description">Dark-umber context, in the page flow. Used on every page but the front page.</p>
    <div class="component-example-section__preview">
        <?= SiteHeader::make(variant: 'default'); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Hero</h2>
    <p class="component-example-section__description">No background; positioned over the Hero and takes its colour context.</p>
    <div class="component-example-section__preview">
        <div class="relative color-context-burnt-sienna" style="min-height: 320px;">
            <?= SiteHeader::make(variant: 'hero'); ?>
        </div>
    </div>
</section>
