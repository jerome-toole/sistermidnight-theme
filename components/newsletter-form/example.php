<?php

use Gust\Components\NewsletterForm;

?>

<h2>Newsletter Form - From options (stone-beige)</h2>
<div class="color-context-stone-beige" style="padding: 2.5rem;">
    <?= NewsletterForm::make(); ?>
</div>

<h2>Newsletter Form - On dark-umber</h2>
<div class="color-context-dark-umber" style="padding: 2.5rem;">
    <?= NewsletterForm::make(); ?>
</div>

<h2>Newsletter Form - Missing form (renders nothing)</h2>
<?= NewsletterForm::make(form_id: 9999); ?>
