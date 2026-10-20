<?php

namespace Gust\Components;

use Gust\Component;
use Gust\ComponentBase;

/**
 * Animate Component
 *
 * Usage:
 *   use Gust\Components\Animate;
 *
 *   echo Animate::make();
 */
class Animate extends ComponentBase
{
    protected static string $name = 'animate';

    /**
     * Create a new Animate component.
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
