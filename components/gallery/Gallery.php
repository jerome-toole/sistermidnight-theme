<?php

namespace Gust\Components;

use Gust\ComponentBase;

class Gallery extends ComponentBase
{
    protected static string $name = 'gallery';

    protected static function getDefaults(): array
    {
        return [
            'background_color' => 'white',
        ];
    }

    public static function make(
        array $classes = [],
        ?string $heading = null,
        ?array $images = null,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function validate(array $args): bool
    {
        return ! empty($args['images']);
    }

    protected static function transform(array $args): array
    {
        if (! empty($args['heading'])) {
            $args['heading'] = [
                'heading' => $args['heading'],
                'el' => 'h2',
                'classes' => ['media-gallery__heading'],
            ];
        }

        return $args;
    }
}
