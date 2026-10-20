<?php
/**
 * Globals style guide template.
 *
 * @var array $colors Theme colors from WordPress palette
 */
?>
<section class="dev-kit__section alignfull flow">
    <h2 data-dev-ui>Design Tokens</h2>

    <?php if (! empty($colors)) : ?>

        <!-- Colors -->
        <div class="dev-kit__subsection">
            <h3 data-dev-ui>Colors</h3>
            <small><code class="dev-kit__code">--color-{name}</code></small>
            <div class="dev-kit__demo">
                <div class="dev-kit__swatches">
                    <?php foreach ($colors as $color) : ?>
                        <div class="dev-kit__swatch">
                            <div class="dev-kit__swatch-chip" style="background: <?= esc_attr($color['color']); ?>;"></div>
                            <small><?= esc_html($color['name'] ?? $color['slug']); ?></small>
                            <code class="dev-kit__code"><?= esc_html($color['color']); ?></code>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Color Contexts -->
        <div class="dev-kit__subsection">
            <h3 data-dev-ui>Color Contexts</h3>
            <small>
                <code class="dev-kit__code">.color-context-{color}</code> — sets background, foreground, link, and focus colors together.
            </small>
            <div class="dev-kit__demo" style="padding: 0; overflow: hidden;">
                <?php foreach ($colors as $color) : ?>
                    <div class="color-context-<?= esc_attr($color['slug']); ?>" style="padding: 1.5rem;">
                        <strong><?= esc_html($color['name'] ?? $color['slug']); ?></strong>
                        <code class="dev-kit__code">.color-context-<?= esc_attr($color['slug']); ?></code>
                        <p style="margin: 0.5rem 0;">Sample text with <a href="#">a link</a> to demonstrate foreground and link colors.</p>
                        <button class="btn btn--small">.btn</button>
                        <button class="btn btn--small btn--theme-2">.btn--theme-2</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    <?php else : ?>
        <p data-dev-ui>No theme colors defined in theme.json</p>
    <?php endif; ?>
</section>
