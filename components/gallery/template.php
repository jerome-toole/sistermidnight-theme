<?php
/**
 * Gallery Template
 *
 * Masonry: a dense grid list. Carousel: a region with prev/next buttons and
 * a natively scrollable, focusable list that scripts.js hands to Blossom.
 * In an editor preview the label is always printed (placeholder) and the
 * images open from an inline toolbar on the body.
 *
 * @var \Gust\Components\Gallery $this
 */
$chevron = staticUrl('images/icons/chevron-right.svg');
?>
<div class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="gallery__inner">
        <?php if ($this->label !== '' || $this->is_preview) { ?>
            <h2 class="gallery__label" <?= $this->inlineText('label', __('Gallery', 'gust')) ?>><?= esc_html($this->label) ?></h2>
        <?php } ?>

        <div class="gallery__body" <?= $this->inlineToolbar(['images'], __('Images', 'gust')) ?>>
            <?php if ($this->images === [] && $this->is_preview) { ?>
                <p class="gallery__placeholder"><?= esc_html__('Add images from the toolbar', 'gust') ?></p>
            <?php } elseif ($this->style === 'carousel') { ?>
                <div class="gallery__carousel" role="region" aria-roledescription="carousel" aria-label="<?= esc_attr($this->label !== '' ? $this->label : __('Gallery', 'gust')) ?>">
                    <div class="gallery__nav">
                        <button type="button" class="gallery__arrow gallery__arrow--prev" data-gallery-prev aria-label="<?= esc_attr__('Previous images', 'gust') ?>">
                            <span class="gallery__arrow-icon" style="--gallery--icon: url('<?= esc_url($chevron) ?>')"></span>
                        </button>
                        <button type="button" class="gallery__arrow gallery__arrow--next" data-gallery-next aria-label="<?= esc_attr__('Next images', 'gust') ?>">
                            <span class="gallery__arrow-icon" style="--gallery--icon: url('<?= esc_url($chevron) ?>')"></span>
                        </button>
                    </div>

                    <ul class="gallery__track" data-gallery-track tabindex="0" aria-label="<?= esc_attr__('Images', 'gust') ?>">
                        <?php foreach ($this->images as $item) { ?>
                            <li class="gallery__slide"><?= $item['image'] ?></li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } else { ?>
                <ul class="gallery__grid">
                    <?php foreach ($this->images as $item) { ?>
                        <li class="<?= classes('gallery__item', $item['landscape'] ? 'gallery__item--wide' : null) ?>"><?= $item['image'] ?></li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>
    </div>
</div>
