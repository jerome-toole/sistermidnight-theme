<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <?= \Gust\Components\CookieConsent::make(); ?>
    <?= \Gust\Components\SkipLink::make(); ?>
    <?= \Gust\Components\SiteHeader::make(); ?>
