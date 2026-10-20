<?php

namespace Theme\Modules\Radio;

class RadioModule
{
    public static function init(): void
    {
        \add_action('rest_api_init', [RadioService::class, 'registerRoute']);
    }
}
