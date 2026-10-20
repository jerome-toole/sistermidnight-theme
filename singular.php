<?php

get_header();

$object = \Gust\WordPress\PageObject::get();

site_main_open(object: $object);

if (! has_block('theme/page-header') && ! has_block('theme/homepage-hero')) {
    echo \Gust\Components\PageHeader::make(object: $object);
}

while (have_posts()) {
    the_post();
    the_content();
}

site_main_close();

get_footer();
