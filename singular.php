<?php

/**
 * Singular: Site Header (header.php), then the Event Header (events), the
 * Article Header (news posts) or the Page Header from the page's fields, the
 * content, then the Newsletter Banner and Site Footer (footer.php).
 * News post content sits in a text-right stone-beige Section.
 */
get_header();

$object = \Gust\WordPress\PageObject::get();
$post_type = $object instanceof \WP_Post ? $object->post_type : '';

site_main_open(object: $object);

if ($post_type === 'event') {
    echo \Gust\Components\EventHeader::make(event: $object);
} elseif ($post_type === 'post') {
    echo \Gust\Components\ArticleHeader::make(post: $object);
} else {
    echo \Gust\Components\PageHeader::fromObject($object);
}

while (have_posts()) {
    the_post();

    if ($post_type === 'post') {
        echo \Gust\Components\Section::make(
            background: 'stone-beige',
            layout: 'text-right',
            content: apply_filters('the_content', get_the_content()),
            classes: ['article-content'],
        );
    } else {
        the_content();
    }
}

site_main_close();

get_footer();
