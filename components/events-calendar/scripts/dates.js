/**
 * Date helpers for the events calendar. Pure functions, no DOM: months are
 * `{ year, month }` (month 1–12), days are `YYYY-MM-DD` keys, and all
 * arithmetic runs on UTC timestamps so DST never shifts a day.
 */

const MONTH_PATTERN = /^(\d{4})-(0[1-9]|1[0-2])$/;

const DAY = 86_400_000;

const pad = (number) => String(number).padStart(2, '0');

/** "2026-04" → { year: 2026, month: 4 }, or null when malformed. */
export function parseMonth(value) {
    const match = MONTH_PATTERN.exec(String(value ?? ''));

    return match ? { year: Number(match[1]), month: Number(match[2]) } : null;
}

/** { year: 2026, month: 4 } → "2026-04" */
export function formatMonth({ year, month }) {
    return `${year}-${pad(month)}`;
}

/** Add (or subtract) whole months, rolling the year over as needed. */
export function addMonths({ year, month }, count) {
    const index = year * 12 + (month - 1) + count;

    return { year: Math.floor(index / 12), month: (index % 12) + 1 };
}

/** Negative when a is before b, 0 when equal, positive when after. */
export function compareMonths(a, b) {
    return a.year * 12 + a.month - (b.year * 12 + b.month);
}

/** Number of days in a month. */
export function daysInMonth({ year, month }) {
    return new Date(Date.UTC(year, month, 0)).getUTCDate();
}

/** "2026-04-01" → { year, month, day } (no validation beyond the shape). */
export function parseDay(key) {
    const [year, month, day] = String(key).split('-').map(Number);

    return { year, month, day };
}

/** One grid cell for a UTC midnight timestamp. */
function cellAt(time) {
    const date = new Date(time);
    const year = date.getUTCFullYear();
    const month = date.getUTCMonth() + 1;
    const day = date.getUTCDate();

    return { year, month, day, key: `${year}-${pad(month)}-${pad(day)}`, weekday: date.getUTCDay() };
}

/**
 * Continuous weeks, Sunday to Saturday, from the week holding the 1st of
 * `from` to the week holding the last day of `to` (inclusive). Each week
 * appears once, so a week that straddles two months is shared by both.
 *
 * Each cell: { year, month, day, key, weekday }.
 */
export function weeksBetween(from, to) {
    const first = Date.UTC(from.year, from.month - 1, 1);
    const start = first - new Date(first).getUTCDay() * DAY;
    const last = Date.UTC(to.year, to.month - 1, daysInMonth(to));
    const end = last + (6 - new Date(last).getUTCDay()) * DAY;
    const rows = [];

    for (let week = start; week <= end; week += 7 * DAY) {
        rows.push(Array.from({ length: 7 }, (_, weekday) => cellAt(week + weekday * DAY)));
    }

    return rows;
}

/**
 * The weeks of a month as rows of seven cells, Sunday to Saturday, padded
 * with the adjacent months' days. 5 or 6 rows depending on the month.
 *
 * Each cell: { year, month, day, key, inMonth, weekday }.
 */
export function monthGrid({ year, month }) {
    return weeksBetween({ year, month }, { year, month }).map((week) =>
        week.map((cell) => ({ ...cell, inMonth: cell.month === month && cell.year === year }))
    );
}

/**
 * Calendar date of an instant in a time zone, as a "YYYY-MM-DD" key. This is
 * how "today" and each event's day are decided: by the site's zone, not the
 * visitor's.
 */
export function dateKey(date, timeZone) {
    const parts = new Intl.DateTimeFormat('en-GB', {
        timeZone,
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
    }).formatToParts(date);
    const get = (type) => parts.find((part) => part.type === type)?.value ?? '';

    return `${get('year')}-${get('month')}-${get('day')}`;
}

/** { year, month } → "April 2026" */
export function monthLabel({ year, month }, locale = 'en-GB') {
    return new Intl.DateTimeFormat(locale, { month: 'long', year: 'numeric', timeZone: 'UTC' }).format(
        new Date(Date.UTC(year, month - 1, 1))
    );
}

/** { year, month, day } → "Wednesday 1 April 2026" (or a custom option set). */
export function dayLabel({ year, month, day }, options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }, locale = 'en-GB') {
    return new Intl.DateTimeFormat(locale, { ...options, timeZone: 'UTC' }).format(new Date(Date.UTC(year, month - 1, day)));
}

/** { year, month, day } → "13th November", like the Event Card's PHP 'jS F'. */
export function ordinalDayLabel({ year, month, day }, locale = 'en-GB') {
    const suffix = day % 10 === 1 && day !== 11 ? 'st' : day % 10 === 2 && day !== 12 ? 'nd' : day % 10 === 3 && day !== 13 ? 'rd' : 'th';
    const name = new Intl.DateTimeFormat(locale, { month: 'long', timeZone: 'UTC' }).format(new Date(Date.UTC(year, month - 1, day)));

    return `${day}${suffix} ${name}`;
}

/** Instant → "19:30" in the site's time zone. */
export function timeLabel(date, timeZone, locale = 'en-GB') {
    return new Intl.DateTimeFormat(locale, { timeZone, hour: '2-digit', minute: '2-digit', hour12: false }).format(date);
}
