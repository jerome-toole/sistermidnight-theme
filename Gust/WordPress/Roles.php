<?php

namespace Gust\WordPress;

use Gust\Config;

/**
 * Site Manager: a client role with full editorial access but none of the
 * dev/admin surface (settings, plugins, themes, core updates).
 *
 * Theme modules and projects add plugin capabilities via the
 * `gust/roles/site_manager/capabilities` filter.
 */
class Roles
{
    const ROLE = 'site_manager';

    const LABEL = 'Site Manager';

    const VERSION_OPTION = 'gust_site_manager_role_version';

    const CAPABILITIES = [
        // Content (matches core Editor, minus unfiltered_html; moderate_comments added when comments are on)
        'read',
        'edit_posts',
        'edit_others_posts',
        'edit_published_posts',
        'edit_private_posts',
        'publish_posts',
        'read_private_posts',
        'delete_posts',
        'delete_others_posts',
        'delete_published_posts',
        'delete_private_posts',
        'edit_pages',
        'edit_others_pages',
        'edit_published_pages',
        'edit_private_pages',
        'publish_pages',
        'read_private_pages',
        'delete_pages',
        'delete_others_pages',
        'delete_published_pages',
        'delete_private_pages',
        'upload_files',
        'manage_categories',

        // Menus
        'edit_theme_options',

        // Users (assignable roles and admin accounts are restricted below)
        'list_users',
        'create_users',
        'edit_users',
        'delete_users',
        'promote_users',
    ];

    /**
     * edit_theme_options is needed for Menus but also unlocks these screens.
     */
    const BLOCKED_APPEARANCE_PAGES = ['themes.php', 'site-editor.php', 'font-library.php', 'customize.php', 'widgets.php'];

    public static function init(): void
    {
        if (! Config::get('site_manager_role', true)) {
            // A leftover role would keep its caps without the restrictions below.
            \add_action('init', [__CLASS__, 'removeRole']);

            return;
        }

        \add_action('init', [__CLASS__, 'syncRole']);
        \add_filter('editable_roles', [__CLASS__, 'filterEditableRoles']);
        \add_filter('map_meta_cap', [__CLASS__, 'protectPrivilegedUsers'], 10, 4);
        \add_action('admin_menu', [__CLASS__, 'hideAppearanceMenu'], 999);
        \add_filter('parent_file', [__CLASS__, 'highlightMenusPage']);

        foreach (self::BLOCKED_APPEARANCE_PAGES as $page) {
            \add_action("load-{$page}", [__CLASS__, 'blockAppearancePage']);
        }
    }

    /**
     * Full capability list, including any added by modules or the project.
     *
     * @return string[]
     */
    public static function getCapabilities(): array
    {
        $caps = self::CAPABILITIES;

        if (! Config::get('deactivate_comments', false)) {
            $caps[] = 'moderate_comments';
        }

        $caps = \apply_filters('gust/roles/site_manager/capabilities', $caps);

        return array_values(array_unique($caps));
    }

    /**
     * Roles live in the database, so re-create the role whenever the
     * capability list changes. Users keep their role slug, so nothing is lost.
     */
    public static function syncRole(): void
    {
        $caps = self::getCapabilities();
        $version = md5(serialize([self::LABEL, $caps]));

        if (\get_role(self::ROLE) && \get_option(self::VERSION_OPTION) === $version) {
            return;
        }

        \remove_role(self::ROLE);
        \add_role(self::ROLE, self::LABEL, array_fill_keys($caps, true));
        \update_option(self::VERSION_OPTION, $version);
    }

    public static function removeRole(): void
    {
        if (! \get_role(self::ROLE)) {
            return;
        }

        \remove_role(self::ROLE);
        \delete_option(self::VERSION_OPTION);
    }

    protected static function canManageAppearance(): bool
    {
        return \current_user_can('switch_themes');
    }

    /**
     * Hide Appearance for users who only have edit_theme_options.
     * They reach menus via Gust's top-level Menus item instead.
     */
    public static function hideAppearanceMenu(): void
    {
        if (self::canManageAppearance() || ! \current_user_can('edit_theme_options')) {
            return;
        }

        \remove_menu_page('themes.php');
    }

    public static function highlightMenusPage(string $parent_file): string
    {
        if (self::canManageAppearance() || $parent_file !== 'themes.php') {
            return $parent_file;
        }

        return 'nav-menus.php';
    }

    public static function blockAppearancePage(): void
    {
        if (self::canManageAppearance()) {
            return;
        }

        \wp_safe_redirect(\admin_url('nav-menus.php'));
        exit;
    }

    /**
     * Only allow assigning roles whose capabilities the current user already has,
     * so a Site Manager can't create an Administrator (or an Editor with unfiltered_html).
     */
    public static function filterEditableRoles(array $roles): array
    {
        // Admins don't hold every plugin cap (e.g. gravityforms_brevo), so skip the check.
        if (\current_user_can('manage_options')) {
            return $roles;
        }

        $user = \wp_get_current_user();

        return array_filter($roles, function (array $role) use ($user) {
            // Ignore legacy user levels (level_0–level_10) that core roles still carry.
            $caps = preg_grep('/^level_\d+$/', array_keys(array_filter($role['capabilities'])), PREG_GREP_INVERT);

            return empty(array_diff($caps, array_keys(array_filter($user->allcaps))));
        });
    }

    /**
     * Stop users editing or deleting accounts that have more access than they do.
     */
    public static function protectPrivilegedUsers(array $caps, string $cap, int $user_id, array $args): array
    {
        if (! in_array($cap, ['edit_user', 'delete_user', 'promote_user', 'remove_user'], true) || empty($args[0])) {
            return $caps;
        }

        $target_id = (int) $args[0];

        if ($target_id === $user_id) {
            return $caps;
        }

        if (\user_can($target_id, 'manage_options') && ! \user_can($user_id, 'manage_options')) {
            $caps[] = 'do_not_allow';
        }

        return $caps;
    }
}
