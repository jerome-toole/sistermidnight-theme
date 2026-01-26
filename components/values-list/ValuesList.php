<?php

namespace Geum\Components;

use Geum\ComponentBase;

class ValuesList extends ComponentBase
{
    protected static string $name = 'values-list';

    public static function make(
        array $args = [],
    ): ?static {
        return static::createFromArgs($args);
    }

    protected static function validate(array $args): bool
    {
        return ! empty($args['values']);
    }

    protected static function transform(array $args): array
    {
        if (! empty($args['values'])) {
            $args['items'] = [];
            foreach ($args['values'] as $index => $value) {
                $args['items'][] = [
                    'number' => $index + 1,
                    'title' => $value['title'] ?? '',
                    'description' => $value['description'] ?? '',
                ];
            }
        }

        return $args;
    }
}
