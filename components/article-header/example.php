<?php

/**
 * Article Header Component Examples
 */

use Gust\Components\ArticleHeader;

$posts = get_posts(['post_type' => 'article', 'posts_per_page' => 1]);

if (! $posts) {
    echo '<p>No articles found. Seed news articles first.</p>';

    return;
}
?>

<section class="component-example-section">
    <h2 class="component-example-section__title">News article</h2>
    <p class="component-example-section__description">Featured image left; date, title (h1) and excerpt right.</p>
    <div class="component-example-section__preview">
        <?= ArticleHeader::make(post: $posts[0]); ?>
    </div>
</section>
