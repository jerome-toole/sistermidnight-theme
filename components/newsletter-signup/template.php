<?php
$container_classes = [
    'newsletter-signup',
    'wp-block',
];
?>

<div
    class="<?= classes($container_classes, $this->classes) ?>"
    <?= attributes($this->attributes) ?>
    data-newsletter-signup
    data-endpoint="<?= esc_attr($this->endpoint) ?>"
    data-nonce="<?= esc_attr($this->nonce) ?>"
    <?php if (! empty($this->brevo_list_id)) { ?>
        data-list-id="<?= esc_attr($this->brevo_list_id) ?>"
    <?php } ?>
>
    <?php if (! empty($this->heading)) { ?>
        <h2 class="newsletter-signup__heading"><?= esc_html($this->heading) ?></h2>
    <?php } ?>

    <form class="newsletter-signup__form" novalidate>
        <label class="newsletter-signup__input-group input-group input--button-height">
            <span class="screen-reader-text"><?= esc_html__('Email address', 'theme') ?></span>
            <input
                type="email"
                name="email"
                placeholder="<?= esc_attr__('Enter your email', 'theme') ?>"
                required
                autocomplete="email"
            >
            <button type="submit" class="btn btn--arrow">
                <?= esc_html__('Subscribe', 'theme') ?>
            </button>
        </label>
    </form>

    <div class="newsletter-signup__message" role="status" aria-live="polite" hidden></div>
</div>
