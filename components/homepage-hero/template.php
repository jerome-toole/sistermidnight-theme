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
        <?= \Gust\SVG::get('homepage-header.svg'); ?>

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
        <?= \Gust\Components\Link::make(...$this->cta); ?>
    <?php } ?>
</section>
