<?php

namespace Theme\Modules\DuplicatePost;

class DuplicatePostModule
{
    public static function init(): void
    {
        \add_filter('post_row_actions', [__CLASS__, 'addDuplicateLink'], 10, 2);
        \add_filter('page_row_actions', [__CLASS__, 'addDuplicateLink'], 10, 2);
        \add_action('admin_action_duplicate_post', [__CLASS__, 'duplicatePost']);
    }

    public static function addDuplicateLink(array $actions, \WP_Post $post): array
    {
        if (!\current_user_can('edit_posts')) {
            return $actions;
        }

        $url = \wp_nonce_url(
            \admin_url('admin.php?action=duplicate_post&post=' . $post->ID),
            'duplicate_post_' . $post->ID
        );

        $actions['duplicate'] = '<a href="' . $url . '" title="Duplicate this item">Duplicate</a>';

        return $actions;
    }

    public static function duplicatePost(): void
    {
        $post_id = isset($_GET['post']) ? \absint($_GET['post']) : 0;

        if (!$post_id) {
            \wp_die('No post to duplicate.');
        }

        \check_admin_referer('duplicate_post_' . $post_id);

        $post = \get_post($post_id);

        if (!$post) {
            \wp_die('Post not found.');
        }

        $new_post_id = \wp_insert_post([
            'post_title'   => $post->post_title . ' (Copy)',
            'post_content' => $post->post_content,
            'post_excerpt' => $post->post_excerpt,
            'post_status'  => 'draft',
            'post_type'    => $post->post_type,
            'post_parent'  => $post->post_parent,
            'menu_order'   => $post->menu_order,
        ]);

        if (\is_wp_error($new_post_id)) {
            \wp_die('Could not duplicate post.');
        }

        // Copy all post meta
        $meta = \get_post_meta($post_id);
        foreach ($meta as $key => $values) {
            foreach ($values as $value) {
                \add_post_meta($new_post_id, $key, \maybe_unserialize($value));
            }
        }

        // Copy taxonomy terms
        $taxonomies = \get_object_taxonomies($post->post_type);
        foreach ($taxonomies as $taxonomy) {
            $terms = \wp_get_object_terms($post_id, $taxonomy, ['fields' => 'ids']);
            \wp_set_object_terms($new_post_id, $terms, $taxonomy);
        }

        \wp_safe_redirect(\admin_url('post.php?action=edit&post=' . $new_post_id));
        exit;
    }
}
