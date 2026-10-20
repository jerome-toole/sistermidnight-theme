<?php

namespace Gust\Components;

use Gust\ComponentBase;

/**
 * Illustration Component
 *
 * Renders a decorative SVG inline from assets/static/images/illustrations
 * so its fill follows `currentColor`. Always aria-hidden.
 *
 * Usage:
 *   use Gust\Components\Illustration;
 *
 *   echo Illustration::make(name: 'cat');
 *   echo Illustration::make(name: 'moon', size: 'sm');
 */
class Illustration extends ComponentBase
{
    protected static string $name = 'illustration';

    public const NAMES = [
        'cat',
        'dancers',
        'disco-ball',
        'flames',
        'flower-roots',
        'guitar-acoustic',
        'guitar-electric',
        'moon',
        'plants-potted',
        'record-face',
        'record-fist',
        'record-flames',
        'record-heart',
        'saxophone',
        'stars',
        'sun-petals',
        'sun-wavy',
        'trumpet',
    ];

    public const SIZES = ['sm', 'md', 'lg'];

    /** Per-request cache of raw SVG file contents keyed by name. */
    private static array $cache = [];

    /**
     * Create a new Illustration component.
     *
     * @param  string  $name  One of self::NAMES.
     * @param  string  $size  Optional size modifier: sm, md or lg. Empty fills the container width.
     * @return static|null Returns null if the illustration does not exist.
     */
    public static function make(
        string $name = '',
        string $size = '',
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

        if (! empty($args['size']) && in_array($args['size'], self::SIZES, true)) {
            $args['classes'][] = 'illustration--'.$args['size'];
        }

        $args['attributes'] ??= [];
        $args['attributes']['class'] = \classes('illustration', $args['classes']);
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
            $path = \get_theme_file_path("assets/static/images/illustrations/{$name}.svg");
            $contents = is_readable($path) ? file_get_contents($path) : false;
            $svg = $contents === false ? null : trim($contents);
        }

        return self::$cache[$name] = $svg;
    }
}
