<?php

/**
 * News Card Template
 *
 * The title link is the card's only link; its ::after covers the card so the
 * whole card is clickable while the link's name stays the title.
 *
 * @var \Gust\Components\NewsCard $this
 */
?>
<article class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <?php if ($this->image) { ?>
        <div class="news-card__media"><?= $this->image ?></div>
    <?php } ?>

    <div class="news-card__body">
        <p class="news-card__date"><time datetime="<?= esc_attr($this->datetime) ?>"><?= esc_html($this->date) ?></time></p>

        <<?= $this->heading_tag ?> class="news-card__title">
            <a class="news-card__link" href="<?= esc_url($this->url) ?>"><?= esc_html($this->title) ?></a>
        </<?= $this->heading_tag ?>>

        <?php if ($this->excerpt !== '') { ?>
            <p class="news-card__excerpt"><?= esc_html($this->excerpt) ?></p>
        <?php } ?>
    </div>
</article>
