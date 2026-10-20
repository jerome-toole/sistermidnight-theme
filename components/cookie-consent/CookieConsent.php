<?php

namespace Gust\Components;

use Gust\Component;
use Gust\ComponentBase;

/**
 * CookieConsent Component
 *
 * Usage:
 *   use Gust\Components\CookieConsent;
 *
 *   echo CookieConsent::make();
 */
class CookieConsent extends ComponentBase
{
    protected static string $name = 'cookie-consent';

    /**
     * Create a new CookieConsent component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $classes = [],
        array $attributes = [],
        string $content = '',
        ?string $accept_button_text = null,
        ?string $accept_button_text_additional_context = null,
        ?string $reject_button_text = null,
        ?string $reject_button_text_additional_context = null,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Skip rendering if cookie consent is disabled in site options.
     */
    protected static function validate(array $args): bool
    {
        $enabled = get_field('cookie_consent_enabled', 'option');

        // Default to enabled if no option has been saved yet
        return $enabled === null || $enabled === '' || (bool) $enabled;
    }

    /**
     * Transform args before rendering.
     */
    protected static function transform(array $args): array
    {
        // Default translated values
        $args['accept_button_text'] ??= __('Accept', 'gust');
        $args['accept_button_text_additional_context'] ??= __('site cookies', 'gust');
        $args['reject_button_text'] ??= __('Reject', 'gust');
        $args['reject_button_text_additional_context'] ??= __('site cookies', 'gust');

        // ---------------------------------------
        // Default attributes.
        // ---------------------------------------
        $args['attributes'] = array_merge([
            'id' => 'site-cookie-consent',
            'aria-hidden' => 'true',
        ], $args['attributes']);

        if ($accept_button_text = get_field('cookie_consent_accept_button_text', 'option')) {
            $args['accept_button_text'] = $accept_button_text;
        }

        if (
            $accept_button_text_additional_context = get_field(
                'cookie_consent_accept_button_text_additional_context',
                'option'
            )
        ) {
            $args['accept_button_text_additional_context'] = $accept_button_text_additional_context;
        }

        if ($reject_button_text = get_field('cookie_consent_reject_button_text', 'option')) {
            $args['reject_button_text'] = $reject_button_text;
        }

        if (
            $reject_button_text_additional_context = get_field(
                'cookie_consent_reject_button_text_additional_context',
                'option'
            )
        ) {
            $args['reject_button_text_additional_context'] = $reject_button_text_additional_context;
        }

        $content = get_field('cookie_consent_text', 'option');
        if (! empty($content)) {
            $args['content'] = $content;
        } elseif (! empty(get_privacy_policy_url())) {
            $args['content'] = sprintf(
                __('We use cookies. Read more about them in our %s', 'gust'),
                Link::make(
                    content: _x('Privacy Policy', 'Cookie consent link text', 'gust'),
                    url: get_privacy_policy_url(),
                )
            );
        }

        return $args;
    }
}
