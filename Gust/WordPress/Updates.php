<?php

namespace Gust\WordPress;

class Updates
{
    public static function init(): void
    {
        $updateThemes = (bool) \Gust\Config::get('auto_update_themes', true);
        $updatePlugins = (bool) \Gust\Config::get('auto_update_plugins', true);
        $updateCore = (bool) \Gust\Config::get('auto_update_core', true);

        \add_filter('auto_update_theme', fn() => $updateThemes);
        \add_filter('auto_update_plugin', fn() => $updatePlugins);

        \add_filter('themes_auto_update_enabled', '__return_false');
        \add_filter('plugins_auto_update_enabled', '__return_false');

        \add_filter('allow_major_auto_core_updates', fn() => $updateCore);
        \add_filter('allow_minor_auto_core_updates', fn() => $updateCore);
        \add_filter('allow_dev_auto_core_updates', '__return_false');

        /**
         * Remove Site Health recommendation that there may be problems with plugin and theme auto-updates.
         */
        \add_filter('site_status_tests', [__CLASS__, 'removeSiteHealthAutoUpdateRecommendation']);

        /**
         * Disable email notifications for auto-updates.
         */
        \add_filter('auto_plugin_update_email', '__return_false');
        \add_filter('auto_theme_update_email', '__return_false');
    }

    /**
     * Filter which site status tests are run on a site to remove the plugin and theme auto-updates test.
     *
     * @link https://developer.wordpress.org/reference/hooks/site_status_tests/
     *
     * @param  array  $tests  An associative array of direct and asynchronous tests.
     * @return array The filtered tests array.
     */
    public static function removeSiteHealthAutoUpdateRecommendation(array $tests): array
    {
        unset($tests['direct']['plugin_theme_auto_updates']);

        return $tests;
    }
}
