<?php

namespace Theme\Modules\Events;

use DateTimeImmutable;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Static events feed (uploads/events/feed.json) for the client-rendered calendar,
 * plus a REST route for months older than the feed window.
 *
 * Changes only flag the feed as stale; it is rebuilt once at shutdown so bulk
 * edits and term assignments during a save trigger a single write.
 */
class Feed
{
    public const CRON_HOOK = 'sm_events_feed_regenerate';

    public const GENERATED_OPTION = 'sm_events_feed_generated';

    /** Months of past events kept in the static feed (and per REST chunk). */
    public const PAST_MONTHS = 3;

    protected const REST_NAMESPACE = 'sm/v1';

    protected const CACHE_TTL = HOUR_IN_SECONDS;

    protected static bool $stale = false;

    public static function init(): void
    {
        \add_action('save_post_event', [__CLASS__, 'markStale']);
        \add_action('trashed_post', [__CLASS__, 'maybeMarkStale']);
        \add_action('untrashed_post', [__CLASS__, 'maybeMarkStale']);
        \add_action('deleted_post', [__CLASS__, 'maybeMarkStale'], 10, 2);
        \add_action('set_object_terms', [__CLASS__, 'maybeMarkStale']);

        \add_action(self::CRON_HOOK, [__CLASS__, 'regenerate']);
        \add_action('init', [__CLASS__, 'scheduleCron']);
        \add_action('switch_theme', [__CLASS__, 'unscheduleCron']);

        \add_action('rest_api_init', [__CLASS__, 'registerRoute']);
    }

    public static function markStale(): void
    {
        if (! self::$stale) {
            self::$stale = true;
            \add_action('shutdown', [__CLASS__, 'regenerate']);
        }
    }

    /**
     * Hooks that fire for every post type: only react to events.
     */
    public static function maybeMarkStale(int $postId, mixed $post = null): void
    {
        $type = $post instanceof \WP_Post ? $post->post_type : \get_post_type($postId);

        if ($type === 'event') {
            self::markStale();
        }
    }

    public static function scheduleCron(): void
    {
        if (! \wp_next_scheduled(self::CRON_HOOK)) {
            \wp_schedule_event(time() + DAY_IN_SECONDS, 'daily', self::CRON_HOOK);
        }
    }

    public static function unscheduleCron(): void
    {
        \wp_clear_scheduled_hook(self::CRON_HOOK);
    }

    /**
     * Write the feed file. Returns the file path, or false on failure.
     */
    public static function regenerate(): string|false
    {
        self::$stale = false;

        $from = self::monthStart(Event::now())->modify('-'.self::PAST_MONTHS.' months');
        $events = EventRepository::since($from);

        $generated = time();
        $json = \wp_json_encode(self::payload($events, $generated), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $path = self::path();

        if ($json === false || ! \wp_mkdir_p(dirname($path))) {
            return false;
        }

        // Write then rename so readers never see a half-written file.
        $tmp = $path.'.tmp';
        if (file_put_contents($tmp, $json) === false || ! rename($tmp, $path)) {
            return false;
        }

        \update_option(self::GENERATED_OPTION, $generated, false);

        return $path;
    }

    public static function path(): string
    {
        $base = \wp_upload_dir(null, false)['basedir'];

        // wp_mkdir_p() rejects paths with "../" (e.g. WP-CLI run with a relative --path).
        return (realpath($base) ?: $base).'/events/feed.json';
    }

    /**
     * Public URL with the cache-busting version the calendar module appends.
     */
    public static function url(): string
    {
        $url = \wp_upload_dir(null, false)['baseurl'].'/events/feed.json';

        return \add_query_arg('v', self::generated(), $url);
    }

    public static function generated(): int
    {
        return (int) \get_option(self::GENERATED_OPTION, 0);
    }

    public static function registerRoute(): void
    {
        \register_rest_route(self::REST_NAMESPACE, '/events', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'handleBefore'],
            'permission_callback' => '__return_true',
            'args' => [
                'before' => [
                    'required' => true,
                    'type' => 'string',
                    'pattern' => '^\d{4}-(0[1-9]|1[0-2])$',
                ],
            ],
        ]);
    }

    /**
     * Events in the PAST_MONTHS months before ?before=YYYY-MM (exclusive).
     */
    public static function handleBefore(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $before = (string) $request->get_param('before');
        $end = DateTimeImmutable::createFromFormat('!Y-m', $before, Event::timezone());

        if (! $end) {
            return new WP_Error('sm_events_bad_month', 'before must be YYYY-MM', ['status' => 400]);
        }

        // Keyed on the feed version so a regeneration invalidates every chunk.
        $key = 'sm_events_before_'.$before.'_'.self::generated();
        $data = \get_transient($key);

        if (! is_array($data)) {
            $events = EventRepository::between($end->modify('-'.self::PAST_MONTHS.' months'), $end);
            $data = self::payload($events, self::generated());
            \set_transient($key, $data, self::CACHE_TTL);
        }

        $response = new WP_REST_Response($data);
        $response->header('Cache-Control', 'public, max-age='.self::CACHE_TTL);

        return $response;
    }

    /**
     * @param  Event[]  $events
     * @return array<string,mixed>
     */
    public static function payload(array $events, int $generated): array
    {
        // Load every poster attachment (and its meta) in one query.
        $thumbnails = array_filter(array_map(fn (Event $event) => (int) \get_post_thumbnail_id($event->post), $events));
        if ($thumbnails) {
            \_prime_post_caches($thumbnails, false, true);
        }

        return [
            'generated' => $generated,
            'timezone' => \wp_timezone_string(),
            'events' => array_values(array_filter(array_map([__CLASS__, 'item'], $events))),
        ];
    }

    /**
     * @return array<string,mixed>|null
     */
    public static function item(Event $event): ?array
    {
        $start = $event->start();

        if (! $start) {
            return null;
        }

        $end = $event->end();

        return [
            'id' => $event->id(),
            'title' => \html_entity_decode($event->title(), ENT_QUOTES, 'UTF-8'),
            'url' => $event->url(),
            'start' => $start->format('c'),
            'end_time' => $end ? $end->format('c') : null,
            'end_label' => $event->endLabel(),
            'venue' => $event->venue(),
            'presenter' => $event->presenter(),
            'poster' => self::poster($event),
        ];
    }

    /**
     * @return array{src:string,srcset:string,alt:string}|null
     */
    protected static function poster(Event $event): ?array
    {
        $id = (int) \get_post_thumbnail_id($event->post);
        $image = $id ? \wp_get_attachment_image_src($id, 'medium_large') : false;

        if (! $image) {
            return null;
        }

        return [
            'src' => $image[0],
            'srcset' => (string) \wp_get_attachment_image_srcset($id, 'medium_large'),
            'alt' => (string) \get_post_meta($id, '_wp_attachment_image_alt', true),
        ];
    }

    protected static function monthStart(DateTimeImmutable $date): DateTimeImmutable
    {
        return $date->modify('first day of this month')->setTime(0, 0, 0);
    }
}
