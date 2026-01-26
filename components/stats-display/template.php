<?php
$container_classes = [
    'stats-display',
    'wp-block',
    'animate',
];

if ($this->is_compact) {
    $container_classes[] = 'stats-display--compact';
}
?>

<div class="<?= classes($container_classes, $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="stats-display__grid">
        <?php foreach ($this->stats as $index => $stat) : ?>
            <div class="stats-display__item animate-element" style="--animate--key: <?= $index ?>;">
                <?php if (! empty($stat['value'])) : ?>
                    <span class="stats-display__value"><?= esc_html($stat['value']) ?></span>
                <?php endif; ?>
                <?php if (! empty($stat['label'])) : ?>
                    <span class="stats-display__label"><?= esc_html($stat['label']) ?></span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
