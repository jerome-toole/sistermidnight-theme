<?php

namespace Theme\Modules\Events;

use Gust\Components\EventsCalendar;
use Gust\Components\UpcomingEvents;

class EventsModule
{
    public static function init(): void
    {
        PostType::init();
        LocationTaxonomy::init();
        Feed::init();

        \add_filter('acf/settings/load_json', [__CLASS__, 'loadACFJson']);
        \add_filter('document_title_parts', [__CLASS__, 'filterArchiveTitleParts']);
        \add_filter('wpseo_title', [__CLASS__, 'filterYoastArchiveTitle']);
        \add_filter('wpseo_opengraph_title', [__CLASS__, 'filterYoastArchiveSocialTitle']);
        \add_filter('wpseo_twitter_title', [__CLASS__, 'filterYoastArchiveSocialTitle']);
    }

    public const ARCHIVE_TITLE = "What's On";

    /** The event archive is "What's On", not "Events Archive" (core document title). */
    public static function filterArchiveTitleParts(array $parts): array
    {
        if (\is_post_type_archive(PostType::SLUG)) {
            $parts['title'] = __(self::ARCHIVE_TITLE, 'gust');
        }

        return $parts;
    }

    /** Yoast replaces the document title with its "%%pt_plural%% Archive" template; use the same name there. */
    public static function filterYoastArchiveTitle(string $title): string
    {
        if (! \is_post_type_archive(PostType::SLUG)) {
            return $title;
        }

        $separator = \function_exists('YoastSEO') ? YoastSEO()->helpers->options->get_title_separator() : '-';

        return __(self::ARCHIVE_TITLE, 'gust').' '.$separator.' '.\get_bloginfo('name');
    }

    public static function filterYoastArchiveSocialTitle(string $title): string
    {
        return \is_post_type_archive(PostType::SLUG) ? __(self::ARCHIVE_TITLE, 'gust') : $title;
    }

    /**
     * What's On: the vertical "What's On" label (the page <h1>) beside the
     * next three events as featured cards, then the calendar. index.php skips
     * the Page Header for this archive.
     */
    public static function renderArchive(): string
    {
        return UpcomingEvents::make(label: __("What's On", 'gust'), label_level: 1, variant: 'featured', count: 3, eager_first: true)
            .EventsCalendar::make();
    }

    public static function loadACFJson(array $paths): array
    {
        $paths[] = __DIR__.'/acf-json';

        return $paths;
    }
}
