<?php

namespace Gust\Components;

use Gust\ComponentBase;
use Theme\Modules\Events\EventRepository;

/**
 * Upcoming Events Component (ACF block `theme/upcoming-events`)
 *
 * The next N events (start >= now) as Event Cards under a vertical label.
 *
 *   grid      Front page: one date-ordered list shown as two staggered
 *             columns (596 / 680) from md, each packed independently so the
 *             gaps within a column stay even. An optional announcement Card
 *             comes first and sits at the top of the right column; without
 *             it the right column is pushed down 300px. A large decorative
 *             illustration sits bottom-left after the last card.
 *   featured  What's On: three cards in a row, the middle one offset.
 *
 * The label is an <h2> by default; What's On passes label_level 1 so it is
 * the page <h1> and the cards become <h2>s.
 *
 * Empty state: "No upcoming events" and a link to What's On.
 *
 * Usage:
 *   use Gust\Components\UpcomingEvents;
 *
 *   echo UpcomingEvents::make();
 *   echo UpcomingEvents::make(label: "What's On", label_level: 1, variant: 'featured', count: 3);
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
            'label_level' => 2,
            'variant' => 'grid',
        ];
    }

    /**
     * Create a new Upcoming Events component.
     *
     * @param  string|null  $label  Vertical gutter label, rendered as the <h2> (see $label_level). Empty hides it.
     * @param  int|null  $label_level  Heading level of the label, 1–3 (default 2). Cards use the level below.
     * @param  int|null  $count  Number of events (default 4 for grid, 3 for featured).
     * @param  string|null  $variant  One of self::VARIANTS.
     * @param  array|null  $announcement  heading, text, illustration, link (ACF link array). Hidden when the heading is empty.
     * @param  string|null  $illustration  Illustration name for the large bottom-left illustration (grid only).
     * @param  bool  $eager_first  Load the first card's poster eagerly (when the block is the page's LCP, e.g. What's On).
     */
    public static function make(
        ?string $label = null,
        ?int $label_level = null,
        ?int $count = null,
        ?string $variant = null,
        ?array $announcement = null,
        ?string $illustration = null,
        bool $eager_first = false,
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

        // Cards sit one level under the label; without a label their titles are
        // the first headings under the page <h1>.
        $args['label_level'] = max(1, min(3, (int) ($args['label_level'] ?? 2)));
        $args['label_tag'] = 'h'.$args['label_level'];
        $args['heading_level'] = $args['label'] !== '' ? $args['label_level'] + 1 : 2;

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
     * The list items in DOM order: the announcement card first (grid only),
     * then the events in date order. The grid variant's two staggered
     * columns are CSS only (floats from md), so Tab and reading order
     * always follow the dates.
     *
     * @return array<int, array{classes: array<string>, html: string}>
     */
    public function items(): array
    {
        $items = [];

        if ($this->variant === 'grid' && $this->announcement) {
            $items[] = [
                'classes' => ['upcoming-events__item--announcement'],
                'html' => (string) $this->announcement,
            ];
        }

        foreach ($this->events as $index => $event) {
            $items[] = [
                'classes' => [],
                'html' => (string) EventCard::make(
                    event: $event,
                    size: $this->variant === 'grid' ? 'lg' : 'sm',
                    heading_level: $this->heading_level,
                    eager: $this->eager_first && $index === 0,
                    classes: ['upcoming-events__card'],
                ),
            ];
        }

        return $items;
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
