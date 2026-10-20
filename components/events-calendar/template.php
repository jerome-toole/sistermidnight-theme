<?php

/**
 * Events Calendar Template
 *
 * The header and the fallback list are server-rendered; scripts.js replaces
 * the body with the month table or the grouped list once the feed loads.
 *
 * @var \Gust\Components\EventsCalendar $this
 */
?>
<section class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <h2 class="screen-reader-text"><?= esc_html__('Events calendar', 'gust') ?></h2>

    <div class="events-calendar__header">
        <nav class="events-calendar__views" aria-label="<?= esc_attr__('Calendar view', 'gust') ?>">
            <a class="events-calendar__view" href="<?= esc_url($this->view_urls['calendar']) ?>" data-events-calendar-view="calendar" <?= $this->view === 'calendar' ? 'aria-current="page"' : '' ?>><?= esc_html__('Calendar', 'gust') ?></a>
            <span class="events-calendar__divider" aria-hidden="true">//</span>
            <a class="events-calendar__view" href="<?= esc_url($this->view_urls['list']) ?>" data-events-calendar-view="list" <?= $this->view === 'list' ? 'aria-current="page"' : '' ?>><?= esc_html__('List', 'gust') ?></a>
        </nav>

        <nav class="events-calendar__months" aria-label="<?= esc_attr__('Month', 'gust') ?>">
            <a class="events-calendar__month-link" href="<?= esc_url($this->prev_url) ?>" data-events-calendar-prev aria-label="<?= esc_attr__('Previous month', 'gust') ?>"><span aria-hidden="true">&lt;</span></a>
            <span class="events-calendar__month" data-events-calendar-month aria-live="polite"><?= esc_html($this->month_label) ?></span>
            <a class="events-calendar__month-link" href="<?= esc_url($this->next_url) ?>" data-events-calendar-next aria-label="<?= esc_attr__('Next month', 'gust') ?>"><span aria-hidden="true">&gt;</span></a>
        </nav>
    </div>

    <div class="events-calendar__body" data-events-calendar-body>
        <?php if ($this->events) { ?>
            <section class="events-calendar__group">
                <h3 class="events-calendar__group-heading"><?= esc_html__('Next events', 'gust') ?></h3>
                <ul class="events-calendar__rows">
                    <?php foreach ($this->events as $event) { ?>
                        <li class="events-calendar__row">
                            <?php if ($event['date'] !== '') { ?>
                                <time class="events-calendar__row-date" datetime="<?= esc_attr($event['datetime']) ?>">
                                    <?= esc_html($event['date']) ?>
                                    <?php if ($event['time'] !== '') { ?>
                                        <span class="events-calendar__row-time"><?= esc_html($event['time']) ?></span>
                                    <?php } ?>
                                </time>
                            <?php } ?>
                            <a class="events-calendar__row-title" href="<?= esc_url($event['url']) ?>"><?= esc_html($event['title']) ?></a>
                            <?php if ($event['venue'] !== '') { ?>
                                <span class="events-calendar__row-venue"><?= esc_html($event['venue']) ?></span>
                            <?php } ?>
                        </li>
                    <?php } ?>
                </ul>
            </section>
        <?php } else { ?>
            <p class="events-calendar__empty"><?= esc_html__('No upcoming events', 'gust') ?></p>
        <?php } ?>
    </div>
</section>
