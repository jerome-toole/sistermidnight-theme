<?php

namespace Gust\Components;

use Gust\ComponentBase;

class InstagramFeed extends ComponentBase
{
    protected static string $name = 'instagram-feed';

    public static function make(
        array $args = [],
    ): ?static {
        return static::createFromArgs($args);
    }

    protected static function validate(array $args): bool
    {
        return ! empty($args['posts']);
    }
}
