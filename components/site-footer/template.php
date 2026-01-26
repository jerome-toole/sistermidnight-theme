<footer class="<?= classes('site-footer', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="site-footer__inner">
        <div class="site-footer__main">
            <div class="site-footer__contact">
                <?php if ($this->footer_text_top) { ?>
                    <div class="site-footer__text">
                        <?= wp_kses_post($this->footer_text_top); ?>
                    </div>
                <?php } ?>

                <?= \Geum\Components\SocialIcons::make(
                    title: __('Visit our %s page', 'geum'),
                ); ?>
            </div>


            <div class="site-footer__links">
                <?= \Geum\Components\Menu::make(
                    theme_location: 'footer-1',
                    max_depth: 1,
                    classes: ['site-footer__menu'],
                ); ?>

                <?php if ($this->footer_text_bottom) { ?>
                    <div class="site-footer__copyright">
                        <?= wp_kses_post($this->footer_text_bottom); ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <?php if ($this->footer_legal_text) { ?>
            <div class="site-footer__legal">
                <?= wp_kses_post($this->footer_legal_text); ?>
            </div>
        <?php } ?>

        <div class="site-footer__illustration" aria-hidden="true">
            <?= \Geum\Image::get('dancing-footer.svg'); ?>
        </div>
    </div>
</footer>
