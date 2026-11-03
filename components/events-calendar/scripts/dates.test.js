/**
 * Run: node --test components/events-calendar/scripts/dates.test.js
 */

import assert from 'node:assert/strict';
import { describe, it } from 'node:test';
import {
    addMonths,
    compareMonths,
    dateKey,
    daysInMonth,
    formatMonth,
    monthGrid,
    monthLabel,
    ordinalDayLabel,
    parseMonth,
    weeksBetween,
} from './dates.js';

describe('parseMonth / formatMonth', () => {
    it('round-trips a valid month', () => {
        assert.deepEqual(parseMonth('2026-04'), { year: 2026, month: 4 });
        assert.equal(formatMonth({ year: 2026, month: 4 }), '2026-04');
    });

    it('rejects malformed input', () => {
        for (const value of ['2026-13', '2026-00', '2026-4', 'april', '', null, undefined]) {
            assert.equal(parseMonth(value), null, `expected ${value} to be rejected`);
        }
    });
});

describe('addMonths', () => {
    it('steps forward within a year', () => {
        assert.deepEqual(addMonths({ year: 2026, month: 4 }, 1), { year: 2026, month: 5 });
    });

    it('rolls the year over forwards', () => {
        assert.deepEqual(addMonths({ year: 2026, month: 12 }, 1), { year: 2027, month: 1 });
        assert.deepEqual(addMonths({ year: 2026, month: 11 }, 3), { year: 2027, month: 2 });
    });

    it('rolls the year over backwards', () => {
        assert.deepEqual(addMonths({ year: 2027, month: 1 }, -1), { year: 2026, month: 12 });
        assert.deepEqual(addMonths({ year: 2026, month: 2 }, -3), { year: 2025, month: 11 });
    });

    it('orders months', () => {
        assert.ok(compareMonths({ year: 2026, month: 12 }, { year: 2027, month: 1 }) < 0);
        assert.equal(compareMonths({ year: 2026, month: 4 }, { year: 2026, month: 4 }), 0);
    });
});

describe('monthGrid', () => {
    it('builds a 5-week month padded with adjacent days (April 2026)', () => {
        const grid = monthGrid({ year: 2026, month: 4 });

        assert.equal(grid.length, 5);
        assert.equal(grid[0][0].key, '2026-03-29');
        assert.equal(grid[0][0].inMonth, false);
        assert.equal(grid[0][3].key, '2026-04-01');
        assert.equal(grid[0][3].inMonth, true);
        assert.equal(grid[0][3].weekday, 3);
        assert.equal(grid[4][6].key, '2026-05-02');
        assert.equal(grid[4][6].inMonth, false);
    });

    it('builds a 6-week month (August 2026 starts on a Saturday)', () => {
        const grid = monthGrid({ year: 2026, month: 8 });

        assert.equal(grid.length, 6);
        assert.equal(grid[0][6].key, '2026-08-01');
        assert.equal(grid[0][0].key, '2026-07-26');
        assert.equal(grid[5][0].key, '2026-08-30');
        assert.equal(grid[5][6].key, '2026-09-05');
        assert.equal(grid.flat().filter((cell) => cell.inMonth).length, 31);
    });

    it('pads across a year boundary (January 2027 starts on a Friday)', () => {
        const grid = monthGrid({ year: 2027, month: 1 });

        assert.equal(grid[0][0].key, '2026-12-27');
        assert.equal(grid[0][0].year, 2026);
        assert.equal(grid[0][5].key, '2027-01-01');
        assert.equal(grid.at(-1).at(-1).key, '2027-02-06');
    });

    it('handles February in a leap year', () => {
        assert.equal(daysInMonth({ year: 2028, month: 2 }), 29);
        assert.equal(monthGrid({ year: 2028, month: 2 }).flat().filter((cell) => cell.inMonth).length, 29);
    });
});

describe('dateKey', () => {
    it('uses the site time zone, not UTC', () => {
        // 23:30 UTC on 31 March is already 1 April in London (BST).
        const instant = new Date('2026-03-31T23:30:00Z');

        assert.equal(dateKey(instant, 'Europe/London'), '2026-04-01');
        assert.equal(dateKey(instant, 'UTC'), '2026-03-31');
    });
});

describe('monthLabel', () => {
    it('formats a month in words', () => {
        assert.equal(monthLabel({ year: 2026, month: 4 }), 'April 2026');
        assert.equal(monthLabel({ year: 2027, month: 1 }), 'January 2027');
    });
});

describe('weeksBetween', () => {
    it('spans whole weeks from the 1st of the first month to the end of the last', () => {
        // July 2026 starts on a Wednesday; January 2027 ends on a Sunday
        const weeks = weeksBetween({ year: 2026, month: 7 }, { year: 2027, month: 1 });

        assert.equal(weeks[0][0].key, '2026-06-28');
        assert.equal(weeks.at(-1)[0].key, '2027-01-31');
        assert.equal(weeks.at(-1)[6].key, '2027-02-06');
        assert.ok(weeks.every((week) => week.length === 7 && week[0].weekday === 0));
    });

    it('never repeats a week where months meet', () => {
        const keys = weeksBetween({ year: 2026, month: 3 }, { year: 2026, month: 5 }).flat().map((cell) => cell.key);

        assert.equal(new Set(keys).size, keys.length);
    });
});

describe('ordinalDayLabel', () => {
    it('adds English ordinals, teens included', () => {
        const label = (day) => ordinalDayLabel({ year: 2026, month: 11, day });

        assert.equal(label(1), '1st November');
        assert.equal(label(2), '2nd November');
        assert.equal(label(3), '3rd November');
        assert.equal(label(11), '11th November');
        assert.equal(label(12), '12th November');
        assert.equal(label(13), '13th November');
        assert.equal(label(21), '21st November');
        assert.equal(label(23), '23rd November');
    });
});
