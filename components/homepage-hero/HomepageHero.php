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
        if (! empty($args['cta'])) {
            $args['cta'] = array_merge($args['cta'], [
                'classes' => ['btn', 'homepage-hero__cta'],
            ]);
        }

        $args['cta'] = null; // Temporarily disable CTA button

        return $args;
    }
}
