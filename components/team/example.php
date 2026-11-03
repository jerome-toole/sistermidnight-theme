<?php

/**
 * Team Component Examples
 *
 * Five members using the design's portraits (by file name) when they are in
 * the media library, otherwise the latest images; captions placed and turned
 * as in the design, one member without a tooltip.
 */

use Gust\Components\Team;

$members = [
    ['first_name' => 'Alex', 'name_x' => 11.9, 'name_y' => 49.2, 'name_rotation' => -75.6],
    ['first_name' => 'Keilah', 'name_x' => 89.9, 'name_y' => 84.1, 'name_rotation' => 38.2],
    ['first_name' => 'Sophie', 'full_name' => 'Sophie Farrell', 'bio' => 'Sophie is Co-Founding director of Sister Midnight, SMFM and Social Records Society.', 'name_x' => 27.2, 'name_y' => 32.3, 'name_rotation' => -30],
    ['first_name' => 'Cleo', 'name_x' => 85.2, 'name_y' => 41.3, 'name_rotation' => 65.6],
    ['first_name' => 'Lenny', 'full_name' => 'Lenny Watson', 'bio' => 'Lenny is a co-founder of Sister Midnight.', 'name_x' => 33.8, 'name_y' => 7.6, 'name_rotation' => -26.2],
];

$fallback = get_posts([
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'posts_per_page' => count($members),
    'orderby' => 'date',
    'order' => 'DESC',
    'fields' => 'ids',
]);

foreach ($members as $index => &$member) {
    $portrait = get_posts([
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'name' => strtolower($member['first_name']),
        'posts_per_page' => 1,
        'fields' => 'ids',
    ]);

    $member['image'] = $portrait[0] ?? $fallback[$index] ?? null;
}
unset($member);
?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Team</h2>
    <p class="component-example-section__description">Label, five portraits in a snapping scroll row; hover a portrait for the panel at the cursor, focus or tap a caption for it anchored to the portrait. The last member has no panel.</p>
    <div class="component-example-section__preview">
        <?= Team::make(label: 'The Team', members: $members); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Team, no label</h2>
    <div class="component-example-section__preview">
        <?= Team::make(members: array_slice($members, 0, 3)); ?>
    </div>
</section>
