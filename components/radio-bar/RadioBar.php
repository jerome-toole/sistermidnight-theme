<?php

namespace Gust\Components;

use Gust\ComponentBase;
use Theme\Modules\Radio\RadioService;

/**
 * Radio Bar Component
 *
 * 64px band on burnt-sienna under the front-page Hero: SMFM marque and
 * wordmark left, "Now playing: {show}" centre-right, "Listen" link right
 * (the SMFM site, in a new tab). Data comes from RadioService::nowPlaying()
 * (Radiocult, server-side, cached 30s). scripts.js re-fetches
 * `sm/v1/now-playing` every 60s and swaps the text (aria-live="polite").
 *
 * Renders nothing when `radio_enabled` is off. While the station is off air
 * the line reads "Off air" (no "Now playing:" label) and Listen stays.
 *
 * Usage:
 *   use Gust\Components\RadioBar;
 *
 *   echo RadioBar::fromService();                                                  // live data
 *   echo RadioBar::make(online: true, show: 'Moonbeam Levels', artist: 'Amar Patel', title: 'Dusk');
 */
class RadioBar extends ComponentBase
{
    protected static string $name = 'radio-bar';

    protected const REFRESH_INTERVAL = 60;

    /**
     * Create a new RadioBar component.
     *
     * @param  bool  $online  Whether a show is live (not the default playlist).
     * @param  string  $show  Current show or playlist name.
     * @param  string  $title  Current track title, if known.
     * @param  string  $artist  Current track artist, if known.
     * @param  string|null  $listen_url  Where "Listen" goes; defaults to RadioService::siteUrl() (the SMFM site).
     * @param  bool|null  $enabled  Overrides the `radio_enabled` option (for previews).
     * @param  bool  $refresh  Poll the now-playing endpoint from the browser.
     * @return static|null Returns null when the radio is disabled.
     */
    public static function make(
        bool $online = false,
        string $show = '',
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
            show: (string) $data['show'],
            title: (string) $data['title'],
            artist: (string) $data['artist'],
            classes: $classes,
            attributes: $attributes,
        );
    }

    /**
     * "Show – Artist – Title" from whichever parts are known; "Live now" when
     * online with nothing known; "Off air" when offline. Mirrored in scripts.js.
     */
    public static function trackLabel(bool $online, string $show, string $title, string $artist): string
    {
        if (! $online) {
            return __('Off air', 'gust');
        }

        $parts = array_filter(array_map('trim', [$show, $artist, $title]));

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

        $args['listen_url'] = $args['listen_url'] ?: RadioService::siteUrl();
        $args['track'] = static::trackLabel($args['online'], $args['show'], $args['title'], $args['artist']);

        $args['classes'] = array_merge(['radio-bar', 'color-context-burnt-sienna'], $args['classes']);
        $args['attributes']['data-radio-bar'] = true;

        if ($args['refresh']) {
            $args['attributes']['data-radio-bar-endpoint'] = \rest_url('sm/v1/now-playing');
            $args['attributes']['data-radio-bar-interval'] = self::REFRESH_INTERVAL;
        }

        return $args;
    }
}
