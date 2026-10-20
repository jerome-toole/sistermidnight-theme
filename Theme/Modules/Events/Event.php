<?php

namespace Theme\Modules\Events;

use DateTimeImmutable;
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

    public function url(): string
    {
        return (string) \get_permalink($this->post);
    }

    public function presenter(): string
    {
        return (string) $this->field('presenter');
    }

    public function start(): ?DateTimeImmutable
    {
        $raw = (string) ($this->field('start') ?: \get_post_meta($this->id(), PostType::START_META_KEY, true));

        if ($raw === '') {
            return null;
        }

        $start = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $raw, self::timezone());

        return $start ?: null;
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
     * "22:00" when an end time is set, otherwise the free-text label ("late").
     */
    public function endLabel(): string
    {
        $end = $this->end();

        if ($end) {
            return $end->format('H:i');
        }

        return (string) $this->field('end_label');
    }

    /**
     * "17:00 – late" or "17:00 – 22:00"; just "17:00" when nothing follows.
     */
    public function timeLabel(): string
    {
        $start = $this->start();

        if (! $start) {
            return '';
        }

        $label = $start->format('H:i');
        $end = $this->endLabel();

        return $end !== '' ? "{$label} – {$end}" : $label;
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
