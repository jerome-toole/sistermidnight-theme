<?php

namespace Gust\Components;

use Gust\ComponentBase;

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
        return ! empty($args['value']);
    }
}
