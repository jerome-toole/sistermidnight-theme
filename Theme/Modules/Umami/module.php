<?php

namespace Theme\Modules\Umami;

class UmamiModule
{
    public static function init(): void
    {
        \add_action('wp_head', [__CLASS__, 'outputHead']);
    }

    public static function outputHead(): void
    {
        if (\wp_get_environment_type() !== 'production' || \is_user_logged_in()) {
            return;
        }

        ?>
        <script defer src="https://cloud.umami.is/script.js" data-website-id="1262ca1b-48b3-4fb0-bab7-4092af9fff94"></script>
        <?php
    }
}
