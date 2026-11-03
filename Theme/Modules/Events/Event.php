<?php

namespace Theme\Modules\Events;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use WP_Post;

/**
 * Read-only view of an event post and its ACF fields.
 */
class Event
{
    public const TICKET_LABELS = [
        'buy' => 'Buy Tickets',
        'signup' => 'Sign up',
        'free' => null,
    ];

    protected ?array $fields = null;

    /** Set on the copies withStart() makes for a repeating event's occurrences. */
    protected ?DateTimeImmutable $occurrence = null;

    protected ?DateTimeImmutable $defaultStart = null;

    protected Schedule|null|false $schedule = false;

    public function __construct(public readonly WP_Post $post) {}

    public static function fromPost(WP_Post|int|null $post = null): ?static
    {
        $post = \get_post($post);

        if (! $post instanceof WP_Post || $post->post_type !== 'event') {
            return null;
        }

        return new static($post);
    }

    public function id(): int
    {
        return $this->post->ID;
    }

    public function title(): string
    {
        return \get_the_title($this->post);
    }

    /**
     * Permalink; an occurrence of a repeating event adds its ?date=Y-m-d.
     */
    public function url(): string
    {
        $url = (string) \get_permalink($this->post);

        return $this->occurrence ? \add_query_arg('date', $this->occurrence->format('Y-m-d'), $url) : $url;
    }

    public function presenter(): string
    {
        return (string) $this->field('presenter');
    }

    /**
     * The start this event shows: the chosen occurrence (withStart()), otherwise
     * for a repeating event its next occurrence (or the last, once it has ended),
     * otherwise the start field.
     */
    public function start(): ?DateTimeImmutable
    {
        if ($this->occurrence) {
            return $this->occurrence;
        }

        $schedule = $this->schedule();

        if ($schedule) {
            return $this->defaultStart ??= $schedule->next(self::now()) ?? $schedule->last() ?? $schedule->first;
        }

        return $this->firstStart();
    }

    /**
     * The start field: for a repeating event, its first occurrence.
     */
    public function firstStart(): ?DateTimeImmutable
    {
        $raw = (string) ($this->field('start') ?: \get_post_meta($this->id(), PostType::START_META_KEY, true));

        if ($raw === '') {
            return null;
        }

        $start = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $raw, self::timezone());

        return $start ?: null;
    }

    /**
     * Repeat rule, or null for a one-off event.
     */
    public function schedule(): ?Schedule
    {
        if ($this->schedule === false) {
            $first = $this->firstStart();
            $this->schedule = $first ? Schedule::make(
                $first,
                (string) $this->field('repeat'),
                $this->field('repeat_days') ?: [],
                (string) $this->field('repeat_until'),
                $this->field('repeat_skip') ?: [],
            ) : null;
        }

        return $this->schedule;
    }

    public function isRepeating(): bool
    {
        return $this->schedule() !== null;
    }

    /**
     * A copy of this event for one occurrence.
     */
    public function withStart(DateTimeImmutable $start): static
    {
        $occurrence = clone $this;
        $occurrence->occurrence = $start;

        return $occurrence;
    }

    /**
     * Occurrences starting in [$from, $to), capped at the listing horizon.
     * A one-off event returns itself when its start is in range.
     *
     * @return static[]
     */
    public function occurrencesBetween(DateTimeInterface $from, DateTimeInterface $to): array
    {
        $schedule = $this->schedule();

        if (! $schedule) {
            $start = $this->firstStart();

            return $start && $start >= $from && $start < $to ? [$this] : [];
        }

        return array_map(
            fn (DateTimeImmutable $start) => $this->withStart($start),
            $schedule->between($from, min(DateTimeImmutable::createFromInterface($to), Schedule::horizon()))
        );
    }

    /**
     * The occurrence on a 'Y-m-d' date (e.g. from ?date=), or this event unchanged.
     */
    public function onDate(string $date): static
    {
        $start = $this->schedule()?->on($date);

        return $start ? $this->withStart($start) : $this;
    }

    /**
     * End time as a DateTimeImmutable on the start date, or null when not set.
     */
    public function end(): ?DateTimeImmutable
    {
        $start = $this->start();
        $raw = (string) $this->field('end_time');

        if (! $start || $raw === '') {
            return null;
        }

        [$hours, $minutes] = array_map('intval', explode(':', $raw) + [0, 0]);
        $end = $start->setTime($hours, $minutes);

        // An end time earlier than the start rolls over to the next day.
        return $end < $start ? $end->modify('+1 day') : $end;
    }

    /**
     * Free text shown instead of the start and end times, e.g. "19:00 till late".
     */
    public function timeOverride(): string
    {
        return trim((string) $this->field('time_override'));
    }

    /**
     * The time override, or "17:00 – 22:00"; just "17:00" without an end time.
     */
    public function timeLabel(): string
    {
        $override = $this->timeOverride();

        if ($override !== '') {
            return $override;
        }

        $start = $this->start();

        if (! $start) {
            return '';
        }

        $end = $this->end();

        return $end ? $start->format('H:i').' – '.$end->format('H:i') : $start->format('H:i');
    }

    public function dateLabel(string $format = 'D j M'): string
    {
        $start = $this->start();

        return $start ? \wp_date($format, $start->getTimestamp(), self::timezone()) : '';
    }

    /**
     * Display venue: the venue_name field, falling back to the location term.
     */
    public function venue(): string
    {
        $name = (string) $this->field('venue_name');

        if ($name !== '') {
            return $name;
        }

        $term = $this->locationTerm();

        return $term ? $term->name : '';
    }

    public function locationTerm(): ?\WP_Term
    {
        $terms = \get_the_terms($this->post, 'location');

        if (! is_array($terms) || empty($terms)) {
            return null;
        }

        return $terms[0];
    }

    public function ticketType(): string
    {
        $type = (string) $this->field('ticket_type');

        return array_key_exists($type, self::TICKET_LABELS) ? $type : 'buy';
    }

    /**
     * ACF link array (url, title, target) or null.
     */
    public function ticketLink(): ?array
    {
        $link = $this->field('ticket_link');

        return is_array($link) && ! empty($link['url']) ? $link : null;
    }

    public function ticketUrl(): string
    {
        return (string) ($this->ticketLink()['url'] ?? '');
    }

    /**
     * CTA label from ticket_type: "Buy Tickets" / "Sign up" / null for free.
     */
    public function ticketCtaLabel(): ?string
    {
        $label = self::TICKET_LABELS[$this->ticketType()];

        return $label ? \__($label, 'gust') : null;
    }

    public function hasTicketCta(): bool
    {
        return $this->ticketCtaLabel() !== null && $this->ticketUrl() !== '' && ! $this->isPast();
    }

    /**
     * A repeating event is past once it has no occurrences left.
     */
    public function isPast(): bool
    {
        $start = $this->start();

        return $start ? $start < self::now() : false;
    }

    public static function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', self::timezone());
    }

    public static function timezone(): DateTimeZone
    {
        return \wp_timezone();
    }

    protected function field(string $name): mixed
    {
        if ($this->fields === null) {
            $fields = function_exists('get_fields') ? \get_fields($this->id()) : false;
            $this->fields = is_array($fields) ? $fields : [];
        }

        return $this->fields[$name] ?? null;
    }
}
