<?php

/**
 * SiteFooter Component Examples
 *
 * Note: reads the Footer options page (address, map link, supporters, legal
 * text), the General options social networks and the "Footer" menu location.
 */

use Gust\Components\SiteFooter;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Site Footer</h2>
    <p class="component-example-section__description">Dancers strip, footer nav and social icons, address with map link, supporter logos (greyscale, 40px) and legal text. Resize below md to see the cropped strip and stacked columns.</p>
    <div class="component-example-section__preview">
        <?= SiteFooter::make(); ?>
    </div>
</section>
