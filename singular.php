<?php

/**
 * Singular: Site Header (header.php), then the Event Header (events) or the
 * Page Header from the page's fields, the content, then the Newsletter
 * Banner and Site Footer (footer.php).
 */
get_header();

$object = \Gust\WordPress\PageObject::get();

site_main_open(object: $object);

if ($object instanceof \WP_Post && $object->post_type === 'event') {
    echo \Gust\Components\EventHeader::make(event: $object);
} else {
    echo \Gust\Components\PageHeader::fromObject($object);
}

while (have_posts()) {
    the_post();
    the_content();
}

site_main_close();

get_footer();
