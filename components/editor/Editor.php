<?php

namespace Gust\Components;

use Gust\Component;
use Gust\ComponentBase;

/**
 * Editor Component
 *
 * Usage:
 *   use Gust\Components\Editor;
 *
 *   echo Editor::make();
 */
class Editor extends ComponentBase
{
    protected static string $name = 'editor';

    /**
     * Create a new Editor component.
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
