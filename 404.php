<?php

get_header();

$object = \Gust\WordPress\PageObject::get();

site_main_open(object: $object);

echo \Gust\Components\PageHeader::fromObject($object, after_intro: \Theme\Controllers\NotFoundController::renderContent());

$routerPage = \Gust\Router::getPage();

// The not-found message is the header intro unless the 404 page sets its own;
// the Template Content slot only renders where the page places the block.
if ($routerPage) {
    echo apply_filters('the_content', $routerPage->post_content);
} else {
    echo \Gust\Components\NoContent::make(object: $object);
}

site_main_close();

get_footer();
