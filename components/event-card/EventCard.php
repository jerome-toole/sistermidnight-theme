<?php

namespace Gust\Components;

use Gust\ComponentBase;
use Theme\Modules\Events\Event;
use WP_Post;

/**
 * Event Card Component (partial)
 *
 * Paper card (stone-beige, padding 24) for one event: portrait poster,
 * optional presenter eyebrow, title, excerpt and a meta row (date, time,
 * venue). The whole card is one link named by the title; the poster is
 * decorative (alt="").
 *
 * `lg` (front page Upcoming grid) caps the poster at 410px wide; `sm`
 * (What's On featured row) lets it fill the card.
 *
 * Usage:
 *   use Gust\Components\EventCard;
 *
 *   echo EventCard::make(event: $post);
 *   echo EventCard::make(event: $post, size: 'sm', show_presenter: false, heading_level: 2);
 */
class EventCard extends ComponentBase
{
    protected static string $name = 'event-card';

    public const SIZES = ['sm', 'lg'];

    protected static function getDefaults(): array
    {
        return [
            'size' => 'lg',
            'show_presenter' => true,
            'heading_level' => 3,
        ];
    }

    /**
     * Create a new Event Card component.
     *
     * @param  WP_Post|Event|int|null  $event  The event post (or its ID / Event wrapper).
     * @param  string|null  $size  One of self::SIZES.
     * @param  bool|null  $show_presenter  Show the presenter eyebrow when set on the event.
     * @param  int|null  $heading_level  Title heading level (2–6).
     * @param  string|null  $sizes  `sizes` attribute for the poster.
     * @param  bool  $eager  Load the poster eagerly with fetchpriority="high" (the page's LCP candidate).
     * @return static|null Null when the post is not an event.
     */
    public static function make(
        WP_Post|Event|int|null $event = null,
        ?string $size = null,
        ?bool $show_presenter = null,
        ?int $heading_level = null,
        ?string $sizes = null,
        bool $eager = false,
        array $classes = [],
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function validate(array $args): bool
    {
        return static::toEvent($args['event'] ?? null) !== null;
    }

    protected static function transform(array $args): array
    {
        $event = static::toEvent($args['event']);

        if (! in_array($args['size'], self::SIZES, true)) {
            $args['size'] = 'lg';
        }

        $level = (int) $args['heading_level'];
        $args['heading_tag'] = 'h'.($level >= 2 && $level <= 6 ? $level : 3);
        $args['title_id'] = 'event-card-'.$event->id().'-'.wp_unique_id();

        $args['url'] = $event->url();
        $args['title'] = $event->title();
        $args['presenter'] = $args['show_presenter'] ? trim($event->presenter()) : '';
        $args['excerpt'] = has_excerpt($event->post) ? trim(get_the_excerpt($event->post)) : '';
        $args['venue'] = $event->venue();
        $args['time'] = $event->timeLabel();

        $start = $event->start();
        $args['date'] = $start ? $event->dateLabel('jS F') : '';
        $args['datetime'] = $start ? $start->format('Y-m-d\TH:i') : '';

        $thumbnail = (int) get_post_thumbnail_id($event->post);
        $args['poster'] = $thumbnail
            ? wp_get_attachment_image($thumbnail, 'medium_large', false, [
                'class' => 'event-card__image',
                'alt' => '',
                'loading' => $args['eager'] ? 'eager' : 'lazy',
                'fetchpriority' => $args['eager'] ? 'high' : false,
                'decoding' => 'async',
                'sizes' => $args['sizes'] ?: ($args['size'] === 'sm'
                    ? '(min-width: 1024px) 380px, calc(100vw - 80px)'
                    : '(min-width: 1024px) 410px, calc(100vw - 80px)'),
            ])
            : '';

        $args['classes'] = array_merge(['event-card', 'event-card--'.$args['size']], $args['classes'] ?? []);

        return $args;
    }

    protected static function toEvent(mixed $event): ?Event
    {
        if ($event instanceof Event) {
            return $event;
        }

        return $event ? Event::fromPost($event) : null;
    }
}
