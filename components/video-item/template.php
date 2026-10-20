<div class="<?= classes('video-item', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <button class="video-item__play-button js-video-item-play">
        <span class="screen-reader-text">
            <?= esc_html__('Play video', 'gust'); ?>
        </span>
    </button>

    <?php if (! empty($this->image)) { ?>
        <div class="video-item__media img-fit">
            <?= \Gust\Components\Image::make(...$this->image); ?>
        </div>
    <?php } ?>

    <div class="video-item__video">
        <div class="video-item__video-inner">

            <div class="video-item__video-wrap">
                <button class="video-item__video-close cross">
                    <span class="screen-reader-text">
                        <?= esc_html__('Close Video', 'gust') ?>
                    </span>
                </button>
                <?= $this->video ?>
            </div>
        </div>
    </div>
</div>
