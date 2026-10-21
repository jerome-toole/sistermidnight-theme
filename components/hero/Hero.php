<?php

namespace Gust\Components;

use Gust\ComponentBase;

/**
 * Hero Component
 *
 * Front-page media banner on burnt-sienna: a poster image (the LCP element,
 * fetchpriority="high"), an optional muted background video layered over it
 * once it plays, and a 44×44 pause/play toggle. The site header (hero variant) sits on top of it.
 *
 * The video has no `autoplay` attribute: scripts.js starts it unless the
 * visitor prefers reduced motion or reduced data, so without JS (or with
 * either preference) the poster is what shows.
 *
 * Usage:
 *   use Gust\Components\Hero;
 *
 *   echo Hero::fromOptions();                                  // Header options: hero_video, hero_poster
 *   echo Hero::make(poster: 316);                              // explicit
 *   echo Hero::make(poster: 316, video: ['url' => '…/clip.mp4', 'mime_type' => 'video/mp4']);
 */
class Hero extends ComponentBase
{
    protected static string $name = 'hero';

    /**
     * Create a new Hero component.
     *
     * @param  int|array|null  $poster  Attachment ID (or ACF image array). Required.
     * @param  array|null  $video  ACF file array (`url`, `mime_type`) or `['url' => …]`. MP4 only.
     * @return static|null Returns null if there is no poster.
     */
    public static function make(
        int|array|null $poster = null,
        ?array $video = null,
        array $classes = [],
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Build from the Header options page (hero_poster, hero_video).
     */
    public static function fromOptions(array $classes = [], array $attributes = []): ?static
    {
        if (! \function_exists('get_field')) {
            return null;
        }

        $poster = \get_field('hero_poster', 'option');
        $video = \get_field('hero_video', 'option');

        return static::make(
            poster: is_numeric($poster) || is_array($poster) ? $poster : null,
            video: is_array($video) ? $video : null,
            classes: $classes,
            attributes: $attributes,
        );
    }

    protected static function validate(array $args): bool
    {
        return static::posterId($args['poster'] ?? null) > 0;
    }

    protected static function transform(array $args): array
    {
        $args['classes'] ??= [];
        $args['attributes'] ??= [];

        $args['poster_id'] = static::posterId($args['poster']);

        $video = $args['video'] ?? null;
        $args['video_url'] = ! empty($video['url']) ? (string) $video['url'] : '';
        $args['video_type'] = ! empty($video['mime_type']) ? (string) $video['mime_type'] : 'video/mp4';

        $args['classes'] = array_merge(['hero', 'color-context-burnt-sienna'], $args['classes']);
        $args['attributes']['data-hero'] = true;

        return $args;
    }

    private static function posterId(int|array|null $poster): int
    {
        if (is_array($poster)) {
            return (int) ($poster['ID'] ?? $poster['id'] ?? 0);
        }

        return (int) $poster;
    }
}
