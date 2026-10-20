<?php

namespace Gust\Components;

use Gust\ComponentBase;

/**
 * CoreBlocks Component
 *
 * Owns the core block styles (registered in scripts/editor/*.js, styled in
 * styles/*.pcss with editor parity in _editor-styles.pcss) and the editor
 * palette, which is limited to the theme-config colours flagged `block_editor`.
 *
 * Usage:
 *   use Gust\Components\CoreBlocks;
 *
 *   echo CoreBlocks::make();
 */
class CoreBlocks extends ComponentBase
{
    protected static string $name = 'core-blocks';

    /**
     * Create a new CoreBlocks component.
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
     * `wp_theme_json_data_theme` filter (after Gust injects the full palette):
     * keep only the colours flagged `block_editor` in theme-config.json so
     * editors pick from the design's backgrounds, not every token.
     */
    public static function limitEditorPalette(\WP_Theme_JSON_Data $theme_json): \WP_Theme_JSON_Data
    {
        $base = \Gust\WordPress\Colors::getConfig()['colors']['base'] ?? [];
        $palette = $theme_json->get_data()['settings']['color']['palette']['theme'] ?? [];

        if (empty($palette)) {
            return $theme_json;
        }

        $allowed = array_values(array_filter(
            $palette,
            fn (array $color): bool => ! empty($base[$color['slug']]['block_editor'])
        ));

        return $theme_json->update_with([
            'version' => 3,
            'settings' => [
                'color' => [
                    'palette' => $allowed,
                ],
            ],
        ]);
    }
}
