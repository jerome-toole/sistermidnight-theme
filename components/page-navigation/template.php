<?php
$container_classes = [
    'page-navigation',
    'wp-block',
];

if (! empty($this->sticky)) {
    $container_classes[] = 'page-navigation--sticky';
}
?>

<nav class="<?= classes($container_classes, $this->classes) ?>" <?= attributes($this->attributes) ?> aria-label="Page navigation">
    <ul class="page-navigation__list">
        <?php foreach ($this->links as $link) { ?>
            <?php if (! empty($link['anchor']) && ! empty($link['label'])) { ?>
                <li class="page-navigation__item">
                    <a class="page-navigation__link" href="<?= esc_attr($link['anchor']) ?>">
                        <?= esc_html($link['label']) ?>
                    </a>
                </li>
            <?php } ?>
        <?php } ?>
    </ul>
</nav>
