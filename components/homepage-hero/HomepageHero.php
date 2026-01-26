<?php

namespace Geum\Components;

use Geum\ComponentBase;

class HomepageHero extends ComponentBase
{
    protected static string $name = 'homepage-hero';

    public static function make(
        array $args = [],
    ): ?static {
        return static::createFromArgs($args);
    }

    protected static function transform(array $args): array
    {
        if (! empty($args['heading'])) {
            $args['heading'] = [
                'heading' => $args['heading'],
                'el' => 'h1',
                'classes' => ['homepage-hero__heading', 'is-style-type-hero'],
            ];
        }

        if (! empty($args['cta'])) {
            $args['cta'] = array_merge($args['cta'], [
                'classes' => ['btn', 'homepage-hero__cta'],
            ]);
        }

        return $args;
    }
}
