<?php

/**
 * Section Component Examples
 *
 * The block fills <InnerBlocks /> itself; here `content` is the inner HTML.
 */

use Gust\Components\Section;

$image = get_posts(['post_type' => 'attachment', 'post_mime_type' => 'image/jpeg', 'numberposts' => 1, 'fields' => 'ids']);

$aims = <<<'HTML'
<h3 class="wp-block-heading is-style-type-h4">Cultural Aims</h3>
<p>Champion local music by giving emerging artists from Lewisham and South East London opportunities to perform, grow, and be heard. Make live music more accessible by creating welcoming, inclusive spaces where more people feel able to take part, on stage and in the audience.</p>
<h3 class="wp-block-heading is-style-type-h4">Community Aims</h3>
<p>Bring people together by creating a space that feels open and welcoming, helping to reduce social isolation and strengthen community connections. Put our community at the centre of what we do, giving members and local people real opportunities to shape our direction and decisions.</p>
<div class="wp-block-buttons"><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#">Become a member</a></div></div>
HTML;

$two_column = <<<'HTML'
<div class="wp-block-group"><h3 class="wp-block-heading">Our Manifesto</h3><p>Sister Midnight is an upcoming community-owned music venue and radio station based in Catford. We’re on a mission to elevate artists around Lewisham and become a creative hub for all across SE London.</p><p>Want to help reimagine what a music venue could be? <a href="#">Become a member</a></p></div>
<div class="wp-block-group"><h3 class="wp-block-heading">Our Values</h3><p>Our core values were co-written with our members, and reflect the principles that we believe a community-owned music venue should uphold. These values form the core of our organisational ethics and will inform the choices we make throughout the lifetime of Sister Midnight.</p></div>
HTML;

$cards = <<<'HTML'
<div class="has-stone-beige-background-color has-background" style="padding: 24px"><h3 class="type-title-sm">Become a Member</h3><p>Since 2021, over 1100 members have raised £365,000 to make this happen — and now we need your help to complete the next stage of renovations.</p><p><a href="#">Read more</a></p></div>
<div class="has-stone-beige-background-color has-background" style="padding: 24px"><h3 class="type-title-sm">Stay in touch</h3><p>We send an occasional newsletter letting you know about our events.</p></div>
HTML;
?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Text right with illustration</h2>
    <p class="component-example-section__description">Vertical label, illustration in the left half, inner blocks in the right half (Objectives).</p>
    <div class="component-example-section__preview">
        <?= Section::make(label: 'Objectives', media: 'illustration', illustration: 'flames', content: $aims); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Text right with image</h2>
    <p class="component-example-section__description">440×652 image in the left half (first JPEG in the media library); media follows the text below md.</p>
    <div class="component-example-section__preview">
        <?= Section::make(label: 'Our Process', media: 'image', image: $image[0] ?? null, content: $aims); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Two column on stone-beige</h2>
    <p class="component-example-section__description">No label; two groups side by side (Manifesto / Values).</p>
    <div class="component-example-section__preview">
        <?= Section::make(layout: 'two-column', background: 'stone-beige', content: $two_column); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Scatter</h2>
    <p class="component-example-section__description">Staggered cards with three decorative illustrations (Join the party). Cards here are stand-ins until the Card block exists.</p>
    <div class="component-example-section__preview">
        <?= Section::make(label: 'Join the party', layout: 'scatter', scatter_illustrations: ['sun-wavy', 'record-heart', 'stars'], content: $cards); ?>
    </div>
</section>
