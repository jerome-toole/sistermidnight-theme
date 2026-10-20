<?php

namespace Gust\Components;

use Gust\Component;
use Gust\ComponentBase;

/**
 * Element Component
 *
 * Usage:
 *   use Gust\Components\Element;
 *
 *   echo Element::make();
 */
class Element extends ComponentBase
{
    protected static string $name = 'element';

    protected static function getDefaults(): array
    {
        return [
            'el' => 'div',
            'content_filter' => 'wp_kses_post',
        ];
    }

    /**
     * Create a new Element component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        ?string $el = null,
        array $classes = [],
        ?string $content = null,
        ?string $content_filter = null,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }
}
