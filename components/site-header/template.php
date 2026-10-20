<?php
$nav_label = __('Primary', 'gust');
?>
<header class="<?= classes($this->classes) ?>" data-site-header <?= attributes($this->attributes) ?>>
    <div class="site-header__bar">
        <a href="<?= esc_url(home_url('/')) ?>" class="site-header__wordmark"><?= esc_html(get_bloginfo('name')) ?></a>

        <?php if ($this->has_menu) { ?>
            <?= \Gust\Components\Menu::make(
                theme_location: \Gust\Components\SiteHeader::MENU_LOCATION,
                classes: ['site-header__nav'],
                attributes: ['aria-label' => $nav_label],
            ); ?>

            <?= \Gust\Components\Button::make(
                content: __('Menu', 'gust'),
                classes: ['site-header__toggle'],
                attributes: [
                    'data-site-header-open' => '',
                    'aria-haspopup' => 'dialog',
                    'command' => 'show-modal',
                    'commandfor' => $this->dialog_id,
                ],
            ); ?>
        <?php } ?>
    </div>

    <?php if ($this->has_menu) { ?>
        <dialog class="site-header__dialog color-context-dark-umber" id="<?= esc_attr($this->dialog_id) ?>" aria-label="<?= esc_attr__('Menu', 'gust') ?>">
            <div class="site-header__dialog-bar">
                <a href="<?= esc_url(home_url('/')) ?>" class="site-header__wordmark"><?= esc_html(get_bloginfo('name')) ?></a>

                <?= \Gust\Components\Button::make(
                    content: __('Close', 'gust'),
                    classes: ['site-header__toggle'],
                    attributes: [
                        'data-site-header-close' => '',
                        'autofocus' => '',
                        'command' => 'close',
                        'commandfor' => $this->dialog_id,
                    ],
                ); ?>
            </div>

            <?= \Gust\Components\Menu::make(
                theme_location: \Gust\Components\SiteHeader::MENU_LOCATION,
                classes: ['site-header__menu'],
                attributes: ['aria-label' => $nav_label],
            ); ?>
        </dialog>
    <?php } ?>
</header>
