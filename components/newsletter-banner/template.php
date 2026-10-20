<?php

use Gust\Components\Illustration;

/**
 * NewsletterBanner Template
 *
 * @var \Gust\Components\NewsletterBanner $this
 */
?>
<section class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?> aria-labelledby="<?= esc_attr($this->heading_id) ?>">
    <div class="newsletter-banner__inner">
        <div class="newsletter-banner__content">
            <h2 class="newsletter-banner__heading" id="<?= esc_attr($this->heading_id) ?>"><?= esc_html($this->heading) ?></h2>

            <?php if (! empty($this->text)) { ?>
                <p class="newsletter-banner__text"><?= nl2br(esc_html($this->text)) ?></p>
            <?php } ?>

            <?= $this->form; ?>
        </div>

        <?php if (! empty($this->illustration)) { ?>
            <div class="newsletter-banner__illustration">
                <?= Illustration::make(name: $this->illustration); ?>
            </div>
        <?php } ?>
    </div>
</section>
