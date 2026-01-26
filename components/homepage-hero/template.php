<section class="<?= classes('homepage-hero', 'wp-block', 'color-context-paper', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="homepage-hero__illustration">
        <?php
        $svg_path = get_template_directory().'/public/build/images/hero-illustration.svg';
if (file_exists($svg_path)) {
    echo file_get_contents($svg_path);
}
?>
    </div>

    <div class="homepage-hero__content content-width-md">
        <?php if (! empty($this->heading)) { ?>
            <?= \Geum\Components\Heading::make(...$this->heading); ?>
        <?php } ?>

        <?php if (! empty($this->cta)) { ?>
            <?= \Geum\Components\Link::make(...$this->cta); ?>
        <?php } ?>
    </div>
</section>
