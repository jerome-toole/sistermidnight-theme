<?php

namespace Theme\Modules\Radio;

class RadioModule
{
    public static function init(): void
    {
        \add_action('rest_api_init', [RadioService::class, 'registerRoute']);

        // Radio is external: /radio/ must not fall through to WP's canonical guess.
        \add_action('template_redirect', [__CLASS__, 'redirectRadio'], 1);
    }

    public static function redirectRadio(): void
    {
        $path = trim((string) \wp_parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');

        if ($path === 'radio') {
            \wp_redirect('https://radio.sistermidnight.org/', 301, 'Sister Midnight');
            exit;
        }
    }
}
