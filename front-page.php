<?php

/**
 * Front page: Site Header (hero variant, from header.php) over the Hero, the
 * Radio Bar, then the page's blocks. The Hero and Radio Bar sit outside <main>
 * so the skip link lands on the content.
 */
get_header();

echo \Gust\Components\Hero::fromOptions();
echo \Gust\Components\RadioBar::fromService();

$object = \Gust\WordPress\PageObject::get();

site_main_open(object: $object);

while (have_posts()) {
    the_post();
    the_content();
}

site_main_close();

get_footer();
