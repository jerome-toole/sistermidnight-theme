<header class="<?= classes('site-header', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="site-header__bar">
        <a href="<?= esc_url(home_url('/')) ?>" class="site-header__logo">
            <?php if (! empty($this->logo['url'])) { ?>
                <img
                    src="<?= esc_url($this->logo['url']) ?>"
                    alt="<?= esc_attr($this->site_name) ?>"
                    width="<?= esc_attr($this->logo['width'] ?? '') ?>"
                    height="<?= esc_attr($this->logo['height'] ?? '') ?>"
                />
            <?php } else { ?>
                <?= esc_html($this->site_name) ?>
            <?php } ?>
        </a>

        <nav class="site-header__nav" aria-label="<?= esc_attr__('Primary navigation', 'geum') ?>">
            <?= \Geum\Components\Menu::make(
                theme_location: 'header',
                menu_id: 'main-menu-desktop',
                classes: ['site-header__menu'],
            ); ?>

            <?php if (! empty($this->radio_url)) { ?>
                <a
                    href="<?= esc_url($this->radio_url) ?>"
                    class="site-header__radio-link"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <?= esc_html__('SMFM Radio', 'geum') ?>
                </a>
            <?php } ?>
        </nav>

        <button
            class="site-header__burger js-site-header-toggle"
            aria-label="<?= esc_attr__('Open menu', 'geum') ?>"
            aria-controls="site-header-menu-panel"
            aria-expanded="false"
            type="button"
        >
            <?= \Geum\Components\Burger::make(); ?>
        </button>
    </div>

    <div class="site-header__panel" id="site-header-menu-panel" inert>
        <div class="site-header__panel-content">
            <?= \Geum\Components\Menu::make(
                theme_location: 'header',
                menu_id: 'main-menu-mobile',
                classes: ['site-header__menu', 'site-header__menu--mobile'],
            ); ?>

            <?php if (! empty($this->radio_url)) { ?>
                <a
                    href="<?= esc_url($this->radio_url) ?>"
                    class="site-header__radio-link"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <?= esc_html__('SMFM Radio', 'geum') ?>
                </a>
            <?php } ?>
        </div>
    </div>
</header>
