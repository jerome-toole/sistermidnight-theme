<?php

namespace Theme\Controllers;

use Gust\Components\Card;
use Gust\Components\NewsCard;
use Gust\Components\NoContent;
use Gust\Components\PageHeader;
use Gust\Components\Pagination;
use Gust\WordPress\PageObject;

/**
 * News archive (/news/, the article post type archive): the Page Header from the
 * "News" router page (label, intro_text) with the newsletter Card under the intro,
 * the router page's content (index.php), then a two-column grid of News Cards and
 * pagination.
 */
class NewsController
{
    public function prepare(): void {}

    /** Page Header with the "Stay in touch" newsletter Card under the intro. */
    public static function renderHeader(?object $object = null): string
    {
        $card = Card::make(
            heading: \__('Stay in touch', 'theme'),
            text: '<p>'.\esc_html__('We send an occasional newsletter letting you know about our events.', 'theme').'</p>',
            illustration: 'disco-ball',
            show_newsletter_form: true,
            size: 'sm',
            heading_level: 2,
        );

        return (string) PageHeader::fromObject($object, after_intro: (string) $card);
    }

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
