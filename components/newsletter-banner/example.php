<?php

use Gust\Components\NewsletterBanner;

?>

<h2>Newsletter Banner - Explicit</h2>
<?= NewsletterBanner::make(
    heading: 'Newsletter',
    text: 'Sign up to the Sister Midnight newsletter to hear about upcoming events and news.',
    illustration: 'trumpet',
); ?>

<h2>Newsletter Banner - Default heading, no illustration</h2>
<?= NewsletterBanner::make(
    text: "Two lines of text.\nKept as typed.",
); ?>

<h2>Newsletter Banner - From options (null on the front page or when hidden)</h2>
<?= NewsletterBanner::fromOptions(); ?>
