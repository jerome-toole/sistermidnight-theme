<?php

/**
 * Article Header Template
 *
 * @var \Gust\Components\ArticleHeader $this
 */
?>
<header class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="article-header__inner content-layout">
        <?php if ($this->image) { ?>
            <div class="article-header__media content-layout__media content-layout__media--always"><?= $this->image ?></div>
        <?php } ?>

        <div class="article-header__content content-layout__text">
            <div class="article-header__intro">
                <p class="article-header__date"><time datetime="<?= esc_attr($this->datetime) ?>"><?= esc_html($this->date) ?></time></p>
                <h1 class="article-header__title"><?= esc_html($this->title) ?></h1>
            </div>

            <?php if ($this->excerpt !== '') { ?>
                <p class="article-header__excerpt"><?= esc_html($this->excerpt) ?></p>
            <?php } ?>
        </div>
    </div>
</header>
