<?php

namespace Gust\Components;

use Gust\ComponentBase;
use Theme\Modules\Events\Event;
use WP_Post;

/**
 * Event Header Component (partial)
 *
 * Top of the event single: the poster (496 wide, natural ratio, 3px stone-beige border) in
 * the left half; presenter eyebrow, <h1> title, meta row (date, time,
 * venue), excerpt and the ticket CTA in the right half. Past events show a
 * "This event has passed" note in place of the button. Below md the poster
 * comes first at full width, then the text.
 *
 * Usage:
 *   use Gust\Components\EventHeader;
 *
 *   echo EventHeader::make(event: $post);
 *   echo EventHeader::make(event: 42, classes: ['custom']);
 */
class EventHeader extends ComponentBase
{
    protected static string $name = 'event-header';

    /**
     * Create a new Event Header component.
     *
     * @param  WP_Post|Event|int|null  $event  The event post (or its ID / Event wrapper).
     * @return static|null Null when the post is not an event.
     */
    public static function make(
        WP_Post|Event|int|null $event = null,
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

        $args['title'] = $event->title();
        $args['presenter'] = trim($event->presenter());
        $args['excerpt'] = has_excerpt($event->post) ? trim(get_the_excerpt($event->post)) : '';
        $args['venue'] = $event->venue();
        $args['time'] = $event->timeLabel();
        $args['is_past'] = $event->isPast();

        $start = $event->start();
        $args['date'] = $start ? $event->dateLabel('jS F') : '';
        $args['datetime'] = $start ? $start->format('Y-m-d\TH:i') : '';

        $args['cta'] = null;
        if ($event->hasTicketCta()) {
            $link = $event->ticketLink();
            $args['cta'] = Button::make(
                content: $event->ticketCtaLabel(),
                url: $event->ticketUrl(),
                variant: 'primary',
                target: ! empty($link['target']) ? $link['target'] : null,
                classes: ['event-header__cta'],
            );
        }

        $thumbnail = (int) get_post_thumbnail_id($event->post);
        $args['poster'] = $thumbnail
            ? wp_get_attachment_image($thumbnail, 'large', false, [
                'class' => 'event-header__image',
                'loading' => 'eager',
                'fetchpriority' => 'high',
                'decoding' => 'async',
                'sizes' => '(min-width: 768px) 496px, calc(100vw - 32px)',
            ])
            : '';

        $args['classes'] = array_merge(['event-header', 'alignfull', 'has-background'], $args['classes'] ?? []);
        $args['attributes'] ??= [];

        if ($args['is_past']) {
            $args['classes'][] = 'event-header--past';
        }

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
