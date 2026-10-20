<?php

/**
 * Button Component Examples
 */

use Gust\Components\Button;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Primary</h2>
    <p class="component-example-section__description">Burnt-sienna fill, 200×58, label + arrow. Renders an &lt;a&gt; when given a URL, otherwise a &lt;button&gt;.</p>
    <div class="component-example-section__preview" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center;">
        <?= Button::make(content: 'Buy Tickets', url: '#', variant: 'primary'); ?>
        <?= Button::make(content: 'Sign up', type: 'submit', variant: 'primary'); ?>
        <?= Button::make(content: 'A much longer label that wraps', url: '#', variant: 'primary'); ?>
        <?= Button::make(content: 'No arrow', url: '#', variant: 'primary', arrow: false); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Primary on a light context</h2>
    <p class="component-example-section__description">The fill stays burnt-sienna; only the surrounding context changes.</p>
    <div class="component-example-section__preview color-context-stone-beige" style="padding: 2rem;">
        <?= Button::make(content: 'Become a Member', url: '#', variant: 'primary'); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Link</h2>
    <p class="component-example-section__description">Underlined text + arrow. Follows the link colour of its context.</p>
    <div class="component-example-section__preview" style="display: flex; flex-wrap: wrap; gap: 2rem; align-items: center;">
        <?= Button::make(content: 'Open map', url: 'https://maps.google.com', target: '_blank', variant: 'link'); ?>
        <?= Button::make(content: 'Show more', variant: 'link'); ?>
    </div>
    <div class="component-example-section__preview color-context-stone-beige" style="padding: 2rem; margin-top: 1rem;">
        <?= Button::make(content: 'Read the manifesto', url: '#', variant: 'link'); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Unstyled (toggles)</h2>
    <p class="component-example-section__description">No variant: plain control used by the accordion and sub-menu togglers. Also supports visually hidden labels.</p>
    <div class="component-example-section__preview" style="display: flex; gap: 1rem;">
        <?= Button::make(content: 'Plain button'); ?>
        <?= Button::make(content: 'Close menu', screen_reader_text: true, attributes: ['aria-label' => 'Close menu']); ?>
    </div>
</section>
