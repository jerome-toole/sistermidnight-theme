<?php
/**
 * Hero Template
 *
 * Poster image first (LCP), then the optional video and its toggle. The
 * <video> has no poster attribute: the <img> is the poster, so the browser
 * does not download the full-size image a second time. The toggle starts
 * hidden and is revealed by scripts.js once it can control the video;
 * aria-pressed="true" means the video is paused.
 *
 * @var \Gust\Components\Hero $this
 */
?>
<div class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <?= wp_get_attachment_image($this->poster_id, 'full', false, [
        'class' => 'hero__poster',
        'sizes' => '100vw',
        'fetchpriority' => 'high',
        'loading' => 'eager',
        'decoding' => 'async',
    ]); ?>

    <?php if ($this->video_url) { ?>
        <video class="hero__video" muted loop playsinline disablepictureinpicture preload="metadata" aria-hidden="true" tabindex="-1" data-hero-video>
            <source src="<?= esc_url($this->video_url) ?>" type="<?= esc_attr($this->video_type) ?>">
        </video>

        <button class="hero__toggle" type="button" aria-pressed="true" hidden data-hero-toggle>
            <span class="screen-reader-text"><?= esc_html__('Pause video', 'gust') ?></span>
            <svg class="hero__icon hero__icon--pause" viewBox="0 0 24 24" width="24" height="24" fill="currentColor" aria-hidden="true" focusable="false"><rect x="5" y="4" width="5" height="16" /><rect x="14" y="4" width="5" height="16" /></svg>
            <svg class="hero__icon hero__icon--play" viewBox="0 0 24 24" width="24" height="24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M7 4l13 8-13 8z" /></svg>
        </button>
    <?php } ?>
</div>
