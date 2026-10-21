<?php

namespace Gust\Components;

use Gust\Component;
use Gust\ComponentBase;

/**
 * Link Component
 *
 * Links with `target: '_blank'` get the standard external-link treatment:
 * rel="noopener", the `.link-external` class, a visually hidden
 * "(opens in new tab)" and the ↗ arrow icon (see Link::externalMarkup()).
 * Pass `external_icon: false` to keep the hidden text but drop the arrow
 * (icon-only links).
 *
 * Usage:
 *   use Gust\Components\Link;
 *
 *   echo Link::make(title: 'Radio', url: 'https://radio.sistermidnight.org/', target: '_blank');
 */
class Link extends ComponentBase
{
    protected static string $name = 'link';

    protected static function getDefaults(): array
    {
        return [
            'content_filter' => 'wp_kses_post',
        ];
    }

    /**
     * Create a new Link component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        ?string $title = null,
        ?string $url = null,
        ?string $target = null,
        array $attributes = [],
        array $classes = [],
        ?string $prefix = null,
        ?string $suffix = null,
        ?string $assistive_text_before = null,
        ?string $assistive_text_after = null,
        ?string $content_filter = null,
        bool $external_icon = true,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Visually hidden "(opens in new tab)" for any link with target="_blank".
     */
    public static function externalText(): string
    {
        return '<span class="screen-reader-text">'.esc_html__('(opens in new tab)', 'gust').'</span>';
    }

    /**
     * The ↗ arrow icon: the Button arrow rotated 45° anticlockwise.
     */
    public static function externalIcon(): string
    {
        return '<svg class="link-external__icon" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M5 19 19 5M8 5h11v11" /></svg>';
    }

    /**
     * Hidden text + arrow, appended after the label of an external link.
     */
    public static function externalMarkup(bool $icon = true): string
    {
        return static::externalText().($icon ? static::externalIcon() : '');
    }

    protected static function transform(array $args): array
    {
        $args['content'] = $args['content'] ?? $args['title'];

        if (! empty($args['assistive_text_before'])) {
            $args['content'] = Element::make(
                el: 'span',
                content: $args['assistive_text_before'],
                classes: ['screen-reader-text'],
            ).$args['content'];
        }

        if (! empty($args['prefix'])) {
            $args['content'] = $args['prefix'].$args['content'];
        }

        if (! empty($args['assistive_text_after'])) {
            $args['content'] .= Element::make(
                el: 'span',
                content: $args['assistive_text_after'],
                classes: ['screen-reader-text'],
            );
        }

        if (! empty($args['suffix'])) {
            $args['content'] .= $args['suffix'];
        }

        if (! empty($args['url'])) {
            $args['attributes']['href'] = esc_url($args['url']);
        }

        if (! empty($args['target'])) {
            $args['attributes']['target'] = $args['target'];

            if ($args['target'] === '_blank') {
                $args['attributes']['rel'][] = 'noopener';
                $args['classes'][] = 'link-external';
                // Appended by the template after the content filter (wp_kses_post strips the SVG).
                $args['external_markup'] = static::externalMarkup($args['external_icon'] ?? true);
            }
        }

        if (empty($args['content_filter'])) {
            $args['content_filter'] = fn ($content) => $content;
        }

        return $args;
    }
}
