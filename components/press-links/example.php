<?php

use Gust\Components\PressLinks;

?>

<h2>Press Links - Default</h2>
<?= PressLinks::make([
    'heading' => 'In the Press',
    'links' => [
        ['date' => '2025-11-15', 'publication' => 'The Guardian', 'url' => 'https://example.com/guardian'],
        ['date' => '2025-10-22', 'publication' => 'Time Out London', 'url' => 'https://example.com/timeout'],
        ['date' => '2025-09-08', 'publication' => 'Resident Advisor', 'url' => 'https://example.com/ra'],
        ['date' => '2025-08-01', 'publication' => 'Mixmag', 'url' => 'https://example.com/mixmag'],
    ],
]); ?>

<h2>Press Links - No Heading</h2>
<div class="color-context-dark-umber">
<?= PressLinks::make([
    'links' => [
        ['date' => '2025-12-01', 'publication' => 'NME', 'url' => 'https://example.com/nme'],
        ['date' => '2025-11-20', 'publication' => 'Lewisham Local', 'url' => 'https://example.com/local'],
    ],
]); ?>
</div>

<h2>Press Links - On Orange</h2>
<div class="color-context-burnt-sienna">
<?= PressLinks::make([
    'heading' => 'Press Coverage',
    'links' => [
        ['date' => '2025-06-15', 'publication' => 'BBC London', 'url' => 'https://example.com/bbc'],
        ['date' => '2025-05-10', 'publication' => 'Evening Standard', 'url' => 'https://example.com/es'],
    ],
]); ?>
</div>
