<?php

namespace Gust\Components;

use Gust\ComponentBase;
use Theme\Modules\Events\EventRepository;

/**
 * Upcoming Events Component (ACF block `theme/upcoming-events`)
 *
 * The next N events (start >= now) as Event Cards under a vertical label.
 *
 *   grid      Front page: two staggered columns (596 / 680). An optional
 *             announcement Card sits first in the right column; without it
 *             the right column is pushed down 300px. A large decorative
 *             illustration sits bottom-left after the last card.
 *   featured  What's On: three cards in a row, the middle one offset.
 *
 * Empty state: "No upcoming events" and a link to What's On.
 *
 * Usage:
 *   use Gust\Components\UpcomingEvents;
 *
 *   echo UpcomingEvents::make();
 *   echo UpcomingEvents::make(label: '', variant: 'featured', count: 3);
 *   echo UpcomingEvents::make(announcement: ['heading' => 'Venue launch', 'text' => '<p>…</p>', 'illustration' => 'record-face']);
 */
class UpcomingEvents extends ComponentBase
{
    protected static string $name = 'upcoming-events';

    public const VARIANTS = ['grid', 'featured'];

    public const COUNTS = ['grid' => 4, 'featured' => 3];

    protected static function getDefaults(): array
    {
        return [
            'label' => 'Upcoming',
            'variant' => 'grid',
        ];
    }

    /**
     * Create a new Upcoming Events component.
     *
     * @param  string|null  $label  Vertical gutter label, rendered as the <h2>. Empty hides it.
     * @param  int|null  $count  Number of events (default 4 for grid, 3 for featured).
     * @param  string|null  $variant  One of self::VARIANTS.
     * @param  array|null  $announcement  heading, text, illustration, link (ACF link array). Hidden when the heading is empty.
     * @param  string|null  $illustration  Illustration name for the large bottom-left illustration (grid only).
     */
    public static function make(
        ?string $label = null,
        ?int $count = null,
        ?string $variant = null,
        ?array $announcement = null,
        ?string $illustration = null,
        bool $is_preview = false,
        array $classes = [],
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function transform(array $args): array
    {
        $args = static::fromBlockFields($args);

        $args['label'] = trim((string) ($args['label'] ?? ''));
        $args['attributes'] ??= [];

        if (! in_array($args['variant'], self::VARIANTS, true)) {
            $args['variant'] = 'grid';
        }

        $variant = $args['variant'];
        $count = (int) ($args['count'] ?? 0);
        $args['count'] = $count > 0 ? min($count, 12) : self::COUNTS[$variant];

        // Announcement card (grid only), hidden when the heading is empty.
        $announcement = is_array($args['announcement'] ?? null) ? static::fromGroupFields($args['announcement']) : [];
        $args['announcement'] = null;

        if ($variant === 'grid' && trim((string) ($announcement['heading'] ?? '')) !== '') {
            $args['announcement'] = Card::make(
                heading: (string) $announcement['heading'],
                text: (string) ($announcement['text'] ?? ''),
                link: is_array($announcement['link'] ?? null) ? $announcement['link'] : null,
                illustration: $announcement['illustration'] ?? null,
                size: 'sm',
                classes: ['upcoming-events__announcement'],
            );
        }

        $args['illustration'] = $variant === 'grid' && in_array($args['illustration'] ?? '', Illustration::NAMES, true)
            ? $args['illustration']
            : '';

        $args['events'] = EventRepository::upcoming($args['count']);
        $args['archive_url'] = (string) \get_post_type_archive_link('event');

        // Without a label the cards' titles are the first headings under the page <h1>.
        $args['heading_level'] = $args['label'] !== '' ? 3 : 2;

        $args['classes'] = array_merge([
            'upcoming-events',
            'upcoming-events--'.$variant,
            'alignfull',
            'has-background',
            'has-dark-umber-background-color',
        ], $args['classes'] ?? []);

        if ($args['label'] !== '' || ! empty($args['is_preview'])) {
            $args['classes'][] = 'upcoming-events--labelled';
        }

        if ($args['announcement']) {
            $args['classes'][] = 'upcoming-events--announced';
        }

        if ($args['illustration'] && $args['events']) {
            $args['classes'][] = 'upcoming-events--illustrated';
        }

        return $args;
    }

    /** Block fields are prefixed (upcoming_events_label…) so their names stay unique for inline editing. */
    protected static function fromBlockFields(array $args): array
    {
        foreach (['label', 'count', 'variant', 'announcement', 'illustration'] as $field) {
            if (array_key_exists("upcoming_events_{$field}", $args)) {
                $args[$field] = $args["upcoming_events_{$field}"] ?? $args[$field] ?? null;
                unset($args["upcoming_events_{$field}"]);
            }
        }

        return $args;
    }

    /** The announcement group's sub fields are prefixed too (upcoming_events_announcement_heading…). */
    protected static function fromGroupFields(array $group): array
    {
        $fields = [];

        foreach ($group as $key => $value) {
            $fields[str_replace('upcoming_events_announcement_', '', (string) $key)] = $value;
        }

        return $fields;
    }

    /**
     * Grid placement for the event at $index (0-based): odd events in the left
     * column, even in the right, each spanning two rows offset by one so the
     * columns stagger without JavaScript. Row 1 of the right column holds the
     * announcement (or the 300px offset).
     */
    public function placement(int $index): string
    {
        if ($this->variant !== 'grid') {
            return '';
        }

        return sprintf('--upcoming-events--column: %d; --upcoming-events--row: %d;', $index % 2 + 1, $index + 1);
    }

    /**
     * ACF inline text editing attributes for a block field (name without the
     * upcoming_events_ prefix). Empty outside an editor preview: ACF 6.8 sets
     * its "doing block preview" flag on front-end renders too.
     */
    public function inlineText(string $field, string $placeholder): string
    {
        if (! $this->is_preview || ! \function_exists('acf_inline_text_editing_attrs')) {
            return '';
        }

        return \acf_inline_text_editing_attrs("upcoming_events_{$field}", ['placeholder' => $placeholder]);
    }
}
