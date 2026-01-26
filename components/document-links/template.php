<?php
$container_classes = [
    'document-links',
    'wp-block',
];
?>

<div class="<?= classes($container_classes, $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <?php if (! empty($this->heading)) { ?>
        <h2 class="document-links__heading"><?= esc_html($this->heading) ?></h2>
    <?php } ?>

    <ul class="document-links__list">
        <?php foreach ($this->documents as $doc) { ?>
            <?php if (! empty($doc['url'])) { ?>
                <li class="document-links__item">
                    <a class="document-links__link" href="<?= esc_url($doc['url']) ?>" target="_blank" rel="noopener" download>
                        <span class="document-links__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14,2 14,8 20,8"/>
                                <line x1="12" y1="18" x2="12" y2="12"/>
                                <polyline points="9,15 12,18 15,15"/>
                            </svg>
                        </span>
                        <span class="document-links__content">
                            <span class="document-links__title">
                                <?= esc_html($doc['title'] ?: $doc['filename']) ?>
                            </span>
                            <?php if (! empty($doc['subtype']) || ! empty($doc['filesize'])) { ?>
                                <span class="document-links__meta">
                                    <?php
                                    $meta_parts = array_filter([$doc['subtype'], $doc['filesize']]);
                                echo esc_html(implode(' · ', $meta_parts));
                                ?>
                                </span>
                            <?php } ?>
                        </span>
                    </a>
                </li>
            <?php } ?>
        <?php } ?>
    </ul>
</div>
