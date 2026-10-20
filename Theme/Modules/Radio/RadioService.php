<?php

namespace Theme\Modules\Radio;

use WP_REST_Response;

/**
 * Radio.co now-playing for the radio bar, fetched server-side and cached.
 *
 * One transient serves both the server render and the REST route. The status
 * endpoint is checked first; the track endpoint is only called while the
 * station is online, so an offline station costs one request per TTL.
 */
class RadioService
{
    public const DEFAULT_STATION = 's35e4926a1';

    protected const CACHE_TTL = 30;

    protected const TIMEOUT = 2;

    protected const REST_NAMESPACE = 'sm/v1';

    /**
     * @return array{online: bool, title: string, artist: string, listen_url: string}
     */
    public static function nowPlaying(): array
    {
        $station = self::stationId();
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

    public static function stationId(): string
    {
        $station = \function_exists('get_field') ? (string) \get_field('station_id', 'option') : '';
        $station = \sanitize_key($station);

        return $station !== '' ? $station : self::DEFAULT_STATION;
    }

    public static function listenUrl(string $station): string
    {
        $url = \function_exists('get_field') ? (string) \get_field('listen_url', 'option') : '';

        return $url !== '' ? $url : 'https://stream.radio.co/'.$station.'/listen';
    }

    /**
     * @return array{online: bool, title: string, artist: string, listen_url: string}
     */
    protected static function fetch(string $station): array
    {
        $result = [
            'online' => false,
            'title' => '',
            'artist' => '',
            'listen_url' => self::listenUrl($station),
        ];

        if (! self::isEnabled()) {
            return $result;
        }

        $status = self::getJson('https://public.radio.co/stations/'.$station.'/status');
        if (($status['status'] ?? '') !== 'online') {
            return $result;
        }

        $result['online'] = true;

        $track = self::getJson('https://public.radio.co/api/v2/'.$station.'/track/current')['data'] ?? [];
        $result['title'] = trim((string) ($track['track_title'] ?? ''));
        $result['artist'] = trim((string) ($track['track_artist'] ?? ''));

        // Fall back to the status payload's "Artist - Title" string.
        if ($result['title'] === '') {
            $combined = trim((string) ($track['title'] ?? $status['current_track']['title'] ?? ''));
            $parts = explode(' - ', $combined, 2);
            [$result['artist'], $result['title']] = count($parts) === 2 ? [trim($parts[0]), trim($parts[1])] : ['', $combined];
        }

        return $result;
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
