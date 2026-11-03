<?php

namespace Theme\Modules\Events;

use WP_Post;
use WP_REST_Request;
use WP_REST_Response;

/**
 * The Event Details block (`theme/event-details`) edits the event's fields.
 * It sits first in the post type template, locked so it can't be moved or
 * removed. Events created before the block existed get it prepended when
 * the editor loads them, and keep it once saved.
 */
class EventDetailsBlock
{
    public const NAME = 'theme/event-details';

    public static function init(): void
    {
        \add_filter('rest_prepare_'.PostType::SLUG, [__CLASS__, 'prependToEditorContent'], 10, 3);
        \add_action('enqueue_block_editor_assets', [__CLASS__, 'enqueuePopoverStyles']);
    }

    /** Styles for the block's inline editing popovers, which render outside the canvas iframe. */
    public static function enqueuePopoverStyles(): void
    {
        if (\get_current_screen()?->post_type === PostType::SLUG) {
            \Gust\Vite::enqueueStyle('event-details-popover', 'components/event-details/event-details-popover.pcss');
        }
    }

    /** Block template entry for register_post_type(). */
    public static function templateEntry(): array
    {
        return [self::NAME, self::attributes()];
    }

    public static function attributes(): array
    {
        return [
            'name' => self::NAME,
            'mode' => 'preview',
            'lock' => ['move' => true, 'remove' => true],
        ];
    }

    public static function prependToEditorContent(WP_REST_Response $response, WP_Post $post, WP_REST_Request $request): WP_REST_Response
    {
        $data = $response->get_data();

        if ($request['context'] !== 'edit' || ! isset($data['content']['raw']) || \has_block(self::NAME, $post)) {
            return $response;
        }

        $block = \serialize_block([
            'blockName' => self::NAME,
            'attrs' => self::attributes(),
            'innerBlocks' => [],
            'innerHTML' => '',
            'innerContent' => [],
        ]);

        $data['content']['raw'] = $block."\n\n".$data['content']['raw'];
        $response->set_data($data);

        return $response;
    }
}
