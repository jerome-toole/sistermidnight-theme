<?php
/**
 * Globals style guide template.
 *
 * @var array $colors Theme colors from WordPress palette
 */
?>
<section class="style-guide__section">
    <h2>Design Tokens</h2>

    <h3>Typography</h3>

    <div style="margin-bottom: 3rem;">
        <h4>Font Stacks</h4>
        <div style="display: grid; gap: 1.5rem; margin-bottom: 2rem;">
            <div>
                <p style="font-family: var(--font-sistermidnight); font-size: 2rem; margin: 0;">SisterMidnight Display</p>
                <code style="font-size: 0.75rem;">--font-sistermidnight</code>
            </div>
            <div>
                <p style="font-family: var(--font-rework); font-size: 1.25rem; margin: 0;">Rework Text — Regular, <em>Italic</em>, <strong>Bold</strong>, <strong><em>Bold Italic</em></strong></p>
                <code style="font-size: 0.75rem;">--font-rework</code>
            </div>
        </div>

        <h4>Heading Scale</h4>
        <div class="prose" style="margin-bottom: 2rem;">
            <h1>Heading 1 — SisterMidnight</h1>
            <h2>Heading 2 — SisterMidnight</h2>
            <h3>Heading 3 — Rework</h3>
            <h4>Heading 4 — Rework</h4>
            <h5>Heading 5 — Rework</h5>
            <h6>Heading 6 — Rework</h6>
        </div>

        <h4>Body Text</h4>
        <div class="prose" style="max-width: 65ch; margin-bottom: 2rem;">
            <p>This is body text using the <strong>Rework</strong> font at the base size. It includes <strong>bold text</strong>, <em>italic text</em>, and <a href="#">inline links</a> that follow the theme's link styling.</p>
            <p>A second paragraph to show spacing. The quick brown fox jumps over the lazy dog. 1234567890</p>
        </div>

        <h4>Lists</h4>
        <div class="prose" style="max-width: 65ch; margin-bottom: 2rem;">
            <ul>
                <li>Unordered list item one</li>
                <li>Unordered list item two with <a href="#">a link</a></li>
                <li>Nested list:
                    <ul>
                        <li>Nested item one</li>
                        <li>Nested item two</li>
                    </ul>
                </li>
            </ul>
            <ol>
                <li>Ordered list item one</li>
                <li>Ordered list item two</li>
                <li>Ordered list item three</li>
            </ol>
        </div>

        <h4>Type Utilities</h4>
        <div style="display: grid; gap: 0.5rem; margin-bottom: 2rem;">
            <p class="type-hero" style="margin: 0;">type-hero</p>
            <p class="type-h1" style="margin: 0;">type-h1</p>
            <p class="type-h2" style="margin: 0;">type-h2</p>
            <p class="type-h3" style="margin: 0;">type-h3</p>
            <p class="type-h4" style="margin: 0;">type-h4</p>
            <p class="type-h5" style="margin: 0;">type-h5</p>
            <p class="type-h6" style="margin: 0;">type-h6</p>
            <p class="type-base" style="margin: 0;">type-base</p>
            <p class="type-small" style="margin: 0;">type-small</p>
            <p class="type-meta" style="margin: 0;">type-meta</p>
        </div>
    </div>

    <?php if (! empty($colors)) { ?>
        <h3>Colors</h3>
        <div class="dev-color-swatches" style="display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
            <?php foreach ($colors as $color) { ?>
                <div style="text-align: center;">
                    <div style="width: 80px; height: 80px; background: <?= esc_attr($color['color']); ?>; border: 1px solid #ccc; border-radius: 4px;"></div>
                    <small><?= esc_html($color['name'] ?? $color['slug']); ?></small><br>
                    <code style="font-size: 0.75rem;"><?= esc_html($color['color']); ?></code>
                </div>
            <?php } ?>
        </div>

        <h3>Color Contexts</h3>
        <p style="margin-bottom: 1rem;">Colors can be defined with a matching foreground colour and additional custom properties. A color context can be set with the <code>.color-context-{color}</code> utility.</p>
        <div style="display: grid; gap: 1rem; margin-bottom: 2rem;">
            <?php foreach ($colors as $color) { ?>
                <div class="color-context-<?= esc_attr($color['slug']); ?>" style="padding: 1.5rem; border-radius: 0.5rem;">
                    <strong><?= esc_html($color['name'] ?? $color['slug']); ?></strong>
                    <code style="opacity: 0.7;">.color-context-<?= esc_attr($color['slug']); ?></code>
                    <p style="margin: 0.5rem 0 0;">Sample text with <a href="#">a link</a> to demonstrate foreground and link colors.</p>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <p>No theme colors defined in theme.json</p>
    <?php } ?>
</section>
