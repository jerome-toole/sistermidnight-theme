<?php

namespace Gust\Components;

use Gust\ComponentBase;

/**
 * SiteFooter Component
 *
 * Dark footer: dancers illustration strip, footer nav + social icons,
 * address with map link, supporter logos and legal text. Everything is
 * read from the Footer options page and the "Footer" menu location.
 *
 * Usage:
 *   use Gust\Components\SiteFooter;
 *
 *   echo SiteFooter::make();
 */
class SiteFooter extends ComponentBase
{
    protected static string $name = 'site-footer';

    public const MENU_LOCATION = 'footer-1';

    /**
     * Create a new SiteFooter component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $classes = [],
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Transform args before rendering.
     */
    protected static function transform(array $args): array
    {
        $args['classes'] = array_merge(['site-footer', 'color-context-dark-umber'], $args['classes'] ?? []);

        $args['site_name'] = \get_bloginfo('name');
        $args['has_menu'] = \has_nav_menu(static::MENU_LOCATION);
        $args['address'] = \get_field('address', 'option');
        $args['map_link'] = \get_field('map_link', 'option');
        $args['legal_text'] = \get_field('footer_legal_text', 'option');
        $args['supporters'] = static::supporters();

        return $args;
    }

    /**
     * Supporter rows from options, keeping only those with a logo and a name.
     *
     * @return array<int, array{logo: int, name: string, url: string}>
     */
    protected static function supporters(): array
    {
        $rows = \get_field('supporters', 'option');

        if (empty($rows) || ! is_array($rows)) {
            return [];
        }

        $supporters = [];

        foreach ($rows as $row) {
            $logo = is_array($row['logo'] ?? null) ? ($row['logo']['ID'] ?? null) : ($row['logo'] ?? null);
            $name = trim((string) ($row['name'] ?? ''));

            if (empty($logo) || $name === '') {
                continue;
            }

            $supporters[] = [
                'logo' => (int) $logo,
                'name' => $name,
                'url' => (string) ($row['url'] ?? ''),
            ];
        }

        return $supporters;
    }
}
