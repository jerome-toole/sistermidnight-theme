<?php
/**
 * Content Test
 *
 * Tests .content-flow spacing, prose max-width, alignwide/alignfull, and
 * consecutive has-background pull-together behaviour.
 */
$lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';
$short = 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.';
?>

<section class="dev-kit__section alignfull flow">
<h2 data-dev-ui>Content Flow</h2>

<div class="content-flow content-grid alignfull">

<!-- Prose — max-width constraint -->
<h2 class="wp-block-heading">Heading 2</h2>
<h3 class="wp-block-heading">Heading 3</h3>
<p><?= $lorem ?></p>
<ul class="wp-block-list">
    <li>List item one</li>
    <li>List item two</li>
    <li>List item three — <?= $short ?></li>
</ul>
<ol class="wp-block-list">
    <li>Ordered item one</li>
    <li>Ordered item two</li>
</ol>

<!-- Standard block — flow spacing -->
<div class="wp-block-group">
    <p><?= $short ?></p>
</div>

<!-- alignwide -->
<div class="wp-block-group alignwide" style="padding: var(--space-layout); background: var(--color-brand-2); text-align: center;">
    <p><strong><code data-dev-ui>alignwide</code> block</strong></p>
</div>

<p><?= $short ?></p>

<!-- alignfull (no background) -->
<div class="wp-block-group alignfull" style="padding: var(--space-layout); text-align: center; border: 1px solid var(--dev-rule);">
    <p><strong><code data-dev-ui>alignfull</code> — no <code data-dev-ui>has-background</code></strong></p>
</div>

<p><?= $short ?></p>

<!-- Two consecutive alignfull.has-background — should touch (--flow-space: 0) -->
<div class="wp-block-group alignfull has-background has-blue-background-color">
    <div style="padding: var(--space-layout); text-align: center;">
        <p><strong>First <code data-dev-ui>alignfull.has-background</code></strong></p>
        <p><?= $short ?></p>
    </div>
</div>
<div class="wp-block-group alignfull has-background has-blue-background-color">
    <div style="padding: var(--space-layout); text-align: center;">
        <p><strong>Second <code data-dev-ui>alignfull.has-background</code> — pulled up (no gap)</strong></p>
        <p><?= $short ?></p>
    </div>
</div>

<h3><?= $short ?></h3>

<!-- Last alignfull.has-background — pulls footer up -->
<div class="wp-block-group alignfull has-background has-blue-background-color">
    <div style="padding: var(--space-layout); text-align: center;">
        <p><strong>Last <code data-dev-ui>alignfull.has-background</code> — footer pulled up</strong></p>
        <p><?= $short ?></p>
    </div>
</div>

</div><!-- /.content-flow.content-grid -->

</section><!-- /.dev-kit__section -->
