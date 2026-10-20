<?php

namespace Gust\Components;

use Gust\ComponentBase;

/**
 * Gallery Component (ACF block `theme/gallery`)
 *
 * A vertical gutter label and a set of images, in one of two block styles:
 *
 *   masonry   (default) dense CSS grid, 360px rows; landscape images span
 *             two columns. Two equal columns below md.
 *   carousel  horizontal native scroller 420px tall, natural widths, with
 *             prev/next arrows. Blossom carousel is loaded by scripts.js only
 *             when a carousel is on the page.
 *
 * The style comes from the block's `is-style-carousel` class, or the `style`
 * argument when the component is used directly.
 *
 * Usage:
 *   use Gust\Components\Gallery;
 *
 *   echo Gallery::make(label: 'Gallery', images: [12, 13, 14]);
 *   echo Gallery::make(label: 'Slider', images: $acf_gallery, style: 'carousel');
 */
class Gallery extends ComponentBase
{
    protected static string $name = 'gallery';

    public const STYLES = ['masonry', 'carousel'];

    protected static function getDefaults(): array
    {
        return [
            'label' => '',
            'images' => [],
            'style' => 'masonry',
        ];
    }

    /**
     * Create a new Gallery component.
     *
     * @param  string  $label  Vertical gutter label, rendered as the <h2>. Optional.
     * @param  array  $images  ACF gallery value (image arrays) or attachment IDs.
     * @param  string|null  $style  One of self::STYLES. The block's `is-style-*` class wins.
     * @return static|null Null when there are no images (outside the editor).
     */
    public static function make(
        string $label = '',
        array $images = [],
        ?string $style = null,
        bool $is_preview = false,
        array $classes = [],
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function validate(array $args): bool
    {
        $args = static::fromBlockFields($args);

        return ! empty($args['is_preview']) || static::images($args['images'] ?? []) !== [];
    }

    protected static function transform(array $args): array
    {
        $args = static::fromBlockFields($args);

        $args['label'] = trim((string) ($args['label'] ?? ''));
        $args['attributes'] ??= [];
        $classes = $args['classes'] ?? [];

        // Block style: the editor adds is-style-carousel; the default style has no class.
        $style = in_array('is-style-carousel', $classes, true) ? 'carousel' : ($args['style'] ?? 'masonry');

        if (! in_array($style, self::STYLES, true)) {
            $style = self::STYLES[0];
        }

        $args['style'] = $style;
        $args['images'] = static::images($args['images'] ?? [], $style);
        $args['classes'] = array_merge(['gallery', 'gallery--'.$style, 'alignfull'], $classes);

        if ($args['label'] !== '' || ! empty($args['is_preview'])) {
            $args['classes'][] = 'gallery--labelled';
        }

        return $args;
    }

    /**
     * Normalise the images to rendered Image components.
     *
     * Accepts the ACF gallery return formats (arrays with ID/id, or IDs). In
     * the masonry style landscape images span two columns.
     */
    protected static function images(mixed $images, string $style = 'masonry'): array
    {
        if (! is_array($images)) {
            return [];
        }

        $clean = [];

        foreach ($images as $image) {
            $id = is_array($image) ? (int) ($image['ID'] ?? $image['id'] ?? 0) : (int) $image;

            if ($id <= 0 || ! \wp_attachment_is_image($id)) {
                continue;
            }

            $meta = \wp_get_attachment_metadata($id) ?: [];
            $landscape = ! empty($meta['width']) && ! empty($meta['height']) && $meta['width'] > $meta['height'];

            $clean[] = [
                'id' => $id,
                'landscape' => $landscape,
                'image' => Image::make(
                    id: $id,
                    size: 'large',
                    sizes: $style === 'carousel' ? '(min-width: 768px) 50vw, 100vw' : '(min-width: 768px) 25vw, 50vw',
                    attributes: ['class' => 'gallery__img'],
                ),
            ];
        }

        return $clean;
    }

    /** Block fields are prefixed (gallery_label…) so their names stay unique for inline editing. */
    protected static function fromBlockFields(array $args): array
    {
        foreach (['label', 'images'] as $field) {
            if (array_key_exists("gallery_{$field}", $args)) {
                $args[$field] = $args["gallery_{$field}"] ?? $args[$field] ?? null;
                unset($args["gallery_{$field}"]);
            }
        }

        return $args;
    }

    /** ACF inline text editing attributes for a block field (name without the gallery_ prefix). Empty outside an editor preview. */
    public function inlineText(string $field, string $placeholder): string
    {
        if (! $this->is_preview || ! \function_exists('acf_inline_text_editing_attrs')) {
            return '';
        }

        return \acf_inline_text_editing_attrs("gallery_{$field}", ['placeholder' => $placeholder]);
    }

    /** ACF inline toolbar attributes for a set of block fields (names without the gallery_ prefix). */
    public function inlineToolbar(array $fields, string $title): string
    {
        if (! $this->is_preview || ! \function_exists('acf_inline_toolbar_editing_attrs')) {
            return '';
        }

        $fields = array_map(fn (string $field) => "gallery_{$field}", $fields);

        return (string) \acf_inline_toolbar_editing_attrs($fields, ['toolbar_title' => $title]);
    }
}
