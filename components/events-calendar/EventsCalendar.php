<?php

namespace Gust\Components;

use DateTimeImmutable;
use Gust\ComponentBase;
use Theme\Modules\Events\Event;
use Theme\Modules\Events\EventRepository;
use Theme\Modules\Events\Feed;

/**
 * Events Calendar Component (partial)
 *
 * What's On calendar. The server renders the header (Calendar // List toggle,
 * month nav) and a fallback list of the next events; scripts.js fetches the
 * JSON feed and draws the month <table> or the list grouped by month in the
 * body. The month and view come from ?month=YYYY-MM and ?view=calendar|list
 * so the server HTML matches the URL before the script runs.
 *
 * Usage:
 *   use Gust\Components\EventsCalendar;
 *
 *   echo EventsCalendar::make();                       // month/view from the request
 *   echo EventsCalendar::make(month: '2026-04', view: 'list', count: 5);
 */
class EventsCalendar extends ComponentBase
{
    protected static string $name = 'events-calendar';

    public const VIEWS = ['calendar', 'list'];

    protected const MONTH_PATTERN = '/^\d{4}-(0[1-9]|1[0-2])$/';

    protected static function getDefaults(): array
    {
        return [
            'count' => 10,
        ];
    }

    /**
     * Create a new Events Calendar component.
     *
     * @param  int|null  $count  Events in the server-rendered fallback list.
     * @param  string|null  $month  YYYY-MM; defaults to ?month= or the current month.
     * @param  string|null  $view  One of self::VIEWS; defaults to ?view= or calendar.
     */
    public static function make(
        ?int $count = null,
        ?string $month = null,
        ?string $view = null,
        array $classes = [],
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function transform(array $args): array
    {
        $timezone = Event::timezone();
        $now = Event::now();

        $month = (string) ($args['month'] ?? static::query('month'));
        if (! preg_match(self::MONTH_PATTERN, $month)) {
            $month = $now->format('Y-m');
        }

        $view = (string) ($args['view'] ?? static::query('view'));
        if (! in_array($view, self::VIEWS, true)) {
            $view = 'calendar';
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m', $month, $timezone) ?: $now->modify('first day of this month');
        $base = (string) \get_post_type_archive_link('event');

        $args['month'] = $month;
        $args['view'] = $view;
        $args['month_label'] = \wp_date('F Y', $date->getTimestamp(), $timezone);
        $args['prev_url'] = static::url($base, $date->modify('-1 month')->format('Y-m'), $view);
        $args['next_url'] = static::url($base, $date->modify('+1 month')->format('Y-m'), $view);
        $args['view_urls'] = [
            'calendar' => static::url($base, $month, 'calendar'),
            'list' => static::url($base, $month, 'list'),
        ];

        $args['feed_url'] = Feed::url();
        $args['rest_url'] = \rest_url('sm/v1/events');
        $args['timezone'] = \wp_timezone_string();
        $args['feed_from'] = $now->modify('first day of this month')->modify('-'.Feed::PAST_MONTHS.' months')->format('Y-m');

        $count = (int) ($args['count'] ?? 10);
        $args['events'] = array_map([static::class, 'row'], EventRepository::upcoming($count > 0 ? $count : 10));

        $args['classes'] = array_merge(['events-calendar', 'alignfull'], $args['classes'] ?? []);
        $args['attributes'] = array_merge([
            'data-events-calendar' => '',
            'data-feed' => $args['feed_url'],
            'data-rest' => $args['rest_url'],
            'data-month' => $month,
            'data-view' => $view,
            'data-from' => $args['feed_from'],
            'data-timezone' => $args['timezone'],
        ], $args['attributes'] ?? []);

        return $args;
    }

    /**
     * @return array{title:string,url:string,date:string,datetime:string,time:string,venue:string}
     */
    protected static function row(Event $event): array
    {
        $start = $event->start();

        return [
            'title' => $event->title(),
            'url' => $event->url(),
            'date' => $event->dateLabel('D j M'),
            'datetime' => $start ? $start->format('c') : '',
            'time' => $start ? $start->format('H:i') : '',
            'venue' => $event->venue(),
        ];
    }

    protected static function url(string $base, string $month, string $view): string
    {
        return \add_query_arg([
            'month' => $month,
            'view' => $view === 'list' ? 'list' : false,
        ], $base);
    }

    protected static function query(string $key): string
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only display state
        return isset($_GET[$key]) ? \sanitize_text_field(\wp_unslash((string) $_GET[$key])) : '';
    }
}
