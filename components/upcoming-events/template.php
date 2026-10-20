<?php

use Gust\Components\Illustration;

/**
 * Upcoming Events Template
 *
 * One list per column (two in the grid variant, one in featured). Each item
 * carries its date-order index so the grid collapses to one column in date
 * order below md.
 *
 * @var \Gust\Components\UpcomingEvents $this
 */
?>
<section class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="upcoming-events__inner">
        <?php if ($this->label !== '' || $this->is_preview) { ?>
            <<?= $this->label_tag ?> class="upcoming-events__label" <?= $this->inlineText('label', __('Upcoming', 'gust')) ?>><?= esc_html($this->label) ?></<?= $this->label_tag ?>>
        <?php } ?>

        <div class="upcoming-events__body">
            <?php if ($this->events) { ?>
                <div class="upcoming-events__columns">
                    <?php foreach ($this->columns() as $column) { ?>
                        <ul class="upcoming-events__list">
                            <?php foreach ($column as $item) { ?>
                                <li class="<?= classes('upcoming-events__item', $item['classes']) ?>" style="--upcoming-events--order: <?= (int) $item['order'] ?>;"><?= $item['html'] ?></li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>

                <?php if ($this->illustration) { ?>
                    <div class="upcoming-events__illustration" aria-hidden="true">
                        <?= Illustration::make(name: $this->illustration); ?>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="upcoming-events__empty">
                    <p class="upcoming-events__empty-text"><?= esc_html__('No upcoming events', 'gust') ?></p>
                    <?php if ($this->archive_url) { ?>
                        <a class="upcoming-events__empty-link" href="<?= esc_url($this->archive_url) ?>"><?= esc_html__("See what's on", 'gust') ?></a>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</section>
