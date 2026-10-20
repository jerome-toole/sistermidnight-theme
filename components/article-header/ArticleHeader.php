<?php

namespace Gust\Components;

use Gust\ComponentBase;
use WP_Post;

/**
 * Article Header Component (partial)
 *
 * Top of the news single (padding 0/104/80): the featured image (531×351)
 * in the left half, the date (<time>), the title (<h1> styled type-h4) and
 * the excerpt in the right half. Stacked below md, image first.
 *
 * Usage:
 *   use Gust\Components\ArticleHeader;
 *
 *   echo ArticleHeader::make(post: $post);
 *   echo ArticleHeader::make(post: 42, classes: ['custom']);
 */
class ArticleHeader extends ComponentBase
{
    protected static string $name = 'article-header';

    /**
     * Create a new Article Header component.
     *
     * @param  WP_Post|int|null  $post  The post (or its ID).
     * @return static|null Null without a post.
     */
    public static function make(
        WP_Post|int|null $post = null,
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

        $args['title'] = get_the_title($post);
        $args['date'] = get_the_date('j F Y', $post);
        $args['datetime'] = get_the_date('Y-m-d', $post);
        $args['excerpt'] = has_excerpt($post) ? trim(get_the_excerpt($post)) : '';

        $thumbnail = (int) get_post_thumbnail_id($post);
        $args['image'] = $thumbnail
            ? wp_get_attachment_image($thumbnail, 'medium_large', false, [
                'class' => 'article-header__image',
                'loading' => 'eager',
                'fetchpriority' => 'high',
                'decoding' => 'async',
                'sizes' => '(min-width: 768px) 531px, calc(100vw - 32px)',
            ])
            : '';

        $args['classes'] = array_merge(['article-header', 'alignfull'], $args['classes'] ?? []);
        $args['attributes'] ??= [];

        return $args;
    }
}
