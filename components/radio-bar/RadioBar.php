<?php

namespace Gust\Components;

use Gust\ComponentBase;
use Theme\Modules\Radio\RadioService;

/**
 * Radio Bar Component
 *
 * 64px band on burnt-sienna under the front-page Hero: SMFM marque and
 * wordmark left, "Now playing: {track}" centre-right, "Listen" link right
 * (opens the stream in a new tab). Data comes from RadioService::nowPlaying()
 * (server-side, cached 30s). scripts.js re-fetches `sm/v1/now-playing` every
 * 60s and swaps the track text (aria-live="polite").
 *
 * Renders nothing when `radio_enabled` is off. When the station is offline the
 * bar still shows (brand + Listen) and the track line is hidden, so the
 * refresh can reveal it once the station comes back.
 *
 * Usage:
 *   use Gust\Components\RadioBar;
 *
 *   echo RadioBar::fromService();                                               // live data
 *   echo RadioBar::make(online: true, title: 'Moonbeam Levels', artist: 'Amar Patel');
 */
class RadioBar extends ComponentBase
{
    protected static string $name = 'radio-bar';

    protected const REFRESH_INTERVAL = 60;

    /**
     * Create a new RadioBar component.
     *
     * @param  bool  $online  Whether the station is broadcasting.
     * @param  string  $title  Current track or show title.
     * @param  string  $artist  Current artist, if separate from the title.
     * @param  string|null  $listen_url  Stream URL; defaults to RadioService::listenUrl().
     * @param  bool|null  $enabled  Overrides the `radio_enabled` option (for previews).
     * @param  bool  $refresh  Poll the now-playing endpoint from the browser.
     * @return static|null Returns null when the radio is disabled.
     */
    public static function make(
        bool $online = false,
        string $title = '',
        string $artist = '',
        ?string $listen_url = null,
        ?bool $enabled = null,
        bool $refresh = true,
        array $classes = [],
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Build from the live RadioService data.
     */
    public static function fromService(array $classes = [], array $attributes = []): ?static
    {
        if (! RadioService::isEnabled()) {
            return null;
        }

        $data = RadioService::nowPlaying();

        return static::make(
            online: (bool) $data['online'],
            title: (string) $data['title'],
            artist: (string) $data['artist'],
            listen_url: (string) $data['listen_url'],
            classes: $classes,
            attributes: $attributes,
        );
    }

    /**
     * "Artist – Title", "Title", or "Live now" when online with no track.
     * Mirrored in scripts.js.
     */
    public static function trackLabel(string $title, string $artist): string
    {
        $parts = array_filter([trim($artist), trim($title)]);

        return $parts ? implode(' – ', $parts) : __('Live now', 'gust');
    }

    protected static function validate(array $args): bool
    {
        return $args['enabled'] ?? RadioService::isEnabled();
    }

    protected static function transform(array $args): array
    {
        $args['classes'] ??= [];
        $args['attributes'] ??= [];

        $args['listen_url'] = $args['listen_url'] ?: RadioService::listenUrl(RadioService::stationId());
        $args['track'] = static::trackLabel($args['title'], $args['artist']);

        $args['classes'] = array_merge(['radio-bar', 'color-context-burnt-sienna'], $args['classes']);
        $args['attributes']['data-radio-bar'] = true;

        if ($args['refresh']) {
            $args['attributes']['data-radio-bar-endpoint'] = \rest_url('sm/v1/now-playing');
            $args['attributes']['data-radio-bar-interval'] = self::REFRESH_INTERVAL;
        }

        return $args;
    }
}
