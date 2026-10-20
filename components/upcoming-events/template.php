<?php

use Gust\Components\EventCard;
use Gust\Components\Illustration;

/**
 * Upcoming Events Template
 *
 * One list in date order. In the grid variant each item carries its column
 * and row (custom properties) so CSS can stagger the two columns.
 *
 * @var \Gust\Components\UpcomingEvents $this
 */
?>
<section class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="upcoming-events__inner">
        <?php if ($this->label !== '' || $this->is_preview) { ?>
            <h2 class="upcoming-events__label" <?= $this->inlineText('label', __('Upcoming', 'gust')) ?>><?= esc_html($this->label) ?></h2>
        <?php } ?>

        <div class="upcoming-events__body">
            <?php if ($this->events) { ?>
                <ul class="upcoming-events__list">
                    <?php if ($this->announcement) { ?>
                        <li class="upcoming-events__item upcoming-events__item--announcement"><?= $this->announcement ?></li>
                    <?php } ?>

                    <?php foreach ($this->events as $index => $event) { ?>
                        <li class="upcoming-events__item" <?= $this->placement($index) ? 'style="'.esc_attr($this->placement($index)).'"' : '' ?>>
                            <?= EventCard::make(
                                event: $event,
                                size: $this->variant === 'grid' ? 'lg' : 'sm',
                                heading_level: $this->heading_level,
                                classes: ['upcoming-events__card'],
                            ); ?>
                        </li>
                    <?php } ?>
                </ul>

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
