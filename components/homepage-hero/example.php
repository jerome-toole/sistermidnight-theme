<?php

use Gust\Components\HomepageHero;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Homepage Hero</h2>
    <p class="component-example-section__description">Full-width hero with SVG illustration and floating CTA.</p>
    <div class="component-example-section__preview">
        <?= HomepageHero::make([
            'heading' => 'A New Music Venue for Lewisham',
            'cta' => [
                'title' => 'Get Involved',
                'url' => '#',
            ],
        ]); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Homepage Hero (No CTA)</h2>
    <p class="component-example-section__description">Hero without floating button.</p>
    <div class="component-example-section__preview">
        <?= HomepageHero::make([
            'heading' => 'Sister Midnight',
        ]); ?>
    </div>
</section>
