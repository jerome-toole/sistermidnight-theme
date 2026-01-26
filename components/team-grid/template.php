<?php

use Geum\Image;

if (empty($this->items)) {
    return;
}

$container_classes = [
    'team-grid',
    'wp-block',
    'animate',
];
?>

<section class="<?= classes($container_classes, $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <?php if (! empty($this->heading)) { ?>
        <h2 class="team-grid__heading"><?= esc_html($this->heading) ?></h2>
    <?php } ?>

    <div class="team-grid__grid">
        <?php foreach ($this->items as $index => $member) { ?>
            <div class="team-grid__item animate-element" style="--animate--key: <?= $index ?>;">
                <?php if (! empty($member['image'])) { ?>
                    <div class="team-grid__image">
                        <?= Image::picture($member['image'], 'medium_large'); ?>
                    </div>
                <?php } else { ?>
                    <div class="team-grid__image team-grid__image--placeholder"></div>
                <?php } ?>

                <div class="team-grid__content">
                    <?php if (! empty($member['name'])) { ?>
                        <h3 class="team-grid__name"><?= esc_html($member['name']); ?></h3>
                    <?php } ?>

                    <?php if (! empty($member['role'])) { ?>
                        <p class="team-grid__role"><?= esc_html($member['role']); ?></p>
                    <?php } ?>

                    <?php if (! empty($member['bio'])) { ?>
                        <div class="team-grid__bio"><?= wp_kses_post($member['bio']); ?></div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</section>
