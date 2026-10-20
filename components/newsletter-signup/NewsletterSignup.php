<?php

namespace Gust\Components;

use Gust\ComponentBase;

class NewsletterSignup extends ComponentBase
{
    protected static string $name = 'newsletter-signup';

    public static function make(
        array $args = [],
    ): ?static {
        return static::createFromArgs($args);
    }

    protected static function transform(array $args): array
    {
        $args['endpoint'] = \rest_url('theme/v1/newsletter');
        $args['nonce'] = \wp_create_nonce('wp_rest');

        return $args;
    }
}
