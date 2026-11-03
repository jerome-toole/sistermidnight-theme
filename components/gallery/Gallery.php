<?php

namespace Gust\Components;

use Gust\ComponentBase;

/**
 * Gallery Component (ACF block `theme/gallery`)
 *
 * A vertical gutter label and a set of images, in one of two block styles:
 *
 *   masonry   (default) justified rows: a fixed row height (360 at 1440),
 *             each image grows by its aspect ratio so every row, the last
 *             included, is filled edge to edge with mixed widths.
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

        if ($style === 'masonry') {
            $args['images'] = static::markRowEnds($args['images']);
        }
        $args['classes'] = array_merge(['gallery', 'gallery--'.$style, 'alignfull', 'has-background'], $classes);

        if ($args['label'] !== '' || ! empty($args['is_preview'])) {
            $args['classes'][] = 'gallery--labelled';
        }

        return $args;
    }

    /**
     * Normalise the images to rendered Image components.
     *
     * Accepts the ACF gallery return formats (arrays with ID/id, or IDs).
     * `ratio` (width / height) drives the masonry row layout.
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
            $ratio = ! empty($meta['width']) && ! empty($meta['height']) ? $meta['width'] / $meta['height'] : 1;

            $clean[] = [
                'id' => $id,
                'ratio' => round($ratio, 4),
                'image' => Image::make(
                    id: $id,
                    size: 'large',
                    sizes: $style === 'carousel' ? '(min-width: 768px) 50vw, 100vw' : '(min-width: 768px) 40vw, 70vw',
                    attributes: ['class' => 'gallery__img'],
                ),
            ];
        }

        return $clean;
    }

    /** Masonry row geometry at the design width (1440): row width, row height, gap. */
    protected const ROW_WIDTH = 1296;

    protected const ROW_HEIGHT = 360;

    protected const ROW_GAP = 12;

    /**
     * Split the images into justified rows and flag the last item of each.
     *
     * Every row is stretched to the full width at a fixed height, so each
     * image is scaled away from its natural width by the row's fill factor
     * (and cover-cropped). A small dynamic programme picks the breaks that
     * keep that factor closest to 1 summed over the rows, the last row
     * included, so no row is left with a couple of over-stretched images.
     */
    protected static function markRowEnds(array $images): array
    {
        $count = count($images);

        if ($count === 0) {
            return $images;
        }

        // $best[$i] = [cost, start of last row] for the first $i images.
        $best = [0 => [0.0, 0]];

        for ($end = 1; $end <= $count; $end++) {
            $best[$end] = [INF, 0];
            $ratios = 0.0;

            for ($start = $end - 1; $start >= 0; $start--) {
                $ratios += $images[$start]['ratio'];
                $fill = (self::ROW_WIDTH - ($end - $start - 1) * self::ROW_GAP) / ($ratios * self::ROW_HEIGHT);

                if ($fill <= 0.4) {
                    break;
                }

                $cost = $best[$start][0] + log($fill) ** 2;

                if ($cost < $best[$end][0]) {
                    $best[$end] = [$cost, $start];
                }
            }
        }

        for ($end = $count; $end > 0; $end = $best[$end][1]) {
            $images[$end - 1]['row_end'] = true;
        }

        return $images;
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
