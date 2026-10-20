<?php

namespace Gust\Components;

use Gust\ComponentBase;

class PageNavigation extends ComponentBase
{
    protected static string $name = 'page-navigation';

    public static function make(
        array $args = [],
    ): ?static {
        return static::createFromArgs($args);
    }

    protected static function validate(array $args): bool
    {
        return ! empty($args['links']);
    }

    protected static function transform(array $args): array
    {
        // Ensure anchors have # prefix
        if (! empty($args['links'])) {
            foreach ($args['links'] as &$link) {
                if (! empty($link['anchor']) && ! str_starts_with($link['anchor'], '#')) {
                    $link['anchor'] = '#'.$link['anchor'];
                }
            }
        }

        return $args;
    }
}
