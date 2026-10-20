<?php

namespace Theme\Modules\Newsletter;

/**
 * The Brevo plugin (mailin) enqueues its form script and stylesheet in <head>
 * on every page, render blocking. Newsletter signup is a Gravity Form here, so
 * they are only kept on pages that render a Brevo form shortcode.
 */
class BrevoAssets
{
    public static function init(): void
    {
        // The plugin enqueues at priority 999.
        \add_action('wp_enqueue_scripts', [__CLASS__, 'dequeue'], 1000);
    }

    public static function dequeue(): void
    {
        if (\is_admin()) {
            return;
        }

        $post = \get_post();

        if ($post && \has_shortcode((string) $post->post_content, 'sibwp_form')) {
            return;
        }

        \wp_dequeue_script('sib-front-js');
        \wp_dequeue_style('sib-front-css');
    }
}
