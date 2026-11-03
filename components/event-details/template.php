<?php
/**
 * Event Details Template (editor preview only)
 *
 * Text values are contenteditable, so they hold the raw value only; ACF
 * shows the placeholder when empty. Clicks outside the values open the
 * expanded editor (editor-scripts.js).
 *
 * @var \Gust\Components\EventDetails $this
 */
?>
<div class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="event-details__header">
        <p class="event-details__heading"><?= esc_html__('Event details', 'gust') ?></p>
        <span class="event-details__edit-all"><?= esc_html__('Edit all details', 'gust') ?></span>
    </div>

    <dl class="event-details__list">
        <?php foreach ($this->rows as $row) { ?>
            <div class="event-details__row <?= ! empty($row['inactive']) ? 'is-inactive' : '' ?>">
                <dt class="event-details__label"><?= esc_html($row['label']) ?></dt>
                <dd class="event-details__value">
                    <?php if (! empty($row['text'])) { ?>
                        <span class="event-details__input" <?= $this->inlineAttributes($row) ?>><?= esc_html($row['value']) ?></span>
                    <?php } else { ?>
                        <span class="event-details__input <?= $row['value'] === '' ? 'is-empty' : '' ?>" <?= $this->inlineAttributes($row) ?>><?= esc_html($row['value'] !== '' ? $row['value'] : __('Not set', 'gust')) ?></span>
                    <?php } ?>
                    <?php if (! empty($row['inactive'])) { ?>
                        <span class="event-details__note"><?= esc_html($row['inactive']) ?></span>
                    <?php } ?>
                </dd>
            </div>
        <?php } ?>
    </dl>

    <p class="event-details__hint">
        <?php if ($this->missing_start) { ?>
            <strong><?= esc_html__('Add a start date before publishing.', 'gust') ?></strong>
        <?php } ?>
        <?= esc_html__('Click a value to edit it, or anywhere else in this box to edit all details.', 'gust') ?>
    </p>
</div>
