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
     * What's On: the next three events as featured cards, then the calendar.
     * The page header comes from the router page (index.php).
     */
    public static function renderArchive(): string
    {
        return UpcomingEvents::make(label: '', variant: 'featured', count: 3)
            .EventsCalendar::make();
    }

    public static function loadACFJson(array $paths): array
    {
        $paths[] = __DIR__.'/acf-json';

        return $paths;
    }
}
