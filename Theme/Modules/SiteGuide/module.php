<?php

namespace Theme\Modules\SiteGuide;

class SiteGuideModule
{
    const PAGE_SLUG = 'site-guide';

    public static function init(): void
    {
        if (! \Gust\Config::get('site_guide', true)) {
            return;
        }

        \add_action('wp_dashboard_setup', [__CLASS__, 'addDashboardWidget']);
        \add_action('admin_menu', [__CLASS__, 'addAdminPage']);
        \add_action('admin_enqueue_scripts', [__CLASS__, 'enqueueStyles']);
    }

    public static function addDashboardWidget(): void
    {
        \wp_add_dashboard_widget(
            'site_guide_widget',
            'Site Guide',
            [__CLASS__, 'renderDashboardWidget']
        );
    }

    public static function addAdminPage(): void
    {
        \add_menu_page(
            'Site Guide',
            'Site Guide',
            'edit_posts',
            self::PAGE_SLUG,
            [__CLASS__, 'renderAdminPage'],
            'dashicons-book-alt',
            3
        );
    }

    public static function enqueueStyles(string $hook): void
    {
        if ($hook !== 'toplevel_page_'.self::PAGE_SLUG && $hook !== 'index.php') {
            return;
        }

        \wp_add_inline_style('wp-admin', self::getStyles());
    }

    /**
     * Override in downstream themes to provide project-specific quick links.
     */
    public static function renderDashboardWidget(): void
    {
        $guide_url = \admin_url('admin.php?page='.self::PAGE_SLUG);
        $pages_url = \admin_url('edit.php?post_type=page');
        $menus_url = \admin_url('nav-menus.php');
        ?>
        <div class="site-guide-widget">
            <p>Quick links to common editing tasks:</p>
            <ul>
                <li><a href="<?= esc_url($pages_url) ?>">Manage Pages</a></li>
                <li><a href="<?= esc_url($menus_url) ?>">Edit Navigation Menus</a></li>
            </ul>
            <p><a href="<?= esc_url($guide_url) ?>" class="button button-primary">View Full Site Guide</a></p>
        </div>
        <?php
    }

    public static function renderAdminPage(): void
    {
        ?>
        <div class="wrap site-guide">
            <h1>Site Guide</h1>
            <p class="site-guide__intro">A reference for editing content on this site. If you're unsure where something lives or how to change it, start here.</p>

            <?php self::renderContentTypes(); ?>
            <?php self::renderBlocks(); ?>
            <?php self::renderGlobalSettings(); ?>
            <?php self::renderNavigation(); ?>
            <?php self::renderMedia(); ?>
            <?php self::renderHelpfulLinks(); ?>
        </div>
        <?php
    }

    protected static function renderContentTypes(): void
    {
        $pages_url = \admin_url('edit.php?post_type=page');
        ?>
        <div class="site-guide__section">
            <h2>Content Types</h2>

            <div class="site-guide__card">
                <h3><a href="<?= esc_url($pages_url) ?>">Pages</a></h3>
                <p>Standard pages built with the block editor and custom blocks.</p>
            </div>
        </div>
        <?php
    }

    protected static function renderBlocks(): void
    {
        ?>
        <div class="site-guide__section">
            <h2>Blocks &amp; Page Building</h2>
            <p>Pages are built using the WordPress block editor. Click the <strong>+</strong> button to insert a block. Custom blocks appear under the theme category in the block inserter.</p>
        </div>
        <?php
    }

    protected static function renderGlobalSettings(): void
    {
        $general_url = \admin_url('admin.php?page=acf-options-general');
        ?>
        <div class="site-guide__section">
            <h2>Global Settings</h2>
            <p>Site-wide settings are managed under <strong>Options</strong> in the admin sidebar.</p>

            <div class="site-guide__card">
                <h3><a href="<?= esc_url($general_url) ?>">General</a></h3>
                <p>Analytics, tracking, and general site configuration.</p>
            </div>
        </div>
        <?php
    }

    protected static function renderNavigation(): void
    {
        $menus_url = \admin_url('nav-menus.php');
        ?>
        <div class="site-guide__section">
            <h2>Navigation Menus</h2>
            <p>Menus are managed under <a href="<?= esc_url($menus_url) ?>">Appearance &rarr; Menus</a>.</p>
        </div>
        <?php
    }

    protected static function renderMedia(): void
    {
        $media_url = \admin_url('upload.php');
        ?>
        <div class="site-guide__section">
            <h2>Images &amp; Media</h2>
            <p>All images are managed in the <a href="<?= esc_url($media_url) ?>">Media Library</a>.</p>
            <ul>
                <li><strong>Alt text</strong> — Always add descriptive alt text for accessibility and SEO.</li>
                <li><strong>File size</strong> — Keep images under 500KB where possible.</li>
            </ul>
        </div>
        <?php
    }

    protected static function renderHelpfulLinks(): void
    {
        ?>
        <div class="site-guide__section">
            <h2>Helpful Links</h2>
            <ul>
                <li><a href="https://wordpress.org/documentation/article/wordpress-block-editor/" target="_blank" rel="noopener">WordPress Block Editor Guide</a></li>
                <li><a href="https://wordpress.org/documentation/" target="_blank" rel="noopener">WordPress Documentation</a></li>
            </ul>
        </div>
        <?php
    }

    private static function getStyles(): string
    {
        return '
            .site-guide__intro {
                font-size: 14px;
                max-width: 720px;
            }
            .site-guide__section {
                max-width: 720px;
                margin-bottom: 2em;
            }
            .site-guide__section h2 {
                border-bottom: 1px solid #c3c4c7;
                padding-bottom: 8px;
            }
            .site-guide__card {
                background: #fff;
                border: 1px solid #c3c4c7;
                border-radius: 4px;
                padding: 12px 16px;
                margin-bottom: 8px;
            }
            .site-guide__card h3 {
                margin: 0 0 4px;
            }
            .site-guide__card p {
                margin: 0;
            }
        ';
    }
}
