<?php

namespace Theme\Controllers;

use Gust\Components\EventCard;
use Gust\Components\NewsCard;
use Gust\Components\Pagination;

class SearchController
{
    /**
     * Prepare the search query.
     */
    public function prepare(): void
    {
        // Modify query if needed
    }

    /**
     * Render the search results: events as small Event Cards, everything
     * else as News Cards, in the news archive's two-column grid.
     */
    public static function renderResults(): string
    {
        $cards = '';
        while (\have_posts()) {
            \the_post();
            $post = \get_post();

            $cards .= $post->post_type === 'event'
                ? EventCard::make(event: $post, size: 'sm', heading_level: 2)
                : NewsCard::make(post: $post);
        }

        if ($cards === '') {
            return '<p class="content-layout__text">'.\esc_html__('No results found. Try a different search term.', 'theme').'</p>';
        }

        \ob_start();
        ?>
        <div class="search-results alignfull pb-(--space-layout)">
            <div class="mx-auto grid max-w-(--width-fluid-content) gap-(--content-layout--gap) md:grid-cols-2">
                <?= $cards; ?>
            </div>
            <?= Pagination::make(); ?>
        </div>
        <?php

        return \ob_get_clean();
    }
}
