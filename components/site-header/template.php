<header class="<?= classes('site-header color-context-deep-blue', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="site-header__bar">
        <a href="<?= esc_url(home_url('/')) ?>" class="site-header__logo">
            Sister Midnight
        </a>

        <nav class="site-header__nav" aria-label="<?= esc_attr__('Primary navigation', 'geum') ?>">
            <?= \Geum\Components\Menu::make(
                theme_location: 'header',
                menu_id: 'main-menu-desktop',
                classes: ['site-header__menu'],
            ); ?>
        </nav>

        <?= \Geum\Components\SocialIcons::make(
            classes: ['site-header__social'],
        ); ?>

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

            <?= \Geum\Components\SocialIcons::make(
                classes: ['site-header__social', 'site-header__social--mobile'],
            ); ?>
        </div>
    </div>
</header>
