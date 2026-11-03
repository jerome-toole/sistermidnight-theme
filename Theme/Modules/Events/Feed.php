<?php

namespace Theme\Modules\Events;

use DateTimeImmutable;

/**
 * Static events feed (uploads/events/feed.json) for the client-rendered calendar:
 * every upcoming event plus the past PAST_MONTHS calendar months, with repeating
 * events expanded up to Schedule::HORIZON_MONTHS ahead (the daily cron moves
 * that window along). Older events
 * stay published (their pages still work) but drop out of the calendar here.
 *
 * Changes only flag the feed as stale; it is rebuilt once at shutdown so bulk
 * edits and term assignments during a save trigger a single write.
 */
class Feed
{
    public const CRON_HOOK = 'sm_events_feed_regenerate';

    public const GENERATED_OPTION = 'sm_events_feed_generated';

    /** Calendar months of past events kept in the feed, before the current one. */
    public const PAST_MONTHS = 2;

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

        // Occurrences of a repeating event share a post, so their ids add the date.
        return [
            'id' => $event->isRepeating() ? $event->id().'-'.$start->format('Ymd') : $event->id(),
            'title' => \html_entity_decode($event->title(), ENT_QUOTES, 'UTF-8'),
            'url' => $event->url(),
            'start' => $start->format('c'),
            'end_time' => $end ? $end->format('c') : null,
            'time_override' => $event->timeOverride() ?: null,
            'venue' => $event->venue(),
            'presenter' => $event->presenter(),
            'excerpt' => self::excerpt($event),
            'poster' => self::poster($event),
        ];
    }

    /**
     * Manual excerpt, or the opening words of the description, for the calendar's hover card.
     */
    protected static function excerpt(Event $event): string
    {
        $text = \has_excerpt($event->post)
            ? \get_the_excerpt($event->post)
            : \wp_trim_words(\excerpt_remove_blocks(\strip_shortcodes($event->post->post_content)), 30);

        return trim(\html_entity_decode(\wp_strip_all_tags($text), ENT_QUOTES, 'UTF-8'));
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
