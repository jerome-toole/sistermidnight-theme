<?php

namespace Gust\Components;

use Gust\ComponentBase;

/**
 * Image Pair Component (ACF block `theme/image-pair`)
 *
 * Two portrait images (599×826 in the design) side by side on a full-width
 * padded colour context. Stacked below md.
 *
 * Usage:
 *   use Gust\Components\ImagePair;
 *
 *   echo ImagePair::make(images: [12, 13]);
 *   echo ImagePair::make(images: $acf_gallery, background: 'stone-beige');
 */
class ImagePair extends ComponentBase
{
    protected static string $name = 'image-pair';

    public const BACKGROUNDS = ['dark-umber', 'stone-beige'];

    protected static function getDefaults(): array
    {
        return [
            'images' => [],
            'background' => 'dark-umber',
        ];
    }

    /**
     * Create a new Image Pair component.
     *
     * @param  array  $images  ACF gallery value (image arrays) or attachment IDs; only the first two are used.
     * @param  string|null  $background  One of self::BACKGROUNDS.
     * @return static|null Null when there are no images (outside the editor).
     */
    public static function make(
        array $images = [],
        ?string $background = null,
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

        if (! in_array($args['background'] ?? null, self::BACKGROUNDS, true)) {
            $args['background'] = self::BACKGROUNDS[0];
        }

        $args['images'] = static::images($args['images'] ?? []);
        $args['attributes'] ??= [];
        $args['classes'] = array_merge([
            'image-pair',
            'alignfull',
            'has-background',
            'has-'.$args['background'].'-background-color',
        ], $args['classes'] ?? []);

        return $args;
    }

    /** Normalise up to two images (ACF gallery arrays or IDs) to rendered Image components. */
    protected static function images(mixed $images): array
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

            $clean[] = Image::make(
                id: $id,
                size: 'large',
                sizes: '(min-width: 768px) 50vw, 100vw',
                attributes: ['class' => 'image-pair__img'],
            );

            if (count($clean) === 2) {
                break;
            }
        }

        return $clean;
    }

    /** Block fields are prefixed (image_pair_images…) so their names stay unique for inline editing. */
    protected static function fromBlockFields(array $args): array
    {
        foreach (['images', 'background'] as $field) {
            if (array_key_exists("image_pair_{$field}", $args)) {
                $args[$field] = $args["image_pair_{$field}"] ?? $args[$field] ?? null;
                unset($args["image_pair_{$field}"]);
            }
        }

        return $args;
    }

    /** ACF inline toolbar attributes for the images field. Empty outside an editor preview. */
    public function inlineToolbar(): string
    {
        if (! $this->is_preview || ! \function_exists('acf_inline_toolbar_editing_attrs')) {
            return '';
        }

        return (string) \acf_inline_toolbar_editing_attrs(['image_pair_images'], ['toolbar_title' => __('Images', 'gust')]);
    }
}
