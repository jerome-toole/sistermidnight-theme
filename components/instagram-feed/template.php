<?php

use Geum\Components\Image;

$container_classes = [
    'instagram-feed',
    'wp-block',
];
?>

<div class="<?= classes($container_classes, $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <div class="instagram-feed__grid">
        <?php foreach ($this->posts as $post) {
            $image = $post['image'] ?? null;
            $url = $post['url'] ?? null;
            $caption = $post['caption'] ?? '';

            if (empty($image)) {
                continue;
            }

            $image_id = $image['ID'] ?? $image['id'] ?? null;
            ?>
            <a
                class="instagram-feed__item"
                href="<?= esc_url($url) ?>"
                target="_blank"
                rel="noopener"
                <?php if (! empty($caption)) { ?>
                    title="<?= esc_attr($caption) ?>"
                <?php } ?>
            >
                <?= Image::make(
                    id: $image_id,
                    size: 'medium',
                    classes: ['instagram-feed__image'],
                ); ?>
            </a>
        <?php } ?>
    </div>

    <?php if (! empty($this->profile_url)) { ?>
        <a class="instagram-feed__link" href="<?= esc_url($this->profile_url) ?>" target="_blank" rel="noopener">
            <?= esc_html__('see more on insta', 'geum') ?> →
        </a>
    <?php } ?>
</div>
