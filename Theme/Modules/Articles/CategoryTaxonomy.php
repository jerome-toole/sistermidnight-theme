<?php

namespace Theme\Modules\Articles;

class CategoryTaxonomy
{
    protected const SLUG = 'category';

    public static function init(): void
    {
        \add_filter('gust/templates/taxonomies', [__CLASS__, 'filterGustTemplatesTaxonomies']);
    }

    public static function filterGustTemplatesTaxonomies($taxonomies): array
    {
        $taxonomies[] = self::SLUG;

        return $taxonomies;
    }
}
