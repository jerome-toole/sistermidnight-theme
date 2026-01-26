<?php

use Geum\Components\PageNavigation;

?>

<h2>Page Navigation</h2>

<h3>Basic Usage</h3>
<?= PageNavigation::make([
    'links' => [
        ['label' => 'Our Team', 'anchor' => 'team'],
        ['label' => 'Our Manifesto', 'anchor' => 'manifesto'],
        ['label' => 'Our History', 'anchor' => 'history'],
    ],
]) ?>

<h3>With Sticky Option</h3>
<?= PageNavigation::make([
    'links' => [
        ['label' => 'Overview', 'anchor' => 'overview'],
        ['label' => 'Benefits', 'anchor' => 'benefits'],
        ['label' => 'How to Join', 'anchor' => 'join'],
        ['label' => 'FAQ', 'anchor' => 'faq'],
    ],
    'sticky' => true,
]) ?>

<h3>With Background Color</h3>
<div class="color-context-paper">
    <?= PageNavigation::make([
        'links' => [
            ['label' => 'Introduction', 'anchor' => 'intro'],
            ['label' => 'Features', 'anchor' => 'features'],
            ['label' => 'Contact', 'anchor' => 'contact'],
        ],
    ]) ?>
</div>

<h3>Many Links</h3>
<?= PageNavigation::make([
    'links' => [
        ['label' => 'Section 1', 'anchor' => 'section-1'],
        ['label' => 'Section 2', 'anchor' => 'section-2'],
        ['label' => 'Section 3', 'anchor' => 'section-3'],
        ['label' => 'Section 4', 'anchor' => 'section-4'],
        ['label' => 'Section 5', 'anchor' => 'section-5'],
        ['label' => 'Section 6', 'anchor' => 'section-6'],
    ],
]) ?>

<!-- Demo anchor targets -->
<div id="team" style="padding-top: 2rem; margin-top: 2rem; border-top: 1px solid currentColor;">
    <h3>Our Team Section</h3>
    <p>Team content here...</p>
</div>

<div id="manifesto" style="padding-top: 2rem; margin-top: 2rem; border-top: 1px solid currentColor;">
    <h3>Our Manifesto Section</h3>
    <p>Manifesto content here...</p>
</div>

<div id="history" style="padding-top: 2rem; margin-top: 2rem; border-top: 1px solid currentColor;">
    <h3>Our History Section</h3>
    <p>History content here...</p>
</div>
