<?php

/**
 * Global helper functions.
 */
if (! function_exists('classes')) {
    /**
     * Build HTML class attribute value from strings/arrays.
     *
     * @param  mixed  ...$args  Strings or arrays of class names.
     * @return string Escaped class string.
     */
    function classes(...$args): string
    {
        return \Gust\Helpers::classes(...$args);
    }
}

if (! function_exists('attributes')) {
    /**
     * Build HTML attributes string from key-value array.
     *
     * @param  array  $attributes  Key-value pairs.
     * @return string Escaped attributes string.
     */
    function attributes(array $attributes = []): string
    {
        return \Gust\Helpers::buildAttributes($attributes);
    }
}

if (! function_exists('staticUrl')) {
    /**
     * Get URL for static assets (images, fonts).
     *
     * @param  string  $path  Path relative to assets/static/
     * @return string Full asset URL.
     */
    function staticUrl(string $path): string
    {
        return \Gust\Helpers::staticUrl($path);
    }
}

if (! function_exists('site_main_open')) {
    /**
     * Output opening SiteMain markup.
     */
    function site_main_open(
        array $classes = [],
        ?object $object = null,
        ?string $inner_el = null,
        array $attributes = [],
    ): void {
        echo \Gust\Components\SiteMain::open($classes, $object, $inner_el, $attributes);
    }
}

if (! function_exists('site_main_close')) {
    /**
     * Output closing SiteMain markup.
     */
    function site_main_close(): void
    {
        echo \Gust\Components\SiteMain::close();
    }
}
