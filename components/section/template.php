<?php

use Gust\Components\Illustration;

/**
 * Section Template
 *
 * <InnerBlocks /> is replaced by ACF: with the editor's block list in a
 * preview, with the saved inner HTML on the front end. When `content` is set
 * (examples, non-block use) the same wrapper is printed with that HTML.
 *
 * @var \Gust\Components\Section $this
 */
// In the editor the label is always rendered (inline-editable placeholder).
$labelled = $this->label !== '' || $this->is_preview;

$content_classes = ['content-section__content', 'content-section__content--'.$this->layout];

if ($this->layout === 'text-right') {
    $content_classes[] = 'content-layout__text';
}

$inner_blocks = function () use ($content_classes) {
    if ($this->content !== null) {
        return '<div class="'.classes($content_classes).'">'.$this->content.'</div>';
    }

    return sprintf(
        '<InnerBlocks class="%s" allowedBlocks="%s" template="%s" templateLock="false" />',
        classes($content_classes),
        esc_attr(wp_json_encode($this->allowed_blocks)),
        esc_attr(wp_json_encode($this->template)),
    );
};
?>
<section class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="content-section__inner">
        <?php if ($labelled) { ?>
            <div class="content-section__label-track">
                <h2 class="content-section__label" <?= $this->inlineText('label', __('Section label', 'gust')) ?>><?= esc_html($this->label) ?></h2>
            </div>
        <?php } ?>

        <?php if ($this->layout === 'text-right') { ?>
            <div class="<?= classes(['content-section__body', 'content-layout', $labelled ? 'content-layout--beside-label' : '']) ?>">
                <?php if ($this->illustration || $this->image) { ?>
                    <div class="content-section__media content-layout__media content-layout__media--always" <?= $this->inlineToolbar(['media', 'illustration', 'image'], __('Media', 'gust')) ?>>
                        <?php if ($this->illustration) { ?>
                            <?= Illustration::make(name: $this->illustration, classes: ['content-section__illustration']); ?>
                        <?php } else { ?>
                            <?= $this->image; ?>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?= $inner_blocks(); ?>
            </div>
        <?php } elseif ($this->layout === 'scatter') { ?>
            <div class="content-section__body content-section__body--scatter">
                <?php if ($this->scatter_illustrations) { ?>
                    <div class="content-section__scatter" aria-hidden="true">
                        <?php foreach ($this->scatter_illustrations as $name) { ?>
                            <?= Illustration::make(name: $name, classes: ['content-section__scatter-illustration']); ?>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?= $inner_blocks(); ?>
            </div>
        <?php } else { ?>
            <div class="content-section__body">
                <?= $inner_blocks(); ?>
            </div>
        <?php } ?>
    </div>
</section>
