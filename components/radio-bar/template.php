<?php
/**
 * Radio Bar Template
 *
 * Marque + wordmark, the now-playing line (a live region so refreshes are
 * announced; the "Now playing:" label is hidden while off air) and the Listen
 * link to the SMFM site (standard external-link treatment via Link).
 *
 * @var \Gust\Components\RadioBar $this
 */
?>
<aside class="<?= classes($this->classes) ?>" aria-label="<?= esc_attr__('Sister Midnight FM', 'gust') ?>" <?= attributes($this->attributes) ?>>
    <p class="radio-bar__brand">
        <svg class="radio-bar__marque" viewBox="0 0 28 28" width="28" height="28" fill="currentColor" aria-hidden="true" focusable="false"><path d="M14 2a12 12 0 1 0 0 24 12 12 0 0 0 0-24Zm0 3.2a8.8 8.8 0 1 1 0 17.6 8.8 8.8 0 0 1 0-17.6Z" /><circle cx="14" cy="14" r="3" /><path d="M14 9.2a4.8 4.8 0 0 1 4.8 4.8h-2.1A2.7 2.7 0 0 0 14 11.3Z" /></svg>
        <span class="radio-bar__wordmark"><?= esc_html__('Sister Midnight FM', 'gust') ?></span>
    </p>

    <p class="radio-bar__track" aria-live="polite" data-radio-bar-track>
        <span class="radio-bar__track-label" data-radio-bar-label <?= $this->online ? '' : 'hidden' ?>><?= esc_html__('Now playing:', 'gust') ?></span>
        <span class="radio-bar__track-title" data-radio-bar-title><?= esc_html($this->track) ?></span>
    </p>

    <?= \Gust\Components\Link::make(
        content: esc_html__('Listen', 'gust'),
        url: $this->listen_url,
        target: '_blank',
        classes: ['radio-bar__listen'],
    ); ?>
</aside>
