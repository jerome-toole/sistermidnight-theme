<?php
/**
 * Radio Bar Template
 *
 * Marque + wordmark (inline Figma SVGs, aria-hidden, with a visually hidden
 * "Sister Midnight FM"), the now-playing line (a live region so refreshes are
 * announced; the "Now playing:" label is hidden while off air) and the Listen
 * link to the SMFM site (standard external-link treatment via Link).
 *
 * @var \Gust\Components\RadioBar $this
 */
?>
<aside class="<?= classes($this->classes) ?>" aria-label="<?= esc_attr__('Sister Midnight FM', 'gust') ?>" <?= attributes($this->attributes) ?>>
    <p class="radio-bar__brand">
        <?= \Gust\Components\RadioBar::brandSvg('smfm-marque', ['radio-bar__marque']); ?>
        <?= \Gust\Components\RadioBar::brandSvg('smfm-logotype', ['radio-bar__wordmark']); ?>
        <span class="screen-reader-text"><?= esc_html__('Sister Midnight FM', 'gust') ?></span>
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
