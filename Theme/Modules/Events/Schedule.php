<?php

namespace Theme\Modules\Events;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Repeat rule for an event: weekly or fortnightly on chosen weekdays, or
 * monthly on the first event's weekday position ("first Friday", "last Sunday").
 * Occurrences are worked out on the fly from the first start, at its time of
 * day, and never stored. Listings only show them up to HORIZON_MONTHS ahead.
 */
class Schedule
{
    public const FREQUENCIES = [
        'weekly' => 'Weekly',
        'fortnightly' => 'Every two weeks',
        'monthly' => 'Monthly',
    ];

    public const DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    /** How far ahead the calendar, list and feed show repeating events. */
    public const HORIZON_MONTHS = 2;

    protected const ORDINALS = [1 => 'first', 2 => 'second', 3 => 'third', 4 => 'fourth'];

    /**
     * @param  string[]  $days  Weekday names (weekly/fortnightly); empty means the first start's weekday.
     * @param  string[]  $skip  'Y-m-d' dates without an occurrence.
     */
    public function __construct(
        public readonly DateTimeImmutable $first,
        public readonly string $frequency,
        public readonly array $days = [],
        public readonly ?DateTimeImmutable $until = null,
        public readonly array $skip = [],
    ) {}

    /**
     * Null when the frequency isn't a known one (the event doesn't repeat).
     *
     * @param  mixed  $skip  ACF repeater rows (['date' => 'Y-m-d']) or plain dates.
     */
    public static function make(DateTimeImmutable $first, string $frequency, mixed $days = [], string $until = '', mixed $skip = []): ?static
    {
        if (! array_key_exists($frequency, self::FREQUENCIES)) {
            return null;
        }

        $days = array_values(array_intersect(self::DAYS, (array) $days));
        $until = $until !== '' ? DateTimeImmutable::createFromFormat('!Y-m-d', $until, $first->getTimezone()) : null;
        $skip = array_values(array_filter(array_map(
            fn ($row) => is_array($row) ? (string) ($row['date'] ?? '') : (string) $row,
            is_array($skip) ? $skip : []
        )));

        return new static($first, $frequency, $days, $until ?: null, $skip);
    }

    /** Latest start a listing shows. */
    public static function horizon(): DateTimeImmutable
    {
        return Event::now()->modify('+'.self::HORIZON_MONTHS.' months');
    }

    /**
     * Occurrence starts with $from <= start < $to, in order.
     *
     * @return DateTimeImmutable[]
     */
    public function between(DateTimeInterface $from, DateTimeInterface $to): array
    {
        $timezone = $this->first->getTimezone();
        $from = DateTimeImmutable::createFromInterface($from)->setTimezone($timezone);
        $to = DateTimeImmutable::createFromInterface($to)->setTimezone($timezone);

        if ($this->until) {
            $to = min($to, $this->until->modify('+1 day'));
        }

        $time = [(int) $this->first->format('G'), (int) $this->first->format('i')];
        $day = max($from, $this->first)->setTime(0, 0);
        $starts = [];

        for (; $day < $to; $day = $day->modify('+1 day')) {
            if (! $this->fallsOn($day)) {
                continue;
            }

            $start = $day->setTime(...$time);

            if ($start >= $from && $start < $to && $start >= $this->first) {
                $starts[] = $start;
            }
        }

        return $starts;
    }

    /** First occurrence at or after $after, looking up to $limit (default a year) ahead. */
    public function next(DateTimeInterface $after, ?DateTimeInterface $limit = null): ?DateTimeImmutable
    {
        $after = DateTimeImmutable::createFromInterface($after);

        return $this->between($after, $limit ?? $after->modify('+1 year'))[0] ?? null;
    }

    /** Last occurrence, when the schedule has an end date. */
    public function last(): ?DateTimeImmutable
    {
        if (! $this->until) {
            return null;
        }

        $starts = $this->between($this->first, $this->until->modify('+1 day'));

        return $starts ? end($starts) : null;
    }

    /** The occurrence on a 'Y-m-d' date, if there is one. */
    public function on(string $date): ?DateTimeImmutable
    {
        $day = DateTimeImmutable::createFromFormat('!Y-m-d', $date, $this->first->getTimezone());

        return $day ? ($this->between($day, $day->modify('+1 day'))[0] ?? null) : null;
    }

    /** "Every Friday", "Every other Tuesday and Thursday", "Last Sunday of the month". */
    public function summary(): string
    {
        if ($this->frequency === 'monthly') {
            $position = $this->monthlyPosition();
            $ordinal = $position === -1 ? 'last' : self::ORDINALS[$position];

            return sprintf(\__('%s %s of the month', 'gust'), ucfirst($ordinal), $this->first->format('l'));
        }

        $days = array_map('ucfirst', $this->weekdays());
        $last = array_pop($days);
        $list = $days ? implode(', ', $days).' '.\__('and', 'gust').' '.$last : $last;

        return $this->frequency === 'fortnightly'
            ? sprintf(\__('Every other %s', 'gust'), $list)
            : sprintf(\__('Every %s', 'gust'), $list);
    }

    protected function fallsOn(DateTimeImmutable $day): bool
    {
        if (in_array($day->format('Y-m-d'), $this->skip, true)) {
            return false;
        }

        $weekday = strtolower($day->format('l'));

        if ($this->frequency === 'monthly') {
            return $weekday === strtolower($this->first->format('l')) && $this->positionOf($day, $this->monthlyPosition() === -1);
        }

        if (! in_array($weekday, $this->weekdays(), true)) {
            return false;
        }

        if ($this->frequency === 'fortnightly') {
            // Weeks run Monday–Sunday; every other week from the first start's week.
            $weeks = intdiv((int) $this->mondayOf($this->first)->diff($this->mondayOf($day))->days, 7);

            return $weeks % 2 === 0;
        }

        return true;
    }

    /** @return string[] */
    protected function weekdays(): array
    {
        return $this->days ?: [strtolower($this->first->format('l'))];
    }

    /** 1–4 for the nth weekday of the month, -1 for the last (a 5th weekday counts as last). */
    protected function monthlyPosition(): int
    {
        $nth = intdiv((int) $this->first->format('j') - 1, 7) + 1;

        return $nth > 4 ? -1 : $nth;
    }

    protected function positionOf(DateTimeImmutable $day, bool $last): bool
    {
        if ($last) {
            return (int) $day->format('j') + 7 > (int) $day->format('t');
        }

        return intdiv((int) $day->format('j') - 1, 7) + 1 === $this->monthlyPosition();
    }

    protected function mondayOf(DateTimeImmutable $date): DateTimeImmutable
    {
        return $date->setTime(0, 0)->modify('-'.((int) $date->format('N') - 1).' days');
    }
}
