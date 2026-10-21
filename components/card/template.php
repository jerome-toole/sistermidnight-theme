<?php

use Gust\Components\Button;
use Gust\Components\Illustration;

/**
 * Card Template
 *
 * In an editor preview the heading and text are always printed (placeholders)
 * so they can be edited in place.
 *
 * @var \Gust\Components\Card $this
 */
?>
<div class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <?php if ($this->illustration) { ?>
        <div class="card__illustration" <?= $this->inlineToolbar(['illustration'], __('Illustration', 'gust')) ?>>
            <?= Illustration::make(name: $this->illustration); ?>
        </div>
    <?php } ?>

    <div class="card__inner">
        <?php if ($this->heading !== '' || $this->is_preview) { ?>
            <h<?= $this->heading_level ?> class="card__heading" <?= $this->inlineText('heading', __('Card heading', 'gust')) ?>><?= esc_html($this->heading) ?></h<?= $this->heading_level ?>>
        <?php } ?>

        <?php if ($this->text !== '' || $this->is_preview) { ?>
            <div class="card__text" <?= $this->inlineToolbar(['text', 'link', 'show_newsletter_form'], __('Card content', 'gust')) ?>>
                <?php if ($this->text !== '') { ?>
                    <?= wp_kses_post($this->text); ?>
                <?php } else { ?>
                    <p class="card__placeholder"><?= esc_html__('Card text', 'gust') ?></p>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if ($this->link) { ?>
            <?= Button::make(
                content: esc_html($this->link['title']),
                url: $this->link['url'],
                target: $this->link['target'] ?: null,
                variant: 'link',
                arrow: false,
                classes: ['card__link'],
            ); ?>
        <?php } ?>

        <?= $this->form; ?>
    </div>
</div>
