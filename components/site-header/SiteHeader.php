<?php

namespace Gust\Components;

use Gust\ComponentBase;

/**
 * SiteHeader Component
 *
 * Wordmark left, primary nav right. Below the md breakpoint the nav moves
 * into a native <dialog> opened by a "Menu" button (see scripts.js).
 *
 * Usage:
 *   use Gust\Components\SiteHeader;
 *
 *   echo SiteHeader::make();                  // variant inferred: hero on the front page
 *   echo SiteHeader::make(variant: 'hero');   // transparent, overlays the Hero
 */
class SiteHeader extends ComponentBase
{
    protected static string $name = 'site-header';

    public const VARIANTS = ['default', 'hero'];

    public const MENU_LOCATION = 'header';

    /**
     * Create a new SiteHeader component.
     *
     * @param  string|null  $variant  'hero' (transparent, absolute over the hero) or 'default' (dark-umber, in flow).
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        ?string $variant = null,
        array $classes = [],
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Transform args before rendering.
     */
    protected static function transform(array $args): array
    {
        if (! in_array($args['variant'], static::VARIANTS, true)) {
            $args['variant'] = \is_front_page() ? 'hero' : 'default';
        }

        $args['classes'] = array_merge(
            ['site-header', 'site-header--'.$args['variant']],
            $args['variant'] === 'default' ? ['color-context-dark-umber'] : [],
            $args['classes'],
        );

        $args['has_menu'] = \has_nav_menu(static::MENU_LOCATION);
        $args['dialog_id'] = \wp_unique_id('site-header-menu-');

        return $args;
    }
}
