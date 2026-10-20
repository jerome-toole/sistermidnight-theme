<?php
/**
 * Gallery Template
 *
 * @var \Gust\Components\Gallery $this
 */

use Gust\Components\Heading;
use Gust\Components\Image;

$image_count = count($this->images ?? []);
?>

<media-gallery class="<?= classes('wp-block', 'alignfull', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="media-gallery__inner content-width-fluid-lg">
        <div class="media-gallery__header">
            <?php if (! empty($this->heading)) { ?>
                <?= Heading::make(...$this->heading); ?>
            <?php } ?>

            <?php if ($image_count > 1) { ?>
                <div class="media-gallery__nav">
                    <button type="button" class="media-gallery__prev btn btn--ghost" aria-label="Previous slide">
                        <span class="btn__icon" style="--btn--icon: url('<?= staticUrl('images/icons/chevron-right.svg') ?>')"></span>
                    </button>

                    <button type="button" class="media-gallery__next btn btn--ghost" aria-label="Next slide">
                        <span class="btn__icon" style="--btn--icon: url('<?= staticUrl('images/icons/chevron-right.svg') ?>')"></span>
                    </button>
                </div>
            <?php } ?>
        </div>

        <?php if (! empty($this->images)) { ?>
            <div class="media-gallery__swiper swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($this->images as $item) { ?>
                        <?php if (! empty($item['image'])) {
                            $image_id = $item['image']['ID'] ?? $item['image']['id'] ?? null;
                            ?>
                            <div class="media-gallery__slide swiper-slide">
                                <?= Image::make(
                                    id: $image_id,
                                    size: 'large',
                                    classes: ['media-gallery__image'],
                                    show_caption: true,
                                    show_credit: true,
                                ); ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</media-gallery>
