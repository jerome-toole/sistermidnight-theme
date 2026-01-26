<?php

/**
 * PageHeader Component Examples
 */

use Geum\Components\PageHeader;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Default (Paper Background)</h2>
    <p class="component-example-section__description">Standard page header with paper background.</p>
    <div class="component-example-section__preview">
        <?= PageHeader::make(
            heading: 'About Us',
            show_breadcrumbs: false,
        ); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">With Subheading</h2>
    <p class="component-example-section__description">Page header with intro text.</p>
    <div class="component-example-section__preview">
        <?= PageHeader::make(
            heading: 'About Us',
            subheading: '<p>We&apos;re Sister Midnight, a community-owned music venue and radio station for Catford, Lewisham. A place that supports creativity, connection, and collective pride.</p>',
            show_breadcrumbs: false,
        ); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Dark Background</h2>
    <p class="component-example-section__description">Page header with warm-black background.</p>
    <div class="component-example-section__preview">
        <?= PageHeader::make(
            heading: 'Membership',
            subheading: 'Join us as a founding member',
            background: 'warm-black',
            show_breadcrumbs: false,
        ); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Midnight Blue Background</h2>
    <p class="component-example-section__description">Page header with midnight blue background.</p>
    <div class="component-example-section__preview">
        <?= PageHeader::make(
            heading: 'The Venue',
            subheading: 'A 250-capacity space for live music, community events, and more.',
            background: 'midnight-blue',
            show_breadcrumbs: false,
        ); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">White Background</h2>
    <p class="component-example-section__description">Page header with white background.</p>
    <div class="component-example-section__preview">
        <?= PageHeader::make(
            heading: 'Contact',
            subheading: 'Get in touch with us',
            background: 'white',
            show_breadcrumbs: false,
        ); ?>
    </div>
</section>
