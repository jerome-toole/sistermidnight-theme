<?php

namespace Theme\Modules\Events;

use DateTimeImmutable;
use DateTimeInterface;
use WP_Query;

/**
 * Queries for published events ordered by their ACF start date. Repeating
 * events are expanded into one Event per occurrence (see Event::withStart()),
 * up to Schedule::horizon().
 */
class EventRepository
{
    protected const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * Next events whose start is now or later; a repeating event counts once, at its next occurrence.
     *
     * @return Event[]
     */
    public static function upcoming(int $limit = 3): array
    {
        $now = Event::now();
        $oneOffs = self::query([
            'posts_per_page' => $limit,
            'meta_query' => [
                'relation' => 'AND',
                self::oneOffClause(),
                self::startClause('>=', $now),
            ],
        ]);
        $repeating = array_filter(array_map(
            fn (Event $event) => $event->occurrencesBetween($now, Schedule::horizon())[0] ?? null,
            self::repeating(Schedule::horizon())
        ));

        return array_slice(self::sort(array_merge($oneOffs, $repeating)), 0, $limit);
    }

    /**
     * All events starting within a calendar month.
     *
     * @return Event[]
     */
    public static function forMonth(int $year, int $month): array
    {
        $start = (new DateTimeImmutable('now', Event::timezone()))
            ->setDate($year, $month, 1)
            ->setTime(0, 0, 0);

        return self::between($start, $start->modify('+1 month'));
    }

    /**
     * All events with start >= $start (repeating ones up to the horizon).
     *
     * @return Event[]
     */
    public static function since(DateTimeInterface $start): array
    {
        $oneOffs = self::query([
            'posts_per_page' => -1,
            'meta_query' => [
                'relation' => 'AND',
                self::oneOffClause(),
                self::startClause('>=', $start),
            ],
        ]);

        return self::sort(array_merge($oneOffs, self::occurrences($start, Schedule::horizon())));
    }

    /**
     * Events with start >= $start and start < $end.
     *
     * @return Event[]
     */
    public static function between(DateTimeInterface $start, DateTimeInterface $end): array
    {
        $oneOffs = self::query([
            'posts_per_page' => -1,
            'meta_query' => [
                'relation' => 'AND',
                self::oneOffClause(),
                self::startClause('>=', $start),
                self::startClause('<', $end),
            ],
        ]);

        return self::sort(array_merge($oneOffs, self::occurrences($start, $end)));
    }

    /**
     * Occurrences of every repeating event in [$start, $end), capped at the horizon.
     *
     * @return Event[]
     */
    protected static function occurrences(DateTimeInterface $start, DateTimeInterface $end): array
    {
        return array_merge([], ...array_map(
            fn (Event $event) => $event->occurrencesBetween($start, $end),
            self::repeating($end)
        ));
    }

    /**
     * Repeating events whose first start is before $before.
     *
     * @return Event[]
     */
    protected static function repeating(DateTimeInterface $before): array
    {
        return self::query([
            'posts_per_page' => -1,
            'meta_query' => [
                'relation' => 'AND',
                ['key' => 'repeat', 'value' => '', 'compare' => '!='],
                self::startClause('<', $before),
            ],
        ]);
    }

    /**
     * @param  Event[]  $events
     * @return Event[]
     */
    protected static function sort(array $events): array
    {
        usort($events, fn (Event $a, Event $b) => $a->start() <=> $b->start());

        return array_values($events);
    }

    /**
     * Shared base arguments; merged with per-query overrides.
     *
     * @return array<string,mixed>
     */
    public static function baseArgs(): array
    {
        return [
            'post_type' => 'event',
            'post_status' => 'publish',
            'meta_key' => PostType::START_META_KEY,
            'orderby' => 'meta_value',
            'order' => 'ASC',
            'no_found_rows' => true,
            'ignore_sticky_posts' => true,
        ];
    }

    /**
     * @param  array<string,mixed>  $args
     * @return Event[]
     */
    protected static function query(array $args): array
    {
        $query = new WP_Query(array_merge(self::baseArgs(), $args));

        return array_values(array_filter(array_map(
            fn ($post) => Event::fromPost($post),
            $query->posts
        )));
    }

    /**
     * Events without a repeat rule.
     *
     * @return array<string,mixed>
     */
    protected static function oneOffClause(): array
    {
        return [
            'relation' => 'OR',
            ['key' => 'repeat', 'compare' => 'NOT EXISTS'],
            ['key' => 'repeat', 'value' => ''],
        ];
    }

    /**
     * @return array<string,mixed>
     */
    protected static function startClause(string $compare, DateTimeInterface $date): array
    {
        return [
            'key' => PostType::START_META_KEY,
            'value' => $date->setTimezone(Event::timezone())->format(self::DATE_FORMAT),
            'compare' => $compare,
            'type' => 'DATETIME',
        ];
    }
}
