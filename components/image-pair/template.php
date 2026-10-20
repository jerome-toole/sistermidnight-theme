<?php
/**
 * Image Pair Template
 *
 * Two images side by side (stacked below md). In an editor preview the
 * images open from an inline toolbar on the list.
 *
 * @var \Gust\Components\ImagePair $this
 */
?>
<div class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <?php if ($this->images === []) { ?>
        <p class="image-pair__placeholder" <?= $this->inlineToolbar() ?>><?= esc_html__('Add two images from the toolbar', 'gust') ?></p>
    <?php } else { ?>
        <ul class="image-pair__list" <?= $this->inlineToolbar() ?>>
            <?php foreach ($this->images as $image) { ?>
                <li class="image-pair__item"><?= $image ?></li>
            <?php } ?>
        </ul>
    <?php } ?>
</div>
