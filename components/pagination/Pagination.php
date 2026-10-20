<?php

namespace Gust\Components;

use Gust\Component;
use Gust\ComponentBase;

/**
 * Pagination Component
 *
 * Usage:
 *   use Gust\Components\Pagination;
 *
 *   echo Pagination::make();
 */
class Pagination extends ComponentBase
{
    protected static string $name = 'pagination';

    protected static function getDefaults(): array
    {
        return [];
    }

    /**
     * Create a new Pagination component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        ?array $classes = null,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Transform args before rendering.
     */
    protected static function transform(array $args): array
    {
        $args['output'] = get_the_posts_pagination([
            'prev_text' => __('Previous page', 'gust'),
            'next_text' => __('Next page', 'gust'),
            'before_page_number' => '<span class="screen-reader-text">'.__('Page', 'gust').' </span>',
            'class' => 'pagination__inner',
        ]);

        return $args;
    }
}
