<?php

namespace Theme\Controllers;

use Gust\Components\NewsCard;
use Gust\Components\NoContent;
use Gust\Components\Pagination;
use Gust\WordPress\PageObject;

/**
 * News archive (/news/, the article post type archive): the router page's header and
 * content (index.php), then a two-column grid of News Cards and pagination.
 */
class NewsController
{
    public function prepare(): void {}

    public static function renderLoop(): string
    {
        $cards = '';
        while (\have_posts()) {
            \the_post();
            $cards .= NewsCard::make(post: \get_post());
        }

        if ($cards === '') {
            return (string) NoContent::make(object: PageObject::get());
        }

        \ob_start();
        ?>
        <div class="news-archive alignfull pb-(--space-layout)">
            <div class="mx-auto grid max-w-(--width-fluid-content) gap-(--content-layout--gap) md:grid-cols-2">
                <?= $cards; ?>
            </div>
            <?= Pagination::make(); ?>
        </div>
        <?php

        return \ob_get_clean();
    }
}
