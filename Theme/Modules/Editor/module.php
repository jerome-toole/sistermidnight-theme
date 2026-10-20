<?php

namespace Theme\Modules\Editor;

use Gust\Components\CoreBlocks;

/**
 * Block editor set-up owned by the theme: the editor palette is limited to the
 * `block_editor` colours after Gust has injected the full palette (priority 10).
 */
class EditorModule
{
    public static function init(): void
    {
        \add_filter('wp_theme_json_data_theme', [CoreBlocks::class, 'limitEditorPalette'], 11);
        \add_action('init', [self::class, 'registerPatternCategory']);
        \add_action('after_switch_theme', [self::class, 'clearPatternCache']);
        \add_action('upgrader_process_complete', [self::class, 'clearPatternCache']);
        \add_filter('wp_theme_files_cache_ttl', [self::class, 'patternCacheTtl']);
    }

    /** Patterns in patterns/ are auto-registered by WordPress; this adds their category. */
    public static function registerPatternCategory(): void
    {
        \register_block_pattern_category('sister-midnight', ['label' => \__('Sister Midnight', 'gust')]);
    }

    /**
     * WordPress caches the patterns/ file list in a site transient keyed by theme
     * version and path, so patterns added without a version bump stay invisible
     * to editors. Clear it on theme switch and after updates.
     */
    public static function clearPatternCache(): void
    {
        \wp_get_theme()->delete_pattern_cache();
    }

    /** In development, cache the pattern file list for a minute instead of a day. */
    public static function patternCacheTtl(int $ttl): int
    {
        return \wp_get_environment_type() === 'development' ? MINUTE_IN_SECONDS : $ttl;
    }
}
