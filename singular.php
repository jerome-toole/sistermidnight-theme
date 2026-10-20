<?php

/**
 * Singular: Site Header (header.php), Page Header from the page's fields,
 * the content, then the Newsletter Banner and Site Footer (footer.php).
 */
get_header();

$object = \Gust\WordPress\PageObject::get();

site_main_open(object: $object);

echo \Gust\Components\PageHeader::fromObject($object);

while (have_posts()) {
    the_post();
    the_content();
}

site_main_close();

get_footer();
