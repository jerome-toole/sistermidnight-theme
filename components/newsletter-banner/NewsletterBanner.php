<?php

namespace Gust\Components;

use Gust\ComponentBase;

/**
 * NewsletterBanner Component
 *
 * Full-width stone-beige band after the page content: heading, text, the
 * Newsletter Form and an illustration on the right (hidden below md).
 * Content comes from the Newsletter options; `fromOptions()` also applies
 * the page rules (never on the front page, off when `hide_newsletter` is set).
 *
 * Usage:
 *   use Gust\Components\NewsletterBanner;
 *
 *   echo NewsletterBanner::fromOptions();   // footer.php
 *   echo NewsletterBanner::make(heading: 'Newsletter', text: '…', illustration: 'trumpet');
 */
class NewsletterBanner extends ComponentBase
{
    protected static string $name = 'newsletter-banner';

    /**
     * Create a new NewsletterBanner component.
     *
     * @param  string  $heading  Defaults to "Newsletter".
     * @param  string  $text  Plain text; line breaks are kept.
     * @param  int|null  $form_id  Gravity Forms ID, defaults to the `newsletter_form_id` option.
     * @param  string  $illustration  One of Illustration::NAMES, or empty for none.
     * @return static|null Returns null when the form cannot render.
     */
    public static function make(
        string $heading = '',
        string $text = '',
        ?int $form_id = null,
        string $illustration = '',
        array $classes = [],
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * The banner for the current request from the Newsletter options, or null on
     * the front page and on pages (or an archive's router page) with `hide_newsletter` checked.
     */
    public static function fromOptions(array $classes = [], array $attributes = []): ?static
    {
        if (\is_front_page() || ! \function_exists('get_field')) {
            return null;
        }

        // Archives and other routed requests use their router page's setting.
        $page_id = \is_singular() ? \get_queried_object_id() : \Gust\Router::getPage()?->ID;

        if ($page_id && \get_field('hide_newsletter', $page_id)) {
            return null;
        }

        // Dark band after a stone-beige section, so the two don't run together
        if (\is_singular('article') || (\is_singular() && static::endsOnStoneBeige(\get_post()))) {
            $classes[] = 'newsletter-banner--dark-umber';
        }

        $form_id = \get_field('newsletter_form_id', 'option');
        $illustration = \get_field('newsletter_illustration', 'option');

        return static::make(
            heading: (string) \get_field('newsletter_heading', 'option'),
            text: (string) \get_field('newsletter_text', 'option'),
            form_id: is_numeric($form_id) ? (int) $form_id : null,
            illustration: is_string($illustration) ? $illustration : '',
            classes: $classes,
            attributes: $attributes,
        );
    }

    /**
     * Whether the last block of a post's content is stone-beige (a Section or Image Pair
     * with a stone-beige background).
     */
    protected static function endsOnStoneBeige(?\WP_Post $post): bool
    {
        if (! $post) {
            return false;
        }

        $blocks = array_values(array_filter(
            \parse_blocks($post->post_content),
            fn ($block) => ! empty($block['blockName']),
        ));
        $last = end($blocks);

        foreach ((array) ($last['attrs']['data'] ?? []) as $key => $value) {
            if (is_string($key) && str_ends_with($key, '_background') && $key[0] !== '_') {
                return $value === 'stone-beige';
            }
        }

        return false;
    }

    protected static function validate(array $args): bool
    {
        return NewsletterForm::formExists(NewsletterForm::resolveFormId($args['form_id'] ?? null));
    }

    protected static function transform(array $args): array
    {
        $args['classes'] = array_merge(['newsletter-banner'], $args['classes'] ?? []);
        $args['attributes'] ??= [];

        if (empty($args['attributes']['id']) && $anchor = NewsletterForm::claimAnchor()) {
            $args['attributes']['id'] = $anchor;
        }

        $args['heading'] = trim($args['heading'] ?? '') ?: __('Newsletter', 'gust');
        $args['heading_id'] = \wp_unique_id('newsletter-banner-heading-');
        $args['form'] = NewsletterForm::make(form_id: $args['form_id'] ?? null);
        $args['illustration'] = in_array($args['illustration'] ?? '', Illustration::NAMES, true)
            ? $args['illustration']
            : '';

        return $args;
    }
}
