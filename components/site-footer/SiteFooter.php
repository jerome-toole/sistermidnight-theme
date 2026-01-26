<?php

namespace Geum\Components;

use Geum\ComponentBase;

/**
 * SiteFooter Component
 *
 * Usage:
 *   use Geum\Components\SiteFooter;
 *
 *   echo SiteFooter::make();
 */
class SiteFooter extends ComponentBase
{
    protected static string $name = 'site-footer';

    /**
     * Create a new SiteFooter component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $classes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Transform args before rendering.
     */
    protected static function transform(array $args): array
    {
        $args['site_name'] = \get_bloginfo('name');
        $args['footer_text_top'] = \get_field('footer_text_top', 'option');
        $args['footer_text_bottom'] = \get_field('footer_text_bottom', 'option');
        $args['footer_legal_text'] = \get_field('footer_legal_text', 'option');
        $args['radio_url'] = \get_field('radio_url', 'option');

        if (have_rows('footer_images', 'option')) {
            $args['content']['images'] = [];

            while (have_rows('footer_images', 'option')) {
                the_row();

                if (! empty(get_sub_field('image'))) {
                    $image = get_sub_field('image');
                    $image['size'] = 'medium';
                }

                if (! empty($image)) {
                    $link = get_sub_field('link');
                    $image_data = [
                        'image' => $image ?? null,
                        'link' => $link,
                    ];

                    // Pre-create link args for images with links
                    if (! empty($link)) {
                        $image_data['link_args'] = array_merge($link, [
                            'classes' => ['site-footer__image', 'img-fit'],
                            'content' => \Geum\Components\Image::make(...$image),
                            'content_filter' => false,
                        ]);
                    }

                    $args['content']['images'][] = $image_data;
                }
            }
        }

        return $args;
    }
}
