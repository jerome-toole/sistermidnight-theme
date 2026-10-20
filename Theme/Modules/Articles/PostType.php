<?php

namespace Theme\Modules\Articles;

class PostType
{
    protected const SLUG = 'article';

    public static function init(): void
    {
        \add_action('init', [__CLASS__, 'register']);
        \add_filter('gust/templates/post-types', [__CLASS__, 'filterGustTemplatesPostTypes']);
    }

    public static function register(): void
    {
        if (! function_exists('register_extended_post_type')) {
            return;
        }

        \register_extended_post_type(self::SLUG, [
            'public' => true,
            'has_archive' => true,
            'hierarchical' => false,
            'show_in_rest' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-media-document',
            'enter_title_here' => 'Article Title',
            'supports' => [
                'title',
                'editor',
                'excerpt',
                'revisions',
                'thumbnail',
                'author',
                'custom-fields',
            ],
            'taxonomies' => [
                'category',
                'post_tag',
            ],
            'template' => [
                [
                    'core/paragraph',
                    ['placeholder' => 'Add content...'],
                ],
            ],
            'admin_filters' => [
                'category' => [
                    'taxonomy' => 'category',
                ],
            ],
            'admin_cols' => [
                'thumbnail' => [
                    'title' => 'Thumbnail',
                    'featured_image' => 'thumbnail',
                    'width' => 80,
                    'height' => 80,
                ],
                'title' => [
                    'title' => 'Title',
                ],
                'author' => [
                    'title' => 'Author',
                ],
                'category' => [
                    'taxonomy' => 'category',
                ],
                'updated' => [
                    'title' => 'Updated',
                    'post_field' => 'post_modified',
                    'date_format' => 'Y/m/d \a\t H:i a',
                ],
            ],
        ], [
            'singular' => __('Article', 'gust'),
            'plural' => __('Articles', 'gust'),
            'slug' => 'articles',
        ]);
    }

    public static function filterGustTemplatesPostTypes($postTypes)
    {
        $postTypes[] = self::SLUG;

        return $postTypes;
    }
}
