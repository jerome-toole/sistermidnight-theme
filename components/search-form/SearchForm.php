<?php

namespace Gust\Components;

use Gust\ComponentBase;

/**
 * SearchForm Component
 *
 * Native GET search form: labelled input in the underline style plus the
 * primary "Search" button. Colours come from the surrounding colour context.
 *
 * Usage:
 *   use Gust\Components\SearchForm;
 *
 *   echo SearchForm::make();                       // pre-filled with the current query
 *   echo SearchForm::make(value: 'choir');
 */
class SearchForm extends ComponentBase
{
    protected static string $name = 'search-form';

    /**
     * Create a new SearchForm component.
     *
     * @param  string|null  $value  Input value; defaults to the current search query.
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        ?string $value = null,
        array $classes = [],
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function transform(array $args): array
    {
        $args['value'] = $args['value'] ?? \get_search_query();
        $args['input_id'] = \wp_unique_id('search-form-input-');
        $args['classes'] = array_merge(['search-form'], $args['classes'] ?? []);

        return $args;
    }
}
