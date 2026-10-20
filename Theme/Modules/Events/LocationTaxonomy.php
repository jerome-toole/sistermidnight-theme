<?php

namespace Theme\Modules\Events;

class LocationTaxonomy
{
    protected const SLUG = 'location';

    public static function init(): void
    {
        \add_action('init', [__CLASS__, 'register']);
        \add_filter('gust/templates/taxonomies', [__CLASS__, 'filterGustTemplatesTaxonomies']);
    }

    public static function register(): void
    {
        if (! function_exists('register_extended_taxonomy')) {
            return;
        }

        \register_extended_taxonomy(
            self::SLUG,
            [
                'event',
            ],
            [
                'hierarchical' => true,
                'show_admin_column' => true,
                'show_in_rest' => true,
                'meta_box' => 'simple',
                'exclusive' => true,
                'required' => true,
                'dashboard_glance' => true,
            ],
            [
                'singular' => __('Location', 'gust'),
                'plural' => __('Locations', 'gust'),
                'slug' => self::SLUG,
            ]
        );
    }

    public static function filterGustTemplatesTaxonomies($taxonomies): array
    {
        $taxonomies[] = self::SLUG;

        return $taxonomies;
    }
}
