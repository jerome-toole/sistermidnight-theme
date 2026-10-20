<?php

/**
 * NewsletterForm Template
 *
 * Gravity Forms renders the <form>; the submit button is replaced with the
 * primary Button via NewsletterForm::submitButton().
 *
 * @var \Gust\Components\NewsletterForm $this
 */
?>
<div class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <?php gravity_form(
        $this->form_id,
        display_title: false,
        display_description: false,
        display_inactive: false,
        field_values: null,
        ajax: false,
        tabindex: 0,
        echo: true,
    ); ?>
</div>
