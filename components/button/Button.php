<?php

namespace Gust\Components;

use Gust\ComponentBase;

/**
 * Button Component
 *
 * Renders a <button>, or an <a> when `url` is given. The base `.button` class
 * is unstyled so toggles (accordion, sub-menu) can use it as a plain control.
 * Styled variants:
 *   primary  burnt-sienna fill, 200×58, label + the ↗ arrow (Icon); pass `icon: 'arrow-right'` for the → used by in-place actions
 *   link     underlined text + arrow
 *
 * Usage:
 *   use Gust\Components\Button;
 *
 *   echo Button::make(content: 'Buy tickets', url: '/tickets/', variant: 'primary');
 *   echo Button::make(content: 'Open map', url: 'https://…', variant: 'link', target: '_blank');
 *   echo Button::make(content: 'Sign up', type: 'submit', variant: 'primary');
 */
class Button extends ComponentBase
{
    protected static string $name = 'button';

    public const VARIANTS = ['primary', 'link'];

    /**
     * Create a new Button component.
     *
     * @param  string  $content  Label (HTML allowed).
     * @param  string  $variant  One of self::VARIANTS, or empty for an unstyled control.
     * @param  string|null  $url  Render an <a href> instead of a <button>.
     * @param  string|null  $target  Link target; `_blank` adds rel="noopener" and a hidden "(opens in new tab)".
     * @param  bool|null  $arrow  Show the arrow icon. Defaults to true for styled variants.
     * @param  string  $icon  Icon name for the arrow (Icon::NAMES): `arrow` (↗) or `arrow-right` (→).
     * @param  bool  $screen_reader_text  Visually hide the label.
     * @param  string  $type  Button type attribute (ignored for links).
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        string $content = '',
        string $variant = '',
        ?string $url = null,
        ?string $target = null,
        ?bool $arrow = null,
        string $icon = 'arrow',
        array $classes = [],
        array $attributes = [],
        bool $screen_reader_text = false,
        string $type = 'button',
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function validate(array $args): bool
    {
        return ! empty($args['content']);
    }

    protected static function transform(array $args): array
    {
        $args['classes'] ??= [];
        $args['attributes'] ??= [];

        $variant = in_array($args['variant'] ?? '', self::VARIANTS, true) ? $args['variant'] : '';
        $args['variant'] = $variant;

        if ($variant !== '') {
            $args['classes'][] = 'button--'.$variant;
        }

        $args['arrow'] = $args['arrow'] ?? ($variant !== '');

        if (! empty($args['screen_reader_text'])) {
            $args['content'] = Element::make(
                el: 'span',
                content: $args['content'],
                classes: ['screen-reader-text'],
            );
        }

        $args['is_link'] = ! empty($args['url']);

        if ($args['is_link']) {
            $args['attributes']['href'] = esc_url($args['url']);

            if (! empty($args['target'])) {
                $args['attributes']['target'] = $args['target'];

                if ($args['target'] === '_blank') {
                    $args['attributes']['rel'][] = 'noopener';
                    $args['content'] .= Link::externalText();
                }
            }
        } else {
            $args['attributes']['type'] = $args['type'] ?? 'button';
        }

        return $args;
    }
}
