<?php

namespace Theme\Modules\Core;

class Menus
{
    public static function init()
    {
        \add_filter('after_setup_theme', [__CLASS__, 'registerThemeMenus']);
    }

    public static function registerThemeMenus(): void
    {
        \register_nav_menus([
            'header' => _x('Primary', 'Menu name', 'gust'),
            'footer-1' => _x('Footer', 'Menu name', 'gust'),
        ]);
    }
}
