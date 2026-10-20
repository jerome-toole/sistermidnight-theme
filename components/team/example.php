<?php

/**
 * Team Component Examples
 *
 * Six members using the latest portrait images in the media library, one
 * caption in each corner, one member without a tooltip.
 */

use Gust\Components\Team;

$images = get_posts([
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'posts_per_page' => 6,
    'orderby' => 'date',
    'order' => 'DESC',
    'fields' => 'ids',
]);

$members = [
    ['first_name' => 'Alex', 'full_name' => 'Alex Morgan', 'bio' => 'Alex is Co-Founding director of Sister Midnight and looks after the venue build.', 'caption_position' => 'top-left'],
    ['first_name' => 'Keilah', 'full_name' => 'Keilah Brown', 'bio' => 'Keilah runs the radio station and the Thursday residencies.', 'caption_position' => 'bottom-right'],
    ['first_name' => 'Sophie', 'full_name' => 'Sophie Farrell', 'bio' => 'Sophie is Co-Founding director of Sister Midnight, SMFM and Social Records Society.', 'caption_position' => 'top-right'],
    ['first_name' => 'Cleo', 'full_name' => 'Cleo Adams', 'bio' => 'Cleo books the live programme and leads the community outreach.', 'caption_position' => 'bottom-left'],
    ['first_name' => 'Lenny', 'full_name' => 'Lenny Okafor', 'bio' => 'Lenny keeps the bar, the books and the members informed.', 'caption_position' => 'top-left'],
    ['first_name' => 'Dams', 'caption_position' => 'bottom-right'],
];

foreach ($members as $index => &$member) {
    $member['image'] = $images[$index] ?? null;
}
unset($member);
?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Team</h2>
    <p class="component-example-section__description">Label, six portraits in a snapping scroll row; hover, focus or click a caption for the tooltip. The last member has no tooltip.</p>
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
