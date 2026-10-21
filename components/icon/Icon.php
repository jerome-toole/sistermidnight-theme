<?php

namespace Gust\Components;

use Gust\ComponentBase;

/**
 * Icon Component
 *
 * Inlines a small UI icon from assets/static/images/icons so it takes
 * `currentColor` (the files use `currentColor` for their fill/stroke, and
 * carry no width/height so CSS sizes them). Always aria-hidden: pair it
 * with visible or visually hidden text.
 *
 * The `arrow` icon is the Figma ↗ (16×15, 3px strokes) used by external
 * links (Link), Button and the radio bar Listen link.
 *
 * Usage:
 *   use Gust\Components\Icon;
 *
 *   echo Icon::make(name: 'arrow', classes: ['button__icon']);
 */
class Icon extends ComponentBase
{
    protected static string $name = 'icon';

    public const NAMES = ['arrow'];

    /** Per-request cache of raw SVG file contents keyed by name. */
    private static array $cache = [];

    /**
     * Create a new Icon component.
     *
     * @param  string  $name  One of self::NAMES.
     * @return static|null Returns null if the icon does not exist.
     */
    public static function make(
        string $name = '',
        array $classes = [],
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function validate(array $args): bool
    {
        return ! empty($args['name']) && static::svg($args['name']) !== null;
    }

    protected static function transform(array $args): array
    {
        $args['classes'] ??= [];
        $args['attributes'] ??= [];

        $args['attributes']['class'] = \classes('icon', 'icon--'.$args['name'], $args['classes']);
        $args['attributes']['aria-hidden'] = 'true';
        $args['attributes']['focusable'] = 'false';

        $args['svg'] = static::svg($args['name']);

        return $args;
    }

    /**
     * Raw SVG markup for a name, read from disk once per request.
     */
    public static function svg(string $name): ?string
    {
        if (array_key_exists($name, self::$cache)) {
            return self::$cache[$name];
        }

        $svg = null;

        if (in_array($name, self::NAMES, true)) {
            $path = \get_theme_file_path("assets/static/images/icons/{$name}.svg");
            $contents = is_readable($path) ? file_get_contents($path) : false;
            $svg = $contents === false ? null : trim($contents);
        }

        return self::$cache[$name] = $svg;
    }
}
