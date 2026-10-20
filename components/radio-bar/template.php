<?php
/**
 * Radio Bar Template
 *
 * Marque + wordmark, the now-playing line (hidden while the station is
 * offline; a live region so refreshes are announced) and the Listen link.
 *
 * @var \Gust\Components\RadioBar $this
 */
?>
<aside class="<?= classes($this->classes) ?>" aria-label="<?= esc_attr__('Sister Midnight FM', 'gust') ?>" <?= attributes($this->attributes) ?>>
    <p class="radio-bar__brand">
        <svg class="radio-bar__marque" viewBox="0 0 28 28" width="28" height="28" fill="currentColor" aria-hidden="true" focusable="false"><path d="M14 2a12 12 0 1 0 0 24 12 12 0 0 0 0-24Zm0 3.2a8.8 8.8 0 1 1 0 17.6 8.8 8.8 0 0 1 0-17.6Z" /><circle cx="14" cy="14" r="3" /><path d="M14 9.2a4.8 4.8 0 0 1 4.8 4.8h-2.1A2.7 2.7 0 0 0 14 11.3Z" /></svg>
        <span class="radio-bar__wordmark"><?= esc_html__('Sister Midnight FM', 'gust') ?></span>
    </p>

    <p class="radio-bar__track" aria-live="polite" data-radio-bar-track <?= $this->online ? '' : 'hidden' ?>>
        <span class="radio-bar__track-label"><?= esc_html__('Now playing:', 'gust') ?></span>
        <span class="radio-bar__track-title" data-radio-bar-title><?= esc_html($this->track) ?></span>
    </p>

    <a class="radio-bar__listen" href="<?= esc_url($this->listen_url) ?>" target="_blank" rel="noopener">
        <?= esc_html__('Listen', 'gust') ?>
        <span class="screen-reader-text"><?= esc_html__('(opens in new tab)', 'gust') ?></span>
        <svg class="radio-bar__arrow" viewBox="0 0 16 16" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M3 13 13 3M5 3h8v8" /></svg>
    </a>
</aside>
