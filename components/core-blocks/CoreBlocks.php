<?php

namespace Gust\Components;

use Gust\Component;
use Gust\ComponentBase;

/**
 * CoreBlocks Component
 *
 * Usage:
 *   use Gust\Components\CoreBlocks;
 *
 *   echo CoreBlocks::make();
 */
class CoreBlocks extends ComponentBase
{
    protected static string $name = 'core-blocks';

    /**
     * Create a new CoreBlocks component.
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
