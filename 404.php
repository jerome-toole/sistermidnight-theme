<?php

get_header();

$object = \Gust\WordPress\PageObject::get();

site_main_open(object: $object);

$routerPage = \Gust\Router::getPage();

if ($routerPage) {
    echo apply_filters('the_content', $routerPage->post_content);
} else {
    echo \Gust\Components\PageHeader::make(object: $object);
    echo \Gust\Components\NoContent::make(object: $object);
}

site_main_close();

get_footer();
