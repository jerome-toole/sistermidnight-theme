<?php

namespace Gust\Components;

use Gust\ComponentBase;
use WP_Post;

/**
 * News Card Component (partial)
 *
 * Paper card (stone-beige, padding 24) for one post on the news archive:
 * landscape image, date (<time>), title (type-h4) and excerpt. The whole
 * card is clickable through the title link (stretched with ::after); the
 * image is decorative (alt="").
 *
 * Usage:
 *   use Gust\Components\NewsCard;
 *
 *   echo NewsCard::make(post: $post);
 *   echo NewsCard::make(post: 42, heading_level: 3);
 */
class NewsCard extends ComponentBase
{
    protected static string $name = 'news-card';

    protected static function getDefaults(): array
    {
        return [
            'heading_level' => 2,
        ];
    }

    /**
     * Create a new News Card component.
     *
     * @param  WP_Post|int|null  $post  The post (or its ID).
     * @param  int|null  $heading_level  Title heading level (2–6).
     * @return static|null Null without a post.
     */
    public static function make(
        WP_Post|int|null $post = null,
        ?int $heading_level = null,
        array $classes = [],
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function validate(array $args): bool
    {
        return get_post($args['post'] ?? null) instanceof WP_Post;
    }

    protected static function transform(array $args): array
    {
        $post = get_post($args['post']);

        $level = (int) $args['heading_level'];
        $args['heading_tag'] = 'h'.($level >= 2 && $level <= 6 ? $level : 2);

        $args['url'] = get_permalink($post);
        $args['title'] = get_the_title($post);
        $args['date'] = get_the_date('j F Y', $post);
        $args['datetime'] = get_the_date('Y-m-d', $post);
        $args['excerpt'] = trim(get_the_excerpt($post));

        $thumbnail = (int) get_post_thumbnail_id($post);
        $args['image'] = $thumbnail
            ? wp_get_attachment_image($thumbnail, 'medium_large', false, [
                'class' => 'news-card__image',
                'alt' => '',
                'loading' => 'lazy',
                'decoding' => 'async',
                'sizes' => '(min-width: 768px) 560px, calc(100vw - 80px)',
            ])
            : '';

        $args['classes'] = array_merge(['news-card'], $args['classes'] ?? []);

        return $args;
    }
}
