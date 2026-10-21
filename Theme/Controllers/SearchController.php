<?php

namespace Theme\Controllers;

use Gust\Components\EventCard;
use Gust\Components\NewsCard;
use Gust\Components\Pagination;
use Gust\Components\SearchForm;

class SearchController
{
    /**
     * Prepare the search query.
     */
    public function prepare(): void
    {
        // An empty ?s= would list every page; show nothing and ask for a term instead.
        \add_action('pre_get_posts', static function (\WP_Query $query): void {
            if ($query->is_main_query() && $query->is_search() && trim((string) $query->get('s')) === '') {
                $query->set('post__in', [0]);
            }
        });
    }

    /**
     * The search form, placed under the Page Header intro.
     */
    public static function renderForm(): string
    {
        return (string) SearchForm::make();
    }

    /**
     * Render the search results: events as small Event Cards, everything
     * else as News Cards, in the news archive's two-column grid.
     */
    public static function renderResults(): string
    {
        if (trim(\get_search_query()) === '') {
            return '<p class="content-layout__text">'.\esc_html__('Enter a search term to find events, news and pages.', 'theme').'</p>';
        }

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
