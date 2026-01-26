<?php
$container_classes = [
    'press-links',
    'wp-block',
];
?>

<div class="<?= classes($container_classes, $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <?php if (! empty($this->heading)) { ?>
        <h2 class="press-links__heading"><?= esc_html($this->heading) ?></h2>
    <?php } ?>

    <ul class="press-links__list">
        <?php foreach ($this->links as $link) { ?>
            <li class="press-links__item">
                <?php if (! empty($link['date'])) { ?>
                    <time class="press-links__date" datetime="<?= esc_attr($link['date']) ?>">
                        <?= esc_html(date_i18n('j M Y', strtotime($link['date']))) ?>
                    </time>
                <?php } ?>
                <a class="press-links__link" href="<?= esc_url($link['url']) ?>" target="_blank" rel="noopener">
                    <?= esc_html($link['publication']) ?>
                </a>
            </li>
        <?php } ?>
    </ul>
</div>
