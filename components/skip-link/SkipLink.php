<?php

namespace Gust\Components;

use Gust\Component;
use Gust\ComponentBase;

/**
 * SkipLink Component
 *
 * Usage:
 *   use Gust\Components\SkipLink;
 *
 *   echo SkipLink::make();
 */
class SkipLink extends ComponentBase
{
    protected static string $name = 'skip-link';

    /**
     * Create a new SkipLink component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $classes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }
}
