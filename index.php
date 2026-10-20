<?php

get_header();

$object = \Gust\WordPress\PageObject::get();

site_main_open(object: $object);

$routerPage = \Gust\Router::getPage();

if ($routerPage) {
    if (! has_block('theme/page-header', $routerPage->ID)) {
        echo \Gust\Components\PageHeader::make(object: $object);
    }

    echo apply_filters('the_content', $routerPage->post_content);
} else {
    $items = [];
    while (have_posts()) {
        the_post();
        $items[]['object'] = get_post();
    }

    if (! has_block('theme/page-header')) {
        echo \Gust\Components\PageHeader::make(object: $object);
    }

    if (! empty($items)) {
        echo \Gust\Components\TaxonomyFilters::make(
            object: $object,
        );

        echo \Gust\Components\Cards::make(items: $items);
        echo \Gust\Components\Pagination::make();
    } else {
        echo \Gust\Components\NoContent::make(
            object: $object,
        );
    }
}

site_main_close();

get_footer();
