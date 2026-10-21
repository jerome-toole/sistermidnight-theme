<?php

/**
 * Icon Component Examples
 */

use Gust\Components\Icon;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Icon - Arrow</h2>
    <p class="component-example-section__description">The Figma ↗ arrow, 1em wide here; it follows the text colour. Used by external links, buttons and the radio bar.</p>
    <div class="component-example-section__preview">
        <p style="font-size: 2rem;">Text <?= Icon::make(name: 'arrow', attributes: ['style' => 'width: 1em; height: 0.94em;']); ?></p>
    </div>
</section>
