<?php

/**
 * NewsletterForm Template
 *
 * Gravity Forms renders the <form> (ids made unique on repeat renders); the submit button is replaced with the
 * primary Button via NewsletterForm::submitButton().
 *
 * @var \Gust\Components\NewsletterForm $this
 */
?>
<div class="<?= classes($this->classes) ?>" <?= attributes($this->attributes) ?>>
    <?= $this->formHtml(); ?>
</div>
