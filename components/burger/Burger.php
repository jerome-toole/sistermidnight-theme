<?php

namespace Gust\Components;

use Gust\Component;
use Gust\ComponentBase;

/**
 * Burger Component
 *
 * Usage:
 *   use Gust\Components\Burger;
 *
 *   echo Burger::make();
 */
class Burger extends ComponentBase
{
    protected static string $name = 'burger';

    /**
     * Create a new Burger component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        string $aria_label = '',
        string $aria_controls = '',
        string $aria_expanded = '',
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
        return $args;
    }
}
