<?php

/**
 * Event Header Template
 *
 * The meta row is a <dl> (Date / Time / Venue) with visually hidden terms;
 * the date value is a <time datetime>. Past events get a <p> note instead of
 * the CTA button.
 *
 * @var \Gust\Components\EventHeader $this
 */
?>
<header class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="event-header__inner content-layout">
        <?php if ($this->poster) { ?>
            <div class="event-header__poster content-layout__media content-layout__media--always"><?= $this->poster ?></div>
        <?php } ?>

        <div class="event-header__content content-layout__text">
            <div class="event-header__intro">
                <?php if ($this->presenter !== '') { ?>
                    <p class="event-header__presenter"><?= esc_html($this->presenter) ?></p>
                <?php } ?>

                <h1 class="event-header__title"><?= esc_html($this->title) ?></h1>

                <?php if ($this->date !== '' || $this->venue !== '') { ?>
                    <dl class="event-header__meta">
                        <?php if ($this->date !== '') { ?>
                            <div class="event-header__meta-item">
                                <dt class="screen-reader-text"><?= esc_html__('Date', 'gust') ?></dt>
                                <dd><time datetime="<?= esc_attr($this->datetime) ?>"><?= esc_html($this->date) ?></time></dd>
                            </div>
                            <?php if ($this->time !== '') { ?>
                                <div class="event-header__meta-item">
                                    <dt class="screen-reader-text"><?= esc_html__('Time', 'gust') ?></dt>
                                    <dd><?= esc_html($this->time) ?></dd>
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <?php if ($this->venue !== '') { ?>
                            <div class="event-header__meta-item">
                                <dt class="screen-reader-text"><?= esc_html__('Venue', 'gust') ?></dt>
                                <dd><?= esc_html($this->venue) ?></dd>
                            </div>
                        <?php } ?>
                    </dl>
                <?php } ?>
            </div>

            <?php if ($this->excerpt !== '' || $this->cta || $this->is_past) { ?>
                <div class="event-header__details">
                    <?php if ($this->excerpt !== '') { ?>
                        <p class="event-header__excerpt"><?= esc_html($this->excerpt) ?></p>
                    <?php } ?>

                    <?php if ($this->is_past) { ?>
                        <p class="event-header__note"><?= esc_html__('This event has passed', 'gust') ?></p>
                    <?php } elseif ($this->cta) { ?>
                        <?= $this->cta ?>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</header>
