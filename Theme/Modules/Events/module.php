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
