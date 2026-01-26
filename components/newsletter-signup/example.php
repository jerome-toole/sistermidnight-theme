<?php

use Geum\Components\NewsletterSignup;

?>

<h2>Newsletter Signup - Default</h2>
<?= NewsletterSignup::make([
    'heading' => 'Stay in the loop',
]); ?>

<h2>Newsletter Signup - No Heading</h2>
<?= NewsletterSignup::make([]); ?>

<h2>Newsletter Signup - With Custom List ID</h2>
<?= NewsletterSignup::make([
    'heading' => 'Join our mailing list',
    'brevo_list_id' => 5,
]); ?>

<h2>Newsletter Signup - On Dark Background</h2>
<div class="color-context-warm-black" style="padding: var(--space-md);">
    <?= NewsletterSignup::make([
        'heading' => 'Subscribe to updates',
    ]); ?>
</div>
