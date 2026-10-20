<?php

namespace Gust\Components;

use Gust\ComponentBase;
use Gust\Router;

/**
 * PageHeader Component
 *
 * Internal page header: the page title as a vertical label in the left
 * gutter (the page <h1>; rotation is CSS only), an optional illustration or
 * image in the left half and the intro copy in the right half. Below md the
 * label sits horizontally above and the media follows the intro.
 *
 * Usage:
 *   use Gust\Components\PageHeader;
 *
 *   echo PageHeader::fromObject($object);                       // templates: page, term, search, 404…
 *   echo PageHeader::fromPage($post);                           // a page's own fields
 *   echo PageHeader::make(label: 'About us', intro: '<p>…</p>', media: 'stars');
 *   echo PageHeader::make(label: 'Venue', media: ['ID' => 12]); // image array or attachment ID
 */
class PageHeader extends ComponentBase
{
    protected static string $name = 'page-header';

    /**
     * Create a new PageHeader component.
     *
     * @param  string  $label  Page title, rendered as the <h1>.
     * @param  string  $intro  Intro copy (HTML).
     * @param  string|int|array|null  $media  Illustration name, image array / attachment ID, or null.
     * @return static|null Returns null without a label.
     */
    public static function make(
        string $label = '',
        string $intro = '',
        string|int|array|null $media = null,
        array $classes = [],
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Build from a page's own fields (intro_text, header_media,
     * header_illustration, header_image). Works for any WP_Post; posts
     * without the fields just get their title. `$intro` is the fallback
     * when the page has no intro_text.
     */
    public static function fromPage(?\WP_Post $post, string $intro = '', array $classes = [], array $attributes = []): ?static
    {
        if (! $post) {
            return null;
        }

        $media = null;

        if (\function_exists('get_field')) {
            $intro = (string) \get_field('intro_text', $post->ID) ?: $intro;
            $type = (string) \get_field('header_media', $post->ID);

            if ($type === 'image') {
                $media = \get_field('header_image', $post->ID) ?: null;
            } elseif ($type === 'illustration') {
                $media = (string) \get_field('header_illustration', $post->ID) ?: null;
            }
        }

        return static::make(
            label: $post->post_title,
            intro: $intro,
            media: $media,
            classes: $classes,
            attributes: $attributes,
        );
    }

    /**
     * Build for the current request from the queried object. Routes with a
     * backing router page (search, 404, archives) use that page's fields.
     */
    public static function fromObject(?object $object = null, array $classes = [], array $attributes = []): ?static
    {
        $object ??= \Gust\WordPress\PageObject::get();

        if ($object instanceof \WP_Post) {
            return static::fromPage($object, classes: $classes, attributes: $attributes);
        }

        $label = '';
        $intro = '';

        if ($object instanceof \WP_Term) {
            $label = $object->name;
            $intro = (string) \term_description($object);
        } elseif ($object instanceof \WP_Post_Type) {
            $label = $object->label;
        } elseif ($object instanceof \WP_User) {
            $label = sprintf(__('Posts by %s', 'gust'), $object->display_name);
        } elseif ($object instanceof \WP_Query && $object->is_404()) {
            $label = __('404', 'gust');
            $intro = '<p>'.esc_html__('The page you are looking for could not be found.', 'gust').'</p>';
        } elseif ($object instanceof \WP_Query && $object->is_search()) {
            $label = __('Search', 'gust');
        }

        if (\is_search() && ($query = \get_search_query())) {
            $intro = '<p>'.sprintf(esc_html__("Showing results for '%s'", 'gust'), esc_html($query)).'</p>';
        }

        if ($page = Router::getPage()) {
            return static::fromPage($page, $intro, $classes, $attributes);
        }

        return static::make(label: $label, intro: $intro, classes: $classes, attributes: $attributes);
    }

    protected static function validate(array $args): bool
    {
        return trim($args['label'] ?? '') !== '';
    }

    protected static function transform(array $args): array
    {
        $args['label'] = trim($args['label']);
        $args['intro'] = trim($args['intro'] ?? '');
        $args['classes'] = array_merge(['page-header', 'alignfull'], $args['classes'] ?? []);
        $args['attributes'] ??= [];

        $media = $args['media'] ?? null;
        $args['illustration'] = '';
        $args['image'] = null;

        if (is_string($media) && in_array($media, Illustration::NAMES, true)) {
            $args['illustration'] = $media;
            $args['classes'][] = 'page-header--illustration';
        } elseif (is_array($media) || is_int($media)) {
            $id = is_array($media) ? (int) ($media['ID'] ?? $media['id'] ?? 0) : $media;

            if ($id > 0 && \wp_attachment_is_image($id)) {
                $args['image'] = Image::make(
                    id: $id,
                    size: 'large',
                    sizes: '(min-width: 768px) 410px, 100vw',
                    classes: ['page-header__img'],
                );
                $args['classes'][] = 'page-header--image';
            }
        }

        unset($args['media']);

        return $args;
    }
}
