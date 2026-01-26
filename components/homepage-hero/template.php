<section class="<?= classes('homepage-hero', 'section', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="homepage-hero__mobile-text">
        <?php if (! empty($this->line_1)) { ?>
            <h1 class="homepage-hero__line-1"><?= nl2br(esc_html($this->line_1)) ?></h1>
        <?php } ?>
        <?php if (! empty($this->line_2)) { ?>
            <p class="homepage-hero__line-2"><?= nl2br(esc_html($this->line_2)) ?></p>
        <?php } ?>
        <?php if (! empty($this->line_3)) { ?>
            <p class="homepage-hero__line-3"><?= nl2br(esc_html($this->line_3)) ?></p>
        <?php } ?>
    </div>

    <div class="homepage-hero__graphic">
        <?= \Geum\SVG::get(\Geum\Asset::path('build/images/homepage-header.svg'), ['asset' => false]); ?>

        <div class="homepage-hero__text">
            <?php if (! empty($this->line_1)) { ?>
                <h1 class="homepage-hero__line-1"><?= nl2br(esc_html($this->line_1)) ?></h1>
            <?php } ?>
            <?php if (! empty($this->line_2)) { ?>
                <p class="homepage-hero__line-2"><?= nl2br(esc_html($this->line_2)) ?></p>
            <?php } ?>
            <?php if (! empty($this->line_3)) { ?>
                <p class="homepage-hero__line-3"><?= nl2br(esc_html($this->line_3)) ?></p>
            <?php } ?>
        </div>
    </div>

    <?php if (! empty($this->cta)) { ?>
        <?= \Geum\Components\Link::make(...$this->cta); ?>
    <?php } ?>
</section>
