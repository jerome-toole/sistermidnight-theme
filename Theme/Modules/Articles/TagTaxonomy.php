<?php

namespace Theme\Modules\Articles;

class TagTaxonomy
{
    protected const SLUG = 'post_tag';

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
