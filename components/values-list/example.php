<?php

use Gust\Components\ValuesList;

$example_values = [
    [
        'title' => 'Community First',
        'description' => 'We believe in the power of community ownership. Sister Midnight is built by and for the people of Lewisham.',
    ],
    [
        'title' => 'Creative Freedom',
        'description' => 'We provide a space for artists to experiment, take risks, and push boundaries without commercial pressure.',
    ],
    [
        'title' => 'Radical Inclusion',
        'description' => 'Everyone is welcome here. We actively work to remove barriers and create accessible spaces for all.',
    ],
    [
        'title' => 'Local Roots',
        'description' => 'We champion South London talent and provide a platform for emerging artists from our community.',
    ],
];

?>

<section class="dev-section">
    <h2>Values List - Default</h2>
    <?= ValuesList::make([
        'heading' => 'Our Manifesto',
        'intro' => 'These are the principles that guide everything we do at Sister Midnight.',
        'values' => $example_values,
    ]) ?>
</section>

<section class="dev-section color-context-warm-black">
    <h2>Values List - Dark Background</h2>
    <?= ValuesList::make([
        'heading' => 'Our Values',
        'values' => array_slice($example_values, 0, 2),
    ]) ?>
</section>

<section class="dev-section">
    <h2>Values List - No Heading</h2>
    <?= ValuesList::make([
        'values' => $example_values,
    ]) ?>
</section>
