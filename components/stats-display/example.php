<?php

use Gust\Components\StatsDisplay;

?>

<h2>Stats Display - Default</h2>
<?= StatsDisplay::make([
    'stats' => [
        ['value' => '£351,725', 'label' => 'raised to date'],
        ['value' => '847', 'label' => 'community members'],
        ['value' => '250', 'label' => 'venue capacity'],
    ],
]); ?>

<h2>Stats Display - Single Stat</h2>
<div class="color-context-orange">
<?= StatsDisplay::make([
    'stats' => [
        ['value' => '£351,725', 'label' => 'raised to date'],
    ],
]); ?>
</div>

<h2>Stats Display - Compact</h2>
<div class="color-context-warm-black">
<?= StatsDisplay::make([
    'stats' => [
        ['value' => '£351,725', 'label' => 'raised'],
        ['value' => '847', 'label' => 'members'],
    ],
    'compact' => true,
]); ?>
</div>
