<?php

namespace Theme\Modules\Core;

/**
 * Image output. Sub-sizes of uploaded JPEGs are generated as WebP (the
 * original stays a JPEG, so `full` is still a JPEG) and existing uploads are
 * converted with `wp media regenerate`. Browsers without WebP (none current)
 * fall back to the original through srcset's `full` candidate.
 */
class Images
{
    public static function init(): void
    {
        \add_filter('image_editor_output_format', [__CLASS__, 'outputWebp']);
    }

    /**
     * @link https://developer.wordpress.org/reference/hooks/image_editor_output_format/
     */
    public static function outputWebp(array $formats): array
    {
        $formats['image/jpeg'] = 'image/webp';

        return $formats;
    }
}
