<?php

namespace Gust\Components;

use DateTimeImmutable;
use Gust\ComponentBase;
use Theme\Modules\Events\Event;

/**
 * Event Details Component (ACF block `theme/event-details`)
 *
 * The event's structured fields (presenter, start, end, venue, tickets),
 * edited through a block pinned to the top of every event. The block uses
 * ACF's usePostMeta, so the values are saved to post meta under the same
 * keys as before (`start`, `venue_name`…) rather than in the post content,
 * and Event, the archive queries and the feed read them unchanged.
 *
 * In the editor it renders the values as a WordPress-admin-style panel (see
 * _editor-styles.pcss). Each value is edited in place with ACF inline
 * editing; clicking anywhere else in the panel opens ACF's expanded editor
 * with every field (editor-scripts.js). Fields are hidden in the sidebar.
 * On the front end it renders nothing: Event Header shows the data.
 *
 * The post type template inserts the block locked in place, and
 * Theme\Modules\Events\EventDetailsBlock adds it to events created before.
 */
class EventDetails extends ComponentBase
{
    protected static string $name = 'event-details';

    /**
     * Create a new Event Details component.
     *
     * @param  bool  $is_preview  Editor preview; the component renders nothing otherwise.
     */
    public static function make(
        bool $is_preview = false,
        array $classes = [],
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function validate(array $args): bool
    {
        return ! empty($args['is_preview']);
    }

    protected static function transform(array $args): array
    {
        $start = static::parseStart((string) ($args['start'] ?? ''));
        $end_time = trim((string) ($args['end_time'] ?? ''));
        $ticket_type = (string) ($args['ticket_type'] ?? '');
        $ticket_type = array_key_exists($ticket_type, Event::TICKET_LABELS) ? $ticket_type : 'buy';
        $ticket_link = is_array($args['ticket_link'] ?? null) ? $args['ticket_link'] : [];
        $ticket_choices = \function_exists('acf_get_field') ? (acf_get_field('field_event_ticket_type')['choices'] ?? []) : [];

        // One row per field, in field group order. Text fields are typed in
        // place; the rest open in ACF's inline toolbar popover. `inactive`
        // notes a field that is ignored because of another one.
        $args['rows'] = [
            [
                'label' => __('Presenter', 'gust'),
                'value' => trim((string) ($args['presenter'] ?? '')),
                'text' => 'presenter',
                'placeholder' => __('Sister Midnight Presents', 'gust'),
            ],
            [
                'label' => __('Start', 'gust'),
                'value' => $start ? \wp_date('l j F Y, H:i', $start->getTimestamp(), Event::timezone()) : '',
                'toolbar' => 'start',
            ],
            [
                'label' => __('End time', 'gust'),
                'value' => $end_time,
                'toolbar' => 'end_time',
            ],
            [
                'label' => __('End label', 'gust'),
                'value' => trim((string) ($args['end_label'] ?? '')),
                'text' => 'end_label',
                'placeholder' => __('late', 'gust'),
                'inactive' => $end_time !== '' ? __('Not shown while an end time is set', 'gust') : '',
            ],
            [
                'label' => __('Venue name', 'gust'),
                'value' => trim((string) ($args['venue_name'] ?? '')),
                'text' => 'venue_name',
                'placeholder' => __('Falls back to the location', 'gust'),
            ],
            [
                'label' => __('Ticket type', 'gust'),
                'value' => (string) ($ticket_choices[$ticket_type] ?? $ticket_type),
                'toolbar' => 'ticket_type',
            ],
            [
                'label' => __('Ticket link', 'gust'),
                'value' => (string) ($ticket_link['url'] ?? ''),
                'toolbar' => 'ticket_link',
                'inactive' => $ticket_type === 'free' ? __('Not used for free events', 'gust') : '',
            ],
        ];
        $args['missing_start'] = $start === null;
        $args['classes'] = array_merge(['event-details'], $args['classes'] ?? []);
        $args['attributes'] ??= [];

        return $args;
    }

    /** ACF's date_time_picker returns "Y-m-d H:i:s"; the expanded editor preview can send "Ymd His". */
    protected static function parseStart(string $raw): ?DateTimeImmutable
    {
        foreach (['Y-m-d H:i:s', 'Ymd His'] as $format) {
            $start = DateTimeImmutable::createFromFormat($format, $raw, Event::timezone());

            if ($start) {
                return $start;
            }
        }

        return null;
    }

    /** ACF inline editing attributes for a row: contenteditable for text fields, otherwise the inline toolbar. */
    public function inlineAttributes(array $row): string
    {
        if (! empty($row['text']) && \function_exists('acf_inline_text_editing_attrs')) {
            return \acf_inline_text_editing_attrs($row['text'], ['placeholder' => $row['placeholder'] ?? null]);
        }

        if (! empty($row['toolbar']) && \function_exists('acf_inline_toolbar_editing_attrs')) {
            return (string) \acf_inline_toolbar_editing_attrs([$row['toolbar']], ['toolbar_title' => $row['label']]);
        }

        return '';
    }
}
