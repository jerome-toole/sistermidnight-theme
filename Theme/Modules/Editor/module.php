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
    }
}
