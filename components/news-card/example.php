<?php

/**
 * News Card Component Examples
 */

use Gust\Components\NewsCard;

$posts = get_posts(['post_type' => 'post', 'posts_per_page' => 2]);

if (! $posts) {
    echo '<p>No posts found. Seed news posts first.</p>';

    return;
}
?>

<section class="component-example-section">
    <h2 class="component-example-section__title">News cards</h2>
    <p class="component-example-section__description">Image, date, title and excerpt; the whole card links to the post.</p>
    <div class="component-example-section__preview">
        <div class="grid gap-8 md:grid-cols-2">
            <?php foreach ($posts as $post) { ?>
                <?= NewsCard::make(post: $post, heading_level: 3); ?>
            <?php } ?>
        </div>
    </div>
</section>
