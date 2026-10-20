<?php

/**
 * Card Component Examples
 */

use Gust\Components\Card;

$member = <<<'HTML'
<p>Since 2021, over 1100 members have raised £365,000 to make this happen — and now we need your help to complete the next stage of renovations.</p>
<p>We’ve started renovating the Brookdale Club in Catford, and now we need your help to finish! Invest in community shares from £25, every pound is double match funded, and become part of Catford’s first community-owned grassroots music venue.</p>
HTML;
?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Member card (lg)</h2>
    <p class="component-example-section__description">Heading, two paragraphs and a "Read more" link.</p>
    <div class="component-example-section__preview" style="max-width: 677px">
        <?= Card::make(heading: 'Become a Member', text: $member, link: ['url' => '/membership/', 'title' => 'Read more']); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Newsletter card (sm)</h2>
    <p class="component-example-section__description">Illustration top-right and the Newsletter Form under the text.</p>
    <div class="component-example-section__preview" style="max-width: 614px">
        <?= Card::make(heading: 'Stay in touch', text: '<p>We send an occasional newsletter letting you know about our events.</p>', illustration: 'disco-ball', show_newsletter_form: true, size: 'sm'); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Announcement card (sm)</h2>
    <p class="component-example-section__description">Text-only card with an illustration, as used first in the Upcoming grid.</p>
    <div class="component-example-section__preview" style="max-width: 680px">
        <?= Card::make(heading: 'Venue launch: 27 June 2027', text: '<p>We’re amet, consectetur adipiscing elit. Sed id nisi vitae mi gravida porta at eget ligula. Maecenas orci tellus, posuere sed metus dapibus, laoreet tincidunt est.</p>', illustration: 'record-face', size: 'sm'); ?>
    </div>
</section>
