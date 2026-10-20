<?php

if (empty($this->items)) {
    return;
}

$container_classes = [
    'values-list',
    'wp-block',
    'animate',
];
?>

<section class="<?= classes($container_classes, $this->classes) ?>" data-animate data-animate-stagger="80" <?= attributes($this->attributes) ?>>
    <?php if (! empty($this->heading)) { ?>
        <h2 class="values-list__heading"><?= esc_html($this->heading) ?></h2>
    <?php } ?>

    <?php if (! empty($this->intro)) { ?>
        <div class="values-list__intro"><?= wp_kses_post($this->intro) ?></div>
    <?php } ?>

    <ol class="values-list__items">
        <?php foreach ($this->items as $index => $item) { ?>
            <li class="values-list__item animate__item">
                <span class="values-list__number"><?= esc_html($item['number']) ?></span>

                <div class="values-list__content">
                    <?php if (! empty($item['title'])) { ?>
                        <h3 class="values-list__title"><?= esc_html($item['title']) ?></h3>
                    <?php } ?>

                    <?php if (! empty($item['description'])) { ?>
                        <div class="values-list__description"><?= wp_kses_post($item['description']) ?></div>
                    <?php } ?>
                </div>
            </li>
        <?php } ?>
    </ol>
</section>
