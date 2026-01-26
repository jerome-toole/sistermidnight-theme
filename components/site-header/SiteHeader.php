<?php

namespace Geum\Components;

use Geum\ComponentBase;

/**
 * SiteHeader Component
 *
 * Usage:
 *   use Geum\Components\SiteHeader;
 *
 *   echo SiteHeader::make();
 */
class SiteHeader extends ComponentBase
{
    protected static string $name = 'site-header';

    /**
     * Create a new SiteHeader component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $content = [],
        array $classes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Transform args before rendering.
     */
    protected static function transform(array $args): array
    {
        $args['site_name'] = \get_bloginfo('name');

        if ($logo = \get_field('logo', 'option')) {
            $args['logo'] = $logo;
        }

        if ($radio_url = \get_field('radio_url', 'option')) {
            $args['radio_url'] = $radio_url;
        }

        return $args;
    }
}
