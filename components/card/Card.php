<?php

namespace Gust\Components;

use Gust\ComponentBase;

/**
 * Card Component (ACF block `theme/card`)
 *
 * The paper card (stone-beige, padding 24 / 20) used three ways: an
 * announcement in the Upcoming grid, the member card and the newsletter card.
 * Heading (h3), rich text, an optional "Read more" link, an optional small
 * illustration in the top-right corner and an optional Newsletter Form.
 *
 * In the editor the heading is typed in place and the text, link and
 * newsletter toggle open from an inline toolbar; illustration and size live
 * in the block inspector.
 *
 * Usage:
 *   use Gust\Components\Card;
 *
 *   echo Card::make(heading: 'Become a Member', text: '<p>…</p>', link: ['url' => '/membership/']);
 *   echo Card::make(heading: 'Stay in touch', text: '<p>…</p>', illustration: 'disco-ball', show_newsletter_form: true, size: 'sm');
 */
class Card extends ComponentBase
{
    protected static string $name = 'card';

    public const SIZES = ['sm', 'lg'];

    protected static function getDefaults(): array
    {
        return [
            'size' => 'lg',
            'show_newsletter_form' => false,
        ];
    }

    /**
     * Create a new Card component.
     *
     * @param  string  $heading  Card heading (h3).
     * @param  string  $text  Rich text (HTML).
     * @param  array|null  $link  ACF link array: url, title, target. Rendered as a "Read more" link.
     * @param  string|null  $illustration  Illustration name, shown small in the top-right corner.
     * @param  bool|null  $show_newsletter_form  Render the Newsletter Form under the text.
     * @param  string|null  $size  One of self::SIZES.
     * @return static|null Null when there is nothing to show (outside the editor).
     */
    public static function make(
        string $heading = '',
        string $text = '',
        ?array $link = null,
        ?string $illustration = null,
        ?bool $show_newsletter_form = null,
        ?string $size = null,
        bool $is_preview = false,
        array $classes = [],
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function validate(array $args): bool
    {
        $args = static::fromBlockFields($args);

        return ! empty($args['is_preview'])
            || trim((string) ($args['heading'] ?? '')) !== ''
            || trim(strip_tags((string) ($args['text'] ?? ''))) !== ''
            || ! empty($args['show_newsletter_form']);
    }

    protected static function transform(array $args): array
    {
        $args = static::fromBlockFields($args);

        $args['heading'] = trim((string) ($args['heading'] ?? ''));
        $args['text'] = trim((string) ($args['text'] ?? ''));
        $args['show_newsletter_form'] = ! empty($args['show_newsletter_form']);
        $args['attributes'] ??= [];

        if (! in_array($args['size'], self::SIZES, true)) {
            $args['size'] = 'lg';
        }

        $args['illustration'] = in_array($args['illustration'] ?? '', Illustration::NAMES, true)
            ? $args['illustration']
            : '';

        // Link: an ACF link array (url, title, target) or a plain URL string.
        $link = $args['link'] ?? null;

        if (is_string($link) && $link !== '') {
            $link = ['url' => $link];
        }

        $args['link'] = is_array($link) && ! empty($link['url'])
            ? [
                'url' => $link['url'],
                'title' => trim((string) ($link['title'] ?? '')) ?: __('Read more', 'gust'),
                'target' => $link['target'] ?? null,
            ]
            : null;

        $args['form'] = $args['show_newsletter_form'] ? NewsletterForm::make(classes: ['card__form']) : null;

        $args['classes'] = array_merge([
            'card',
            'card--'.$args['size'],
        ], $args['classes'] ?? []);

        if ($args['illustration']) {
            $args['classes'][] = 'card--illustrated';
        }

        return $args;
    }

    /** Block fields are prefixed (card_heading…) so their names stay unique for inline editing. */
    protected static function fromBlockFields(array $args): array
    {
        foreach (['heading', 'text', 'link', 'illustration', 'show_newsletter_form', 'size'] as $field) {
            if (array_key_exists("card_{$field}", $args)) {
                $args[$field] = $args["card_{$field}"] ?? $args[$field] ?? null;
                unset($args["card_{$field}"]);
            }
        }

        return $args;
    }

    /**
     * ACF inline text editing attributes for a block field (name without the
     * card_ prefix). Empty outside an editor preview: ACF 6.8 sets its
     * "doing block preview" flag on front-end renders too.
     */
    public function inlineText(string $field, string $placeholder): string
    {
        if (! $this->is_preview || ! \function_exists('acf_inline_text_editing_attrs')) {
            return '';
        }

        return \acf_inline_text_editing_attrs("card_{$field}", ['placeholder' => $placeholder]);
    }

    /** ACF inline toolbar attributes for a set of block fields (names without the card_ prefix). */
    public function inlineToolbar(array $fields, string $title): string
    {
        if (! $this->is_preview || ! \function_exists('acf_inline_toolbar_editing_attrs')) {
            return '';
        }

        $fields = array_map(fn (string $field) => "card_{$field}", $fields);

        return (string) \acf_inline_toolbar_editing_attrs($fields, ['toolbar_title' => $title]);
    }
}
