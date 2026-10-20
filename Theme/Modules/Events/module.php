<?php

namespace Theme\Modules\Events;

use Gust\Components\Cards;
use Gust\Components\NoContent;
use Gust\Components\Pagination;
use Gust\Components\TaxonomyFilters;
use Gust\WordPress\PageObject;

class EventsModule
{
    public static function init(): void
    {
        PostType::init();
        LocationTaxonomy::init();

        \add_filter('acf/settings/load_json', [__CLASS__, 'loadACFJson']);
    }

    public static function renderArchive(): string
    {
        $object = PageObject::get();

        $items = [];
        while (\have_posts()) {
            \the_post();
            $items[]['object'] = \get_post();
        }

        \ob_start();

        if (! empty($items)) {
            echo TaxonomyFilters::make(object: $object);
            echo Cards::make(items: $items);
            echo Pagination::make();
        } else {
            echo NoContent::make(object: $object);
        }

        return \ob_get_clean();
    }

    public static function loadACFJson(array $paths): array
    {
        $paths[] = __DIR__.'/acf-json';

        return $paths;
    }
}
