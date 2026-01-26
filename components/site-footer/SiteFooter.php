<?php

namespace Geum\Components;

use Geum\ComponentBase;

/**
 * SiteFooter Component
 *
 * Usage:
 *   use Geum\Components\SiteFooter;
 *
 *   echo SiteFooter::make();
 */
class SiteFooter extends ComponentBase
{
    protected static string $name = 'site-footer';

    /**
     * Create a new SiteFooter component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
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
        $args['footer_text_top'] = \get_field('footer_text_top', 'option');
        $args['footer_text_bottom'] = \get_field('footer_text_bottom', 'option');
        $args['footer_legal_text'] = \get_field('footer_legal_text', 'option');
        $args['radio_url'] = \get_field('radio_url', 'option');

        return $args;
    }
}
