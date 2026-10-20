<?php
/**
 * FAQ Template
 *
 * Native <details>/<summary>, no `name`, so several answers can be open.
 * In an editor preview the heading is always printed (placeholder) and
 * every item is open.
 *
 * @var \Gust\Components\Faq $this
 */
?>
<div class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <?php if ($this->heading !== '' || $this->is_preview) { ?>
        <h2 class="faq__heading" <?= $this->inlineText('heading', __('FAQ', 'gust')) ?>><?= esc_html($this->heading) ?></h2>
    <?php } ?>

    <div class="faq__items" <?= $this->inlineToolbar(['items'], __('Questions', 'gust')) ?>>
        <?php foreach ($this->items as $item) { ?>
            <details class="faq__item" <?= $this->open ? 'open' : '' ?>>
                <summary class="faq__question"><?= esc_html($item['question']) ?></summary>
                <?php if ($item['answer'] !== '') { ?>
                    <div class="faq__answer"><?= wp_kses_post($item['answer']) ?></div>
                <?php } ?>
            </details>
        <?php } ?>

        <?php if ($this->items === [] && $this->is_preview) { ?>
            <p class="faq__placeholder"><?= esc_html__('Add questions from the toolbar', 'gust') ?></p>
        <?php } ?>
    </div>
</div>
