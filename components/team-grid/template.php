<?php

use Gust\Components\Image;

if (empty($this->items)) {
    return;
}

?>

<section class="<?= classes('team-grid', 'wp-block', 'animate', 'section', $this->classes) ?>" data-animate data-animate-stagger="80" <?= attributes($this->attributes) ?>>
    <?php if (! empty($this->heading)) { ?>
        <div class="section__header">
            <h2 class="team-grid__heading"><?= esc_html($this->heading) ?></h2>
        </div>
    <?php } ?>

    <div class="team-grid__grid grid-simple">
        <?php foreach ($this->items as $index => $member) { ?>
            <div class="team-grid__item animate__item">
                <?php if (! empty($member['image'])) { ?>
                    <div class="team-grid__image">
                        <?= Image::make(...array_merge($member['image'], ['size' => 'medium_large'])); ?>
                    </div>
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
