<?php

use Geum\Components\Image;

?>

<footer class="<?= classes('site-footer', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="site-footer__inner">
        <div class="site-footer__main">
            <div class="site-footer__left">
                <?= \Geum\Components\Menu::make(
                    theme_location: 'footer-1',
                    max_depth: 1,
                    classes: ['site-footer__menu'],
                ); ?>

                <?= \Geum\Components\SocialIcons::make(
                    title: __('Visit our %s page', 'geum'),
                ); ?>
            </div>

            <div class="site-footer__right">
                <?php if ($this->footer_text_top) { ?>
                    <div class="site-footer__address">
                        <?= wp_kses_post($this->footer_text_top); ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="site-footer__bottom">
            <?php if (! empty($this->content['images'])) { ?>
                <div class="site-footer__supporters">
                    <span class="site-footer__supporters-label"><?= esc_html__('supported by', 'geum'); ?></span>
                    <div class="site-footer__images">
                        <?php foreach ($this->content['images'] as $image) { ?>
                            <?php if (! empty($image['link_args'])) { ?>
                                <?= \Geum\Components\Link::make(...$image['link_args']); ?>
                            <?php } else { ?>
                                <div class="site-footer__image">
                                    <?= Image::make(...$image['image']); ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

            <div class="site-footer__meta">
                <?php if ($this->footer_legal_text) { ?>
                    <div class="site-footer__legal">
                        <?= wp_kses_post($this->footer_legal_text); ?>
                    </div>
                <?php } ?>

                <?php if ($this->footer_text_bottom) { ?>
                    <div class="site-footer__copyright">
                        <?= wp_kses_post($this->footer_text_bottom); ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="site-footer__illustration" aria-hidden="true">
            <?= \Geum\Image::get('dancing-footer.svg'); ?>
        </div>
    </div>
</footer>
