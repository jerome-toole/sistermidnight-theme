<?php

/**
 * FAQ Component Examples
 */

use Gust\Components\Faq;

$items = [
    [
        'question' => 'Will there be food at the event?',
        'answer' => '<p>Yes, you can amet, consectetur adipiscing elit. Sed id nisi vitae mi gravida porta at eget ligula. Maecenas orci tellus, posuere sed metus dapibus, laoreet tincidunt est.</p><p>£6 – meal<br>£10 – meal and a drink</p>',
    ],
    [
        'question' => 'How do I find out about your access provisions?',
        'answer' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed id nisi vitae mi gravida porta at eget ligula.</p><p>Contact: <a href="mailto:access@sistermidnight.org">access@sistermidnight.org</a></p>',
    ],
];
?>

<section class="component-example-section">
    <h2 class="component-example-section__title">FAQ</h2>
    <p class="component-example-section__description">Heading and two questions, closed by default; several can be open at once.</p>
    <div class="component-example-section__preview" style="max-width: 594px">
        <?= Faq::make(heading: 'FAQ', items: $items); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">FAQ, open</h2>
    <p class="component-example-section__description">Every item open, as in the design and the editor preview.</p>
    <div class="component-example-section__preview" style="max-width: 594px">
        <?= Faq::make(heading: 'FAQ', items: $items, open: true); ?>
    </div>
</section>
