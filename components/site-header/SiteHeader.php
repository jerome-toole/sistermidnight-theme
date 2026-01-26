<?php

namespace Geum\Components;

use Geum\Component;
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
        if ($header_call_to_action_1 = get_field('header_call_to_action_1', 'option')) {
            $args['call_to_action_1'] = $header_call_to_action_1;
        }

        return $args;
    }
}
