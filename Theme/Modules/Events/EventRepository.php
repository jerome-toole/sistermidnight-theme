<?php

namespace Theme\Modules\Events;

use DateTimeImmutable;
use DateTimeInterface;
use WP_Query;

/**
 * Queries for published events ordered by their ACF start date.
 */
class EventRepository
{
    protected const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * Next events whose start is now or later.
     *
     * @return Event[]
     */
    public static function upcoming(int $limit = 3): array
    {
        return self::query([
            'posts_per_page' => $limit,
            'meta_query' => [
                self::startClause('>=', Event::now()),
            ],
        ]);
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
     * Events with start >= $start and start < $end.
     *
     * @return Event[]
     */
    public static function between(DateTimeInterface $start, DateTimeInterface $end): array
    {
        return self::query([
            'posts_per_page' => -1,
            'meta_query' => [
                'relation' => 'AND',
                self::startClause('>=', $start),
                self::startClause('<', $end),
            ],
        ]);
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
