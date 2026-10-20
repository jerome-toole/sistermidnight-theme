<?php

get_header();

$object = \Gust\WordPress\PageObject::get();

site_main_open(object: $object);

// What's On draws its own vertical <h1> beside the featured cards (EventsModule::renderArchive).
if (! is_post_type_archive('event')) {
    echo \Gust\Components\PageHeader::fromObject($object);
}

$routerPage = \Gust\Router::getPage();

if ($routerPage) {
    echo apply_filters('the_content', $routerPage->post_content);

    if (! has_block('theme/template-content', $routerPage)) {
        echo \Gust\Router::renderSlot('template-content');
    }
} else {
    if (have_posts()) {
        echo '<div class="content-width-lg grid gap-6 md:grid-cols-2 lg:grid-cols-3">';
        while (have_posts()) {
            the_post();
            echo \Gust\Components\NewsCard::make(post: get_post());
        }
        echo '</div>';

        echo \Gust\Components\Pagination::make();
    } else {
        echo \Gust\Components\NoContent::make(
            object: $object,
        );
    }
}

site_main_close();

get_footer();
