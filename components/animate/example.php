<?php

/**
 * Animate Component Examples
 *
 * The animate component is a CSS/JS utility — no PHP class needed.
 * Mark the wrapper with `.animate` + `data-animate`, and its items with `.animate__item`.
 * scripts.js sets `[data-playing]` on the wrapper when it scrolls into view.
 *
 * Data attributes on the wrapper:
 *   data-animate-stagger="ms"   Delay each item by its index × ms
 *
 * CSS variables (set inline or via class on .animate):
 *   --animate--animation-name              Animation name (default: fade-in)
 *   --animate--animation-duration          Duration (default: 300ms)
 *   --animate--animation-delay             Base delay (default: 50ms)
 *   --animate--animation-timing-function   Easing (default: ease-out)
 *   --animate--translate                   Start offset for fade-in-translate (default: 0 16px)
 *
 * Tailwind utilities (via @theme):
 *   animate-fade-in           — standalone fade-in animation
 *   animate-fade-in-translate — standalone fade + slide animation
 */
?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Default: Fade In</h2>
    <p class="component-example-section__description">
        Wrap content in <code>.animate</code> with <code>data-animate</code>, and mark children with
        <code>.animate__item</code>. Each item fades in when the wrapper scrolls into view.
    </p>
    <div class="component-example-section__preview">
        <div class="animate" data-animate>
            <p class="animate__item p-16 bg-slate-100 rounded">I fade in on scroll.</p>
        </div>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Fade In + Translate (slide up)</h2>
    <p class="component-example-section__description">
        Set <code>--animate--animation-name: fade-in-translate</code>. Items slide up 16px by default;
        change the start offset with <code>--animate--translate</code>.
    </p>
    <div class="component-example-section__preview">
        <div class="animate" data-animate style="--animate--animation-name: fade-in-translate; --animate--translate: 0 24px;">
            <p class="animate__item p-16 bg-slate-100 rounded">I slide up and fade in on scroll.</p>
        </div>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Staggered Items</h2>
    <p class="component-example-section__description">
        Add <code>data-animate-stagger="80"</code> to the wrapper to delay each item by its index × 80ms.
    </p>
    <div class="component-example-section__preview">
        <div
            class="animate flex gap-16"
            data-animate
            data-animate-stagger="80"
            style="--animate--animation-name: fade-in-translate;"
        >
            <?php foreach (['First', 'Second', 'Third', 'Fourth'] as $label) { ?>
                <div class="animate__item p-16 bg-slate-100 rounded flex-1 text-center">
                    <?= $label ?>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Custom Duration &amp; Easing</h2>
    <p class="component-example-section__description">
        Override timing via CSS variables on the <code>.animate</code> wrapper.
    </p>
    <div class="component-example-section__preview">
        <div class="animate" data-animate style="--animate--animation-duration: 800ms; --animate--animation-timing-function: var(--ease-out-expo);">
            <p class="animate__item p-16 bg-slate-100 rounded">Slow, expo-eased fade in.</p>
        </div>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Standalone Tailwind Utility</h2>
    <p class="component-example-section__description">
        Use <code>animate-fade-in</code> or <code>animate-fade-in-translate</code> as a Tailwind utility
        without the scroll-trigger wrapper — useful for page-load animations.
    </p>
    <div class="component-example-section__preview" style="display: flex; gap: 1rem;">
        <div class="animate-fade-in p-16 bg-slate-100 rounded">animate-fade-in</div>
        <div class="animate-fade-in-translate p-16 bg-slate-100 rounded">animate-fade-in-translate</div>
    </div>
</section>
