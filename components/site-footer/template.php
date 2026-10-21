<?php

use Gust\Components\Image;
use Gust\Components\Menu;
use Gust\Components\SiteFooter;
use Gust\Components\SocialIcons;

?>
<footer class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="site-footer__strip" aria-hidden="true">
        <?= \Gust\SVG::get('dancing-footer.svg'); ?>
    </div>

    <div class="site-footer__inner">
        <div class="site-footer__top">
            <div class="site-footer__menus">
                <?php if ($this->has_menu) { ?>
                    <?php add_filter('wp_get_nav_menu_items', [SiteFooter::class, 'localNewsletterLink']); ?>
                    <?= Menu::make(
                        theme_location: SiteFooter::MENU_LOCATION,
                        max_depth: 1,
                        classes: ['site-footer__nav'],
                        attributes: ['aria-label' => __('Footer', 'gust')],
                    ); ?>
                    <?php remove_filter('wp_get_nav_menu_items', [SiteFooter::class, 'localNewsletterLink']); ?>
                <?php } ?>

                <?= SocialIcons::make(
                    title: __('Visit our %s page', 'gust'),
                    classes: ['site-footer__social'],
                ); ?>
            </div>

            <?php if ($this->address || $this->map_link) { ?>
                <div class="site-footer__location">
                    <?php if ($this->address_lines) { ?>
                        <address class="site-footer__address"><?= implode('<br>', array_map('esc_html', $this->address_lines)); ?></address>
                    <?php } ?>

                    <?php if ($this->map_link) { ?>
                        <?= \Gust\Components\Link::make(
                            content: esc_html__('Open map', 'gust'),
                            url: $this->map_link,
                            target: '_blank',
                            classes: ['site-footer__map-link'],
                        ); ?>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <?php if (! empty($this->supporters) || $this->legal_text) { ?>
            <div class="site-footer__bottom">
                <?php if (! empty($this->supporters)) { ?>
                    <div class="site-footer__supporters">
                        <p class="site-footer__supporters-label"><?= esc_html__('supported by', 'gust'); ?></p>
                        <svg class="site-footer__logo-filter" aria-hidden="true" focusable="false" width="0" height="0">
                            <filter id="site-footer-logo-tint" color-interpolation-filters="sRGB">
                                <feFlood class="site-footer__logo-flood" />
                                <feComposite in2="SourceAlpha" operator="in" />
                            </filter>
                        </svg>
                        <ul class="site-footer__logos">
                            <?php foreach ($this->supporters as $supporter) { ?>
                                <li class="site-footer__logo">
                                    <?php $logo = Image::make(id: $supporter['logo'], size: 'medium', attributes: ['alt' => $supporter['name']]); ?>
                                    <?php if ($supporter['url']) { ?>
                                        <a href="<?= esc_url($supporter['url']) ?>"><?= $logo; ?></a>
                                    <?php } else { ?>
                                        <?= $logo; ?>
                                    <?php } ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>

                <?php if ($this->legal_text) { ?>
                    <div class="site-footer__legal"><?= wp_kses_post($this->legal_text); ?></div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</footer>
