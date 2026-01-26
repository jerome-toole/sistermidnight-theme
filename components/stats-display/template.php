<?php
$target = ! empty($this->target) ? floatval($this->target) : 0;
$numeric_value = floatval(preg_replace('/[^0-9.]/', '', $this->value ?? '0'));
$percentage = $target > 0 ? min(100, ($numeric_value / $target) * 100) : 85;
?>

<div class="<?= classes(['stats-display', 'wp-block', 'animate'], $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="stats-display__circle" style="--ring-percent: <?= $percentage ?>;">
        <svg class="stats-display__ring" viewBox="0 0 200 200">
            <circle class="stats-display__ring-bg" cx="100" cy="100" r="90" />
            <circle class="stats-display__ring-progress" cx="100" cy="100" r="90" />
        </svg>
        <div class="stats-display__content">
            <?php if (! empty($this->value)) { ?>
                <span class="stats-display__value"><?= esc_html($this->value) ?></span>
            <?php } ?>
            <?php if (! empty($this->label)) { ?>
                <span class="stats-display__label"><?= esc_html($this->label) ?></span>
            <?php } ?>
        </div>
    </div>
</div>
