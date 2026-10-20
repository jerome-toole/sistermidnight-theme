
        <?= \Gust\Components\NewsletterBanner::fromOptions(
            classes: is_singular('post') ? ['newsletter-banner--dark-umber'] : [],
        ); ?>
        <?= \Gust\Components\SiteFooter::make(); ?>
        <?php wp_footer(); ?>
    </body>
</html>
