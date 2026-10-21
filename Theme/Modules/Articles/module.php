<?php

namespace Theme\Modules\Articles;

class ArticlesModule
{
    public static function init(): void
    {
        PostType::init();
        CategoryTaxonomy::init();
        TagTaxonomy::init();

        \add_filter('acf/settings/load_json', [__CLASS__, 'loadACFJson']);
        \add_filter('document_title_parts', [__CLASS__, 'filterArchiveTitleParts']);
        \add_filter('wpseo_title', [__CLASS__, 'filterYoastArchiveTitle']);
        \add_filter('wpseo_opengraph_title', [__CLASS__, 'filterYoastArchiveSocialTitle']);
        \add_filter('wpseo_twitter_title', [__CLASS__, 'filterYoastArchiveSocialTitle']);
    }

    public const ARCHIVE_TITLE = 'News';

    /** The article archive is "News", not "News Archive" (core/Yoast document title). */
    public static function filterArchiveTitleParts(array $parts): array
    {
        if (\is_post_type_archive(PostType::SLUG)) {
            $parts['title'] = __(self::ARCHIVE_TITLE, 'gust');
        }

        return $parts;
    }

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

    public static function loadACFJson(array $paths): array
    {
        $paths[] = __DIR__.'/acf-json';

        return $paths;
    }
}
