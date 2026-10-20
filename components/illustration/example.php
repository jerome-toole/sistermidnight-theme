<?php

/**
 * Illustration Component Examples
 */

use Gust\Components\Illustration;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">All illustrations</h2>
    <p class="component-example-section__description">Every name in <code>Illustration::NAMES</code>, rendered inline with <code>currentColor</code> fill. Small size.</p>
    <div class="component-example-section__preview flex flex-wrap gap-6">
        <?php foreach (Illustration::NAMES as $name) { ?>
            <figure class="flex flex-col items-center gap-2">
                <?= Illustration::make(name: $name, size: 'sm'); ?>
                <figcaption><code><?= esc_html($name); ?></code></figcaption>
            </figure>
        <?php } ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Sizes</h2>
    <p class="component-example-section__description">`sm`, `md`, `lg` (design uses 562px at 1445 wide). No size fills the container width.</p>
    <div class="component-example-section__preview flex flex-wrap items-end gap-6">
        <?= Illustration::make(name: 'cat', size: 'sm'); ?>
        <?= Illustration::make(name: 'cat', size: 'md'); ?>
        <?= Illustration::make(name: 'cat', size: 'lg'); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Colour follows context</h2>
    <p class="component-example-section__description">Fill is <code>currentColor</code>, so it picks up the colour context's foreground.</p>
    <div class="component-example-section__preview flex flex-wrap gap-6">
        <div class="color-context-dark-umber p-6"><?= Illustration::make(name: 'moon', size: 'sm'); ?></div>
        <div class="color-context-warm-sand p-6"><?= Illustration::make(name: 'sun-petals', size: 'sm'); ?></div>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Unknown name</h2>
    <p class="component-example-section__description">Renders nothing: <code>Illustration::make(name: 'nope')</code> → "<?= Illustration::make(name: 'nope'); ?>"</p>
</section>
