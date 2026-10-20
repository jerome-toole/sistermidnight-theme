<?php

namespace Theme\Modules\Core;

class Admin
{
    public static function init(): void
    {
        \add_action('wp_enqueue_scripts', [__CLASS__, 'enqueueAdminBarStyles']);
        \add_action('admin_enqueue_scripts', [__CLASS__, 'enqueueAdminBarStyles']);
    }

    public static function enqueueAdminBarStyles(): void
    {
        if (! \is_admin_bar_showing()) {
            return;
        }

        \Gust\Vite::enqueueStyle('gust-admin-bar-styles', 'components/admin/admin-bar.pcss');
    }
}
