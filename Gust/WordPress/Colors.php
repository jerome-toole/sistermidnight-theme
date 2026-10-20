<?php

namespace Gust\WordPress;

class Colors
{
    private static ?array $config = null;

    public static function init(): void
    {
        \add_filter('wp_theme_json_data_theme', [__CLASS__, 'injectColorPalette']);
        \add_action('init', [__CLASS__, 'defineColorPaletteConstant']);
    }

    /**
     * Load theme config JSON.
     */
    public static function getConfig(): array
    {
        if (self::$config === null) {
            // Theme root, not assets/: it's read at runtime and assets/ isn't deployed
            $path = \get_theme_file_path('theme-config.json');

            if (! is_readable($path)) {
                \wp_trigger_error(__METHOD__, "Missing {$path}, so the color palette is empty.");

                return self::$config = [];
            }

            self::$config = json_decode(file_get_contents($path), true) ?? [];
        }

        return self::$config;
    }

    /**
     * Get colors formatted for WordPress palette.
     */
    public static function getWordPressPalette(): array
    {
        $config = self::getConfig();
        $baseColors = $config['colors']['base'] ?? [];
        $palette = [];

        foreach ($baseColors as $slug => $colorConfig) {
            // Skip colors that reference other colors (namedColor) or have no hex value
            if (empty($colorConfig['color'])) {
                continue;
            }

            $palette[] = [
                'slug' => $slug,
                'name' => $colorConfig['name'] ?? ucfirst(str_replace('-', ' ', $slug)),
                'color' => $colorConfig['color'],
            ];
        }

        return $palette;
    }

    /**
     * Inject color palette into theme.json data (WP 6.1+).
     */
    public static function injectColorPalette(\WP_Theme_JSON_Data $theme_json): \WP_Theme_JSON_Data
    {
        $palette = self::getWordPressPalette();

        if (empty($palette)) {
            return $theme_json;
        }

        $new_data = [
            'version' => 2,
            'settings' => [
                'color' => [
                    'palette' => $palette,
                ],
            ],
        ];

        return $theme_json->update_with($new_data);
    }

    /**
     * Define GUST_COLOR_PALETTE constant for backward compatibility.
     */
    public static function defineColorPaletteConstant(): void
    {
        if (defined('GUST_COLOR_PALETTE')) {
            return;
        }

        define('GUST_COLOR_PALETTE', self::getWordPressPalette());
    }
}
