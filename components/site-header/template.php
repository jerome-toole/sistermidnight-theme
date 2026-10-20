<header class="<?= classes('site-header color-context-deep-blue', $this->classes) ?>" data-site-header <?= attributes($this->attributes) ?>>
    <div class="site-header__bar">
        <a href="<?= esc_url(home_url('/')) ?>" class="site-header__logo">
            <?= esc_html(get_bloginfo('name')) ?>
        </a>

        <div class="site-header__buttons">
            <button
                class="site-header__toggle site-header__burger btn btn--ghost"
                popovertarget="site-header-nav"
                type="button">
                <?= \Gust\Components\Burger::make(attributes: ['aria-hidden' => 'true']); ?>
                <span class="sr-only"><?= __('Menu', 'gust') ?></span>
                <span class="site-header__toggle-closed" aria-hidden="true"><?= __('Menu', 'gust') ?></span>
                <span class="site-header__toggle-open" aria-hidden="true"><?= __('Close', 'gust') ?></span>
            </button>
        </div>

        <?php /* Inline from the site-header breakpoint up, a popover panel below it */ ?>
        <nav class="site-header__nav site-header__panel" id="site-header-nav" aria-label="<?= esc_attr__('Primary navigation', 'gust') ?>" popover data-scroll-lock>
            <?= \Gust\Components\Menu::make(
                theme_location: 'header',
                menu_id: 'main-menu',
                classes: ['site-header__navigation'],
            ); ?>

            <?= \Gust\Components\SocialIcons::make(
                classes: ['site-header__social'],
            ); ?>
        </nav>
    </div>
</header>
