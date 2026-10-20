<?php

/**
 * Event Card Template
 *
 * One <a> wraps the card; aria-labelledby makes the title its accessible name.
 *
 * @var \Gust\Components\EventCard $this
 */
?>
<article class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <a class="event-card__link" href="<?= esc_url($this->url) ?>" aria-labelledby="<?= esc_attr($this->title_id) ?>">
        <?php if ($this->poster) { ?>
            <div class="event-card__poster"><?= $this->poster ?></div>
        <?php } ?>

        <div class="event-card__body">
            <div class="event-card__header">
                <?php if ($this->presenter !== '') { ?>
                    <p class="event-card__presenter"><?= esc_html($this->presenter) ?></p>
                <?php } ?>

                <<?= $this->heading_tag ?> class="event-card__title" id="<?= esc_attr($this->title_id) ?>"><?= esc_html($this->title) ?></<?= $this->heading_tag ?>>

                <?php if ($this->excerpt !== '') { ?>
                    <p class="event-card__excerpt"><?= esc_html($this->excerpt) ?></p>
                <?php } ?>
            </div>

            <?php if ($this->date !== '' || $this->venue !== '') { ?>
                <p class="event-card__meta">
                    <?php if ($this->date !== '') { ?>
                        <time class="event-card__date" datetime="<?= esc_attr($this->datetime) ?>"><?= esc_html($this->date) ?></time>
                        <?php if ($this->time !== '') { ?>
                            <span class="event-card__time"><?= esc_html($this->time) ?></span>
                        <?php } ?>
                    <?php } ?>
                    <?php if ($this->venue !== '') { ?>
                        <span class="event-card__venue"><?= esc_html($this->venue) ?></span>
                    <?php } ?>
                </p>
            <?php } ?>
        </div>
    </a>
</article>
