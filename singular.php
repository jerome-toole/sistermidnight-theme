<?php

/**
 * Singular: Site Header (header.php), then the Event Header (events), the
 * Article Header (news articles) or the Page Header from the page's fields, the
 * content, then the Newsletter Banner and Site Footer (footer.php).
 * Article content sits in a text-right stone-beige Section; so does event
 * content made only of core blocks (no theme/* band blocks), so loose prose
 * gets the design's content-width text column.
 */
get_header();

$object = \Gust\WordPress\PageObject::get();
$post_type = $object instanceof \WP_Post ? $object->post_type : '';

site_main_open(object: $object);

if ($post_type === 'event') {
    echo \Gust\Components\EventHeader::make(event: $object);
} elseif ($post_type === 'article') {
    echo \Gust\Components\ArticleHeader::make(post: $object);
} else {
    echo \Gust\Components\PageHeader::fromObject($object);
}

/** True when none of the top-level blocks is a theme block (Section, Image Pair, FAQ…). */
$isProseOnly = static function (string $content): bool {
    foreach (parse_blocks($content) as $block) {
        if (str_starts_with((string) $block['blockName'], 'theme/')) {
            return false;
        }
    }

    return true;
};

while (have_posts()) {
    the_post();

    $content = trim((string) apply_filters('the_content', get_the_content()));

    // Nothing to show (or only empty paragraphs): no content band at all.
    if (trim((string) preg_replace('#<p(\s[^>]*)?>(\s|&nbsp;)*</p>#i', '', $content)) === '') {
        continue;
    }

    if ($post_type === 'article' || ($post_type === 'event' && $isProseOnly(get_the_content()))) {
        echo \Gust\Components\Section::make(
            background: 'stone-beige',
            layout: 'text-right',
            content: $content,
            classes: ['article-content'],
        );
    } else {
        echo $content;
    }
}

site_main_close();

get_footer();
