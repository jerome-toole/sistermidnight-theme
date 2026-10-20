<?php

/**
 * Front page: Site Header (hero variant, from header.php) over the Hero, then
 * the page's blocks. The Hero sits outside <main> so the skip link lands on
 * the content.
 */
get_header();

echo \Gust\Components\Hero::fromOptions();

$object = \Gust\WordPress\PageObject::get();

site_main_open(object: $object);

while (have_posts()) {
    the_post();
    the_content();
}

site_main_close();

get_footer();
