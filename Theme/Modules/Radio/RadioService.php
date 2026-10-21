<?php

namespace Theme\Modules\Radio;

use WP_REST_Response;

/**
 * Radiocult now-playing for the radio bar, fetched server-side and cached.
 *
 * One request: `GET api.radiocult.fm/api/station/{slug}/schedule/live` (no
 * auth). `result.status` is `defaultPlaylist` while off air (`offAir` for an
 * unknown station), otherwise a live show; `result.content.name` is the show name and `result.metadata`
 * title/artist the current track (" - " / null when there is none).
 *
 * One transient serves both the server render and the REST route. Any HTTP
 * error, timeout or malformed body reads as off air, so the bar never breaks
 * the page.
 */
class RadioService
{
    public const DEFAULT_STATION = 'sister-midnight-fm';

    public const DEFAULT_SITE_URL = 'https://radio.sistermidnight.org/';

    protected const CACHE_TTL = 30;

    protected const TIMEOUT = 2;

    protected const REST_NAMESPACE = 'sm/v1';

    /**
     * @return array{online: bool, show: string, title: string, artist: string}
     */
    public static function nowPlaying(): array
    {
        $station = self::stationSlug();
        $key = 'sm_radio_now_playing_'.$station;
        $data = \get_transient($key);

        if (! is_array($data)) {
            $data = self::fetch($station);
            \set_transient($key, $data, self::CACHE_TTL);
        }

        return $data;
    }

    public static function registerRoute(): void
    {
        \register_rest_route(self::REST_NAMESPACE, '/now-playing', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'handleRequest'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function handleRequest(): WP_REST_Response
    {
        $response = new WP_REST_Response(self::nowPlaying());
        $response->header('Cache-Control', 'public, max-age='.self::CACHE_TTL);

        return $response;
    }

    /**
     * The `radio_enabled` option (true when ACF is unavailable).
     */
    public static function isEnabled(): bool
    {
        return ! \function_exists('get_field') || \get_field('radio_enabled', 'option') !== false;
    }

    /**
     * The Radiocult station slug (`station_slug` option).
     */
    public static function stationSlug(): string
    {
        $station = \function_exists('get_field') ? (string) \get_field('station_slug', 'option') : '';
        $station = \sanitize_title($station);

        return $station !== '' ? $station : self::DEFAULT_STATION;
    }

    /**
     * The SMFM website (`radio_url` option): where "Radio" and "Listen" links go.
     */
    public static function siteUrl(): string
    {
        $url = \function_exists('get_field') ? (string) \get_field('radio_url', 'option') : '';

        return $url !== '' ? $url : self::DEFAULT_SITE_URL;
    }

    /**
     * @return array{online: bool, show: string, title: string, artist: string}
     */
    protected static function fetch(string $station): array
    {
        $result = [
            'online' => false,
            'show' => '',
            'title' => '',
            'artist' => '',
        ];

        if (! self::isEnabled()) {
            return $result;
        }

        $live = self::getJson('https://api.radiocult.fm/api/station/'.$station.'/schedule/live')['result'] ?? [];
        $status = (string) ($live['status'] ?? '');

        // "offAir" (with a plain-string content) is what an unknown station returns.
        if (in_array($status, ['', 'defaultPlaylist', 'offAir'], true) || ! is_array($live['content'] ?? null)) {
            return $result;
        }

        $result['online'] = true;
        $result['show'] = trim((string) ($live['content']['name'] ?? ''));
        $result['title'] = self::meaningful($live['metadata']['title'] ?? null);
        $result['artist'] = self::meaningful($live['metadata']['artist'] ?? null);

        return $result;
    }

    /**
     * Radiocult sends " - " / null for an unknown track: treat those as empty.
     */
    protected static function meaningful(mixed $value): string
    {
        $value = is_string($value) ? trim($value) : '';

        return in_array($value, ['', '-'], true) ? '' : $value;
    }

    /**
     * @return array<string,mixed>
     */
    protected static function getJson(string $url): array
    {
        try {
            $response = \wp_remote_get($url, ['timeout' => self::TIMEOUT]);

            if (\is_wp_error($response) || \wp_remote_retrieve_response_code($response) !== 200) {
                return [];
            }

            $data = \json_decode(\wp_remote_retrieve_body($response), true);

            return is_array($data) ? $data : [];
        } catch (\Throwable) {
            return [];
        }
    }
}
