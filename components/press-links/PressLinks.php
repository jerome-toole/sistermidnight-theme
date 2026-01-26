<?php

namespace Geum\Components;

use Geum\ComponentBase;

class PressLinks extends ComponentBase
{
    protected static string $name = 'press-links';

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
        // Sort links by date (newest first)
        if (! empty($args['links'])) {
            usort($args['links'], function ($a, $b) {
                $dateA = $a['date'] ?? '';
                $dateB = $b['date'] ?? '';

                return strcmp($dateB, $dateA);
            });
        }

        return $args;
    }
}
