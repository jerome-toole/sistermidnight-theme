<?php

namespace Geum\Components;

use Geum\ComponentBase;

class StatsDisplay extends ComponentBase
{
    protected static string $name = 'stats-display';

    public static function make(
        array $args = [],
    ): ?static {
        return static::createFromArgs($args);
    }

    protected static function validate(array $args): bool
    {
        return ! empty($args['stats']);
    }

    protected static function transform(array $args): array
    {
        $args['is_compact'] = ! empty($args['compact']);

        return $args;
    }
}
