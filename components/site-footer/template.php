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

            <div class="site-footer__brand">
                <a href="<?= esc_url(home_url('/')) ?>" class="site-footer__logo">
                    <?= \Geum\Image::get('logo-alt.svg', [
                        'alt' => esc_attr($this->site_name),
                    ]); ?>
                </a>

                <?php if (! empty($this->radio_url)) { ?>
                    <a
                        href="<?= esc_url($this->radio_url) ?>"
                        class="site-footer__radio-link"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <?= esc_html__('SMFM Radio', 'geum') ?>
                    </a>
                <?php } ?>
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
            <?= \Geum\Image::get('footer-figures.svg'); ?>
        </div>
    </div>
</footer>
