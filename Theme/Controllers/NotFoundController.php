<?php

namespace Theme\Controllers;

use Gust\Components\SearchForm;

class NotFoundController
{
    /**
     * Render the 404 content: ways on (Home, What's On) and a search form.
     * Placed under the Page Header intro.
     */
    public static function renderContent(): string
    {
        $events = \get_post_type_archive_link('event');

        \ob_start();
        ?>
        <div class="not-found-content flex flex-col gap-6">
            <ul class="not-found-content__links flex flex-wrap gap-x-6 gap-y-2">
                <li><a href="<?= \esc_url(\home_url('/')); ?>"><?php \esc_html_e('Home', 'theme'); ?></a></li>
                <?php if ($events) { ?>
                    <li><a href="<?= \esc_url($events); ?>"><?php \esc_html_e("What's On", 'theme'); ?></a></li>
                <?php } ?>
            </ul>
            <?= SearchForm::make(value: ''); ?>
        </div>
        <?php
        return \ob_get_clean();
    }
}
