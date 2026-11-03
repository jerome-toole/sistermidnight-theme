/**
 * Events calendar: client-rendered week grid or list from the static JSON
 * feed. Markup: ./template.php, date maths: ./scripts/dates.js, hover card
 * and touch sheet: ./scripts/preview.js.
 *
 * The server renders the header (view toggle, month nav) and a fallback list
 * of the next events inside [data-events-calendar-body]. Once the feed has
 * loaded this replaces that body with a <table> (calendar view) or a list
 * grouped by month (list view) and keeps ?month= / ?view= in the URL via
 * history.replaceState. Below md only the list renders, whatever the view.
 *
 * Calendar view: one continuous run of weeks in a scroller that snaps to
 * week rows, from a buffer month before the earliest event to a buffer month
 * after the last (always including this month). The month whose week is at
 * the top names the header and the URL; other months' days are muted. When
 * a buffer month scrolls in, a bubble at that edge says there's nothing more.
 * The month arrows scroll to the previous/next month's first week, and a
 * "Back to today" button shows whenever today's week is out of view. Wheel
 * scrolling past either end carries on scrolling the page.
 *
 * Mouse: hovering an event shows a compact card that trails the cursor.
 * Touch: the first tap on a day's event opens a sheet that swipes through
 * every loaded event instead of following the link.
 *
 * "Today" and "past" follow the site's time zone (from the feed), not the
 * visitor's. The feed only holds the last two months of past events, so the
 * grid never reaches further back than that.
 */

import dynamicElements from '../../assets/scripts/helpers/dynamicElements.js';
import {
    addMonths,
    compareMonths,
    dateKey,
    dayLabel,
    formatMonth,
    monthLabel,
    parseDay,
    parseMonth,
    timeLabel,
    weeksBetween,
} from './scripts/dates.js';
import { h } from './scripts/dom.js';
import { eventSheet, hoverCard } from './scripts/preview.js';

const WEEKDAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
const VIEWS = ['calendar', 'list'];
const small = window.matchMedia('(max-width: 767.98px)');
const canHover = window.matchMedia('(hover: hover) and (pointer: fine)');
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

async function fetchJson(url) {
    const response = await fetch(url, { headers: { Accept: 'application/json' } });

    if (!response.ok) throw new Error(`${url}: ${response.status}`);

    return response.json();
}

const monthOf = ({ year, month }) => ({ year, month });
const maxMonth = (a, b) => (compareMonths(a, b) >= 0 ? a : b);
const minMonth = (a, b) => (compareMonths(a, b) <= 0 ? a : b);

function eventsCalendar(root) {
    const body = root.querySelector('[data-events-calendar-body]');
    const label = root.querySelector('[data-events-calendar-month]');
    const prev = root.querySelector('[data-events-calendar-prev]');
    const next = root.querySelector('[data-events-calendar-next]');
    const months = root.querySelector('[data-events-calendar-months]');
    const toggles = [...root.querySelectorAll('[data-events-calendar-view]')];
    const icon = root.querySelector('[data-events-calendar-icon]')?.content.firstElementChild ?? null;
    const current = parseMonth(root.dataset.month);

    if (!body || !label || !prev || !next || !current || !root.dataset.feed) return;

    const state = {
        month: current,
        view: VIEWS.includes(root.dataset.view) ? root.dataset.view : 'calendar',
        timeZone: root.dataset.timezone || 'UTC',
        today: '',
        events: [],
    };
    const ids = new Set();
    const byId = new Map();

    const addEvents = (items) => {
        for (const item of items) {
            if (ids.has(item.id) || !item.start) continue;

            ids.add(item.id);
            const start = new Date(item.start);
            const event = { ...item, start, key: dateKey(start, state.timeZone) };
            state.events.push(event);
            byId.set(String(item.id), event);
        }

        state.events.sort((a, b) => a.start - b.start);
    };

    const urlFor = (month, view) => {
        const url = new URL(location.href);
        url.searchParams.set('month', formatMonth(month));
        if (view === 'list') url.searchParams.set('view', 'list');
        else url.searchParams.delete('view');

        return url;
    };

    const eventsOn = (key) => state.events.filter((event) => event.key === key);

    /* ---- Calendar ---------------------------------------------------------- */

    // Built per calendar render; see renderCalendar()
    let grid = null;
    let preview = null;
    let sheet = null;
    let rehover = null; // set per grid by bindPreviews()
    window.addEventListener('scroll', () => rehover?.(), { passive: true });

    /** Months the grid scrolls through: events (and today) plus a buffer month each side. */
    const range = () => {
        const today = parseMonth(state.today.slice(0, 7)) ?? current;
        const firstEvent = state.events[0] ? parseMonth(state.events[0].key.slice(0, 7)) : today;
        const lastEvent = state.events.at(-1) ? parseMonth(state.events.at(-1).key.slice(0, 7)) : today;
        const from = minMonth(firstEvent, today);
        const to = maxMonth(lastEvent, today);

        return { from: addMonths(from, -1), to: addMonths(to, 1), firstEvent: from, lastEvent: to };
    };

    const eventLink = (event) =>
        h(
            'li',
            { class: 'events-calendar__event', 'data-event-id': event.id },
            h('a', { class: 'events-calendar__event-link', href: event.url }, event.title),
            event.presenter ? h('span', { class: 'events-calendar__event-presenter' }, event.presenter) : null
        );

    const cell = ({ key, day, year, month }) => {
        const events = eventsOn(key);
        const classes = ['events-calendar__cell'];
        const monthKey = key.slice(0, 7);

        if (grid && monthKey !== grid.active) classes.push('events-calendar__cell--outside');
        if (key < state.today) classes.push('events-calendar__cell--past');
        if (key === state.today) classes.push('events-calendar__cell--today');
        if (events.length) classes.push('events-calendar__cell--has-events');

        // "April 1" on the first of the month (month first, as in the design)
        const number = day === 1 ? dayLabel({ year, month, day }, { month: 'long', day: 'numeric' }, 'en-US') : String(day);

        const td = h(
            'td',
            { class: classes.join(' '), role: 'cell', 'data-month': monthKey },
            h(
                'div',
                { class: 'events-calendar__cell-inner' },
                h(
                    'span',
                    { class: 'events-calendar__day' },
                    h('span', { 'aria-hidden': 'true' }, number),
                    h('span', { class: 'screen-reader-text' }, dayLabel({ year, month, day }))
                ),
                events.length ? h('ul', { class: 'events-calendar__events' }, ...events.map(eventLink)) : null
            )
        );

        grid?.cells.set(key, td);

        return td;
    };

    const renderCalendar = () => {
        const { from, to, firstEvent, lastEvent } = range();
        const weeks = weeksBetween(from, to);

        grid = {
            active: formatMonth(state.month),
            cells: new Map(),
            rows: [],
            // Month each row stands for: the month its Saturday falls in, so the
            // week holding the 1st opens that month
            rowMonths: weeks.map((week) => formatMonth(monthOf(week[6]))),
            from: formatMonth(from),
            to: formatMonth(to),
            // Rows entirely inside a buffer month
            startBuffer: weeks.findIndex((week) => compareMonths(monthOf(week[6]), firstEvent) >= 0),
            endBuffer: weeks.findIndex((week) => compareMonths(monthOf(week[0]), lastEvent) > 0),
        };

        grid.rows = weeks.map((week) => h('tr', { class: 'events-calendar__week', role: 'row' }, ...week.map(cell)));

        grid.scroller = h(
            'div',
            { class: 'events-calendar__scroller', tabindex: '0', 'aria-label': 'Events calendar, scrolls by week' },
            h(
                'table',
                { class: 'events-calendar__table', role: 'table' },
                h('caption', { class: 'screen-reader-text' }, `Events from ${monthLabel(from)} to ${monthLabel(to)}`),
                h(
                    'thead',
                    { class: 'events-calendar__head', role: 'rowgroup' },
                    h(
                        'tr',
                        { class: 'events-calendar__week', role: 'row' },
                        ...WEEKDAYS.map((name) =>
                            h('th', { class: 'events-calendar__heading', scope: 'col', role: 'columnheader' }, name)
                        )
                    )
                ),
                h('tbody', { role: 'rowgroup' }, ...grid.rows)
            )
        );

        grid.edges = {
            start: h('p', { class: 'events-calendar__edge events-calendar__edge--start' }, 'No earlier events'),
            end: h('p', { class: 'events-calendar__edge events-calendar__edge--end' }, 'No more events yet. Check back soon'),
        };

        grid.todayRow = weeks.findIndex((week) => week.some((day) => day.key === state.today));
        grid.todayButton = h('button', { class: 'button events-calendar__today', type: 'button', tabindex: '-1' }, 'Back to today');
        grid.todayButton.addEventListener('click', () => {
            // This month from its first week, unless that would leave today below the fold
            const top = Math.max(grid.rowMonths.indexOf(state.today.slice(0, 7)), grid.todayRow - visibleRows() + 1);
            grid.scroller.scrollTo({ top: grid.rows[top].offsetTop - grid.rows[0].offsetTop, behavior: reducedMotion.matches ? 'instant' : 'smooth' });
        });

        return h('div', { class: 'events-calendar__viewport' }, grid.scroller, grid.edges.start, grid.edges.end, grid.todayButton);
    };

    const rowHeight = () => (grid.rows.length > 1 ? grid.rows[1].offsetTop - grid.rows[0].offsetTop : 1);

    /** Index of the week row snapped (or nearest) to the top of the scroller. */
    const topRow = () => Math.max(0, Math.min(grid.rows.length - 1, Math.round(grid.scroller.scrollTop / rowHeight())));

    const visibleRows = () => {
        const head = grid.scroller.querySelector('thead')?.offsetHeight ?? 0;

        return Math.max(1, Math.floor((grid.scroller.clientHeight - head) / rowHeight()));
    };

    const scrollToMonth = (month, smooth) => {
        const key = formatMonth(month);
        const index = grid.rowMonths.indexOf(key);

        if (index < 0) return;

        grid.scroller.scrollTo({
            top: grid.rows[index].offsetTop - grid.rows[0].offsetTop,
            behavior: smooth && !reducedMotion.matches ? 'smooth' : 'instant',
        });
    };

    const setActiveMonth = (key) => {
        if (key === grid.active) return;

        for (const td of grid.cells.values()) {
            td.classList.toggle('events-calendar__cell--outside', td.dataset.month !== key);
        }

        grid.active = key;
        state.month = parseMonth(key);
        updateHeader();
        replaceUrl();
    };

    let urlTimer = 0;
    const replaceUrl = () => {
        clearTimeout(urlTimer);
        urlTimer = setTimeout(() => history.replaceState(null, '', urlFor(state.month, state.view)), 200);
    };

    const onScroll = () => {
        if (!grid) return;

        const top = topRow();
        const bottom = top + visibleRows() - 1;

        setActiveMonth(grid.rowMonths[top]);
        grid.edges.start.classList.toggle('is-visible', grid.startBuffer > 0 && top < grid.startBuffer);
        grid.edges.end.classList.toggle('is-visible', grid.endBuffer >= 0 && bottom >= grid.endBuffer);

        const away = grid.todayRow >= 0 && (grid.todayRow < top || grid.todayRow > bottom);
        grid.todayButton.classList.toggle('is-visible', away);
        grid.todayButton.tabIndex = away ? 0 : -1;
    };

    /**
     * Wheel past the top or bottom scrolls the page instead. Browsers latch a
     * wheel gesture to the scroller it started in, so without this the page
     * only moves on the next gesture.
     */
    const chainWheel = (event) => {
        if (event.ctrlKey || !event.deltaY) return;

        const { scrollTop, scrollHeight, clientHeight } = grid.scroller;
        const atTop = scrollTop <= 0 && event.deltaY < 0;
        const atBottom = scrollTop + clientHeight >= scrollHeight - 1 && event.deltaY > 0;

        if (!atTop && !atBottom) return;

        event.preventDefault();
        const lines = event.deltaMode === WheelEvent.DOM_DELTA_LINE ? 16 : event.deltaMode === WheelEvent.DOM_DELTA_PAGE ? window.innerHeight : 1;
        window.scrollBy({ top: event.deltaY * lines, behavior: 'instant' });
    };

    /** Mouse hover card, keyboard-focus card and the touch sheet. */
    const bindPreviews = () => {
        const scroller = grid.scroller;
        let pointer = { x: 0, y: 0, type: 'mouse' };

        preview ??= hoverCard();
        sheet ??= eventSheet({ icon });

        /** Event under (x, y): the hovered event, else the first in the hovered day. */
        const eventAt = (target) => {
            const item = target?.closest?.('[data-event-id]') ?? target?.closest?.('.events-calendar__cell')?.querySelector('[data-event-id]');

            return item && scroller.contains(item) ? byId.get(item.dataset.eventId) : null;
        };

        const hover = () => {
            const event = eventAt(document.elementFromPoint(pointer.x, pointer.y));

            if (event) preview.show(event, pointer.x, pointer.y);
            else preview.hide();
        };

        scroller.addEventListener('pointermove', (event) => {
            pointer = { x: event.clientX, y: event.clientY, type: event.pointerType };
            if (event.pointerType === 'mouse' && canHover.matches) hover();
        });
        scroller.addEventListener('pointerleave', () => preview.hide());
        // Scrolling the grid or the page moves the days under a still cursor
        rehover = () => {
            if (pointer.type === 'mouse' && canHover.matches && scroller.isConnected && scroller.matches(':hover')) {
                requestAnimationFrame(hover);
            }
        };
        scroller.addEventListener('scroll', rehover, { passive: true });

        scroller.addEventListener('focusin', (event) => {
            const item = event.target.closest('[data-event-id]');
            if (item && event.target.matches(':focus-visible')) preview.anchor(byId.get(item.dataset.eventId), event.target.getBoundingClientRect());
        });
        scroller.addEventListener('focusout', () => preview.hide());

        scroller.addEventListener('pointerdown', (event) => {
            pointer.type = event.pointerType;
        });

        // First tap on a touch screen previews instead of following the link
        scroller.addEventListener('click', (event) => {
            if (pointer.type !== 'touch') return;

            const selected = eventAt(event.target);
            if (!selected) return;

            event.preventDefault();
            preview.hide();
            sheet.open(state.events, state.events.indexOf(selected));
        });
    };

    /* ---- List -------------------------------------------------------------- */

    const row = (event) => {
        const { year, month, day } = parseDay(event.key);
        const time = event.time_override || timeLabel(event.start, state.timeZone);

        return h(
            'li',
            { class: 'events-calendar__row' },
            h(
                'time',
                { class: 'events-calendar__row-date', datetime: event.start.toISOString() },
                dayLabel({ year, month, day }, { weekday: 'short', day: 'numeric', month: 'short' }),
                time ? h('span', { class: 'events-calendar__row-time' }, time) : null
            ),
            h('a', { class: 'events-calendar__row-title', href: event.url }, event.title),
            event.venue ? h('span', { class: 'events-calendar__row-venue' }, event.venue) : null
        );
    };

    /** Events from the selected month onwards, grouped by month. */
    const renderList = () => {
        const from = formatMonth(state.month);
        const groups = new Map();

        for (const event of state.events) {
            const month = event.key.slice(0, 7);
            if (month < from) continue;
            if (!groups.has(month)) groups.set(month, []);
            groups.get(month).push(event);
        }

        if (!groups.size) {
            return h('p', { class: 'events-calendar__empty' }, `No events from ${monthLabel(state.month)}`);
        }

        return h(
            'div',
            { class: 'events-calendar__list' },
            ...[...groups].map(([month, events]) =>
                h(
                    'section',
                    { class: 'events-calendar__group' },
                    h('h3', { class: 'events-calendar__group-heading' }, monthLabel(parseMonth(month))),
                    h('ul', { class: 'events-calendar__rows' }, ...events.map(row))
                )
            )
        );
    };

    /* ---- Shared ------------------------------------------------------------ */

    const updateHeader = () => {
        label.textContent = monthLabel(state.month);
        prev.href = urlFor(addMonths(state.month, -1), state.view);
        next.href = urlFor(addMonths(state.month, 1), state.view);

        // The arrows stop at the buffer months
        const atStart = grid && compareMonths(state.month, parseMonth(grid.from)) <= 0;
        const atEnd = grid && compareMonths(state.month, parseMonth(grid.to)) >= 0;
        prev.setAttribute('aria-disabled', atStart ? 'true' : 'false');
        next.setAttribute('aria-disabled', atEnd ? 'true' : 'false');

        for (const toggle of toggles) {
            const name = toggle.dataset.eventsCalendarView;
            toggle.href = urlFor(state.month, name);
            if (name === state.view) toggle.setAttribute('aria-current', 'page');
            else toggle.removeAttribute('aria-current');
        }
    };

    const render = () => {
        const view = small.matches ? 'list' : state.view;

        root.dataset.eventsCalendarView = view;

        // The list spans several months, so the month nav only belongs to the calendar
        if (months) {
            if (view === 'list' && months.contains(document.activeElement)) {
                body.setAttribute('tabindex', '-1');
                body.focus({ preventScroll: true });
            }
            months.hidden = view === 'list';
        }

        preview?.hide();
        grid = null;

        if (view === 'list') {
            updateHeader();
            body.replaceChildren(renderList());

            return;
        }

        // Keep the requested month inside the scrollable range
        const { from, to } = range();
        state.month = maxMonth(minMonth(state.month, to), from);

        body.replaceChildren(renderCalendar());
        scrollToMonth(state.month, false);
        updateHeader();
        bindPreviews();
        grid.scroller.addEventListener('scroll', () => requestAnimationFrame(onScroll), { passive: true });
        grid.scroller.addEventListener('wheel', chainWheel, { passive: false });
        onScroll();
    };

    const step = (count) => (event) => {
        event.preventDefault();

        if (grid) scrollToMonth(addMonths(parseMonth(grid.active), count), true);
        else {
            state.month = addMonths(state.month, count);
            replaceUrl();
            render();
        }
    };

    fetchJson(root.dataset.feed)
        .then((feed) => {
            state.timeZone = feed.timezone || state.timeZone;
            state.today = dateKey(new Date(), state.timeZone);
            addEvents(feed.events ?? []);

            prev.addEventListener('click', step(-1));
            next.addEventListener('click', step(1));
            for (const toggle of toggles) {
                toggle.addEventListener('click', (event) => {
                    event.preventDefault();
                    state.view = toggle.dataset.eventsCalendarView;
                    replaceUrl();
                    render();
                });
            }
            small.addEventListener('change', render);

            render();
        })
        .catch((error) => {
            // Leave the server-rendered fallback list in place.
            console.warn('Events calendar: feed unavailable', error);
        });
}

dynamicElements.define('[data-events-calendar]', eventsCalendar);
