<?php

namespace Theme\Modules\Core;

class Preloads
{
    public static function init()
    {
        \add_filter('gust/wordpress/head/preload_assets', [__CLASS__, 'addPreloads']);
    }

    public static function addPreloads(array $preloads): array
    {
        $preloads = array_merge($preloads, [
            [
                'href' => \Gust\Helpers::staticUrl('fonts/sister-midnight-regular.woff2'),
                'type' => 'font/woff2',
                'as' => 'font',
            ],
            [
                'href' => \Gust\Helpers::staticUrl('fonts/foss-modern-bold.woff2'),
                'type' => 'font/woff2',
                'as' => 'font',
            ],
        ]);

        return $preloads;
    }
}
