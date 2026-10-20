<?php

namespace Gust\Components;

use Gust\Component;
use Gust\ComponentBase;

/**
 * NoContent Component
 *
 * Usage:
 *   use Gust\Components\NoContent;
 *
 *   echo NoContent::make();
 */
class NoContent extends ComponentBase
{
    protected static string $name = 'no-content';

    protected static function getDefaults(): array
    {
        return [
            'content' => ['message' => __('No content found.', 'gust')],
        ];
    }

    /**
     * Create a new NoContent component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $classes = [],
        ?array $content = null,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Transform args before rendering.
     */
    protected static function transform(array $args): array
    {
        if (! empty($args['object'])) {
            $object = $args['object'];

            if ($object instanceof \WP_Query && $object->is_404()) {
                $args['content']['message'] = __("It seems we can't find what you're looking for.", 'gust');
            } elseif ($object instanceof \WP_Query && $object->is_search()) {
                $args['content']['message'] = __(
                    'Sorry, but nothing matched your search terms. Please try again with some different keywords.',
                    'gust'
                );
            }
        } elseif (is_admin()) {
            $args['content']['message'] = __('Items cannot be displayed in the editor.', 'gust');
        }

        return $args;
    }
}
