/**
 * Events calendar: client-rendered month grid or list from the static JSON
 * feed. Markup: ./template.php, date maths: ./scripts/dates.js.
 *
 * The server renders the header (view toggle, month nav) and a fallback list
 * of the next events inside [data-events-calendar-body]. Once the feed has
 * loaded this replaces that body with a <table> (calendar view) or a list
 * grouped by month (list view) and keeps ?month= / ?view= in the URL via
 * history.replaceState. Below md only the list renders, whatever the view.
 *
 * "Today" and "past" follow the site's time zone (from the feed), not the
 * visitor's. Months before the feed window load on demand from the REST
 * route in [data-rest] (three months per request).
 */

import dynamicElements from '../../assets/scripts/helpers/dynamicElements.js';
import {
    addMonths,
    compareMonths,
    dateKey,
    dayLabel,
    formatMonth,
    monthGrid,
    monthLabel,
    parseDay,
    parseMonth,
    timeLabel,
} from './scripts/dates.js';

const WEEKDAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
// Column headings as the design abbreviates them ("Thur", not "Thu")
const WEEKDAY_SHORT = ['Sun', 'Mon', 'Tue', 'Wed', 'Thur', 'Fri', 'Sat'];
const VIEWS = ['calendar', 'list'];
const small = window.matchMedia('(max-width: 767.98px)');

/** createElement shorthand: h('a', { href, class }, 'text', childNode, …) */
function h(tag, attrs = {}, ...children) {
    const element = document.createElement(tag);

    for (const [name, value] of Object.entries(attrs)) {
        if (value !== null && value !== undefined && value !== false) element.setAttribute(name, value);
    }

    element.append(...children.filter((child) => child !== null && child !== undefined && child !== false));

    return element;
}

async function fetchJson(url) {
    const response = await fetch(url, { headers: { Accept: 'application/json' } });

    if (!response.ok) throw new Error(`${url}: ${response.status}`);

    return response.json();
}

function eventsCalendar(root) {
    const body = root.querySelector('[data-events-calendar-body]');
    const label = root.querySelector('[data-events-calendar-month]');
    const prev = root.querySelector('[data-events-calendar-prev]');
    const next = root.querySelector('[data-events-calendar-next]');
    const toggles = [...root.querySelectorAll('[data-events-calendar-view]')];
    const current = parseMonth(root.dataset.month);

    if (!body || !label || !prev || !next || !current || !root.dataset.feed) return;

    const state = {
        month: current,
        view: VIEWS.includes(root.dataset.view) ? root.dataset.view : 'calendar',
        loadedFrom: parseMonth(root.dataset.from) ?? current,
        timeZone: root.dataset.timezone || 'UTC',
        today: '',
        events: [],
    };
    const ids = new Set();

    const addEvents = (items) => {
        for (const item of items) {
            if (ids.has(item.id) || !item.start) continue;

            ids.add(item.id);
            const start = new Date(item.start);
            state.events.push({ ...item, start, key: dateKey(start, state.timeZone) });
        }

        state.events.sort((a, b) => a.start - b.start);
    };

    /** Load older chunks until the feed covers the requested month. */
    const ensureLoaded = async (month) => {
        for (let guard = 0; compareMonths(month, state.loadedFrom) < 0 && guard < 12 && root.dataset.rest; guard++) {
            const url = new URL(root.dataset.rest, location.href);
            url.searchParams.set('before', formatMonth(state.loadedFrom));
            addEvents((await fetchJson(url)).events ?? []);
            state.loadedFrom = addMonths(state.loadedFrom, -3);
        }
    };

    const urlFor = (month, view) => {
        const url = new URL(location.href);
        url.searchParams.set('month', formatMonth(month));
        if (view === 'list') url.searchParams.set('view', 'list');
        else url.searchParams.delete('view');

        return url;
    };

    const eventsOn = (key) => state.events.filter((event) => event.key === key);

    const eventLink = (event) =>
        h(
            'li',
            { class: 'events-calendar__event' },
            h('a', { class: 'events-calendar__event-link', href: event.url }, event.title),
            event.presenter ? h('span', { class: 'events-calendar__event-presenter' }, event.presenter) : null
        );

    const cell = ({ key, day, inMonth, year, month }) => {
        const events = eventsOn(key);
        const classes = ['events-calendar__cell'];

        if (!inMonth) classes.push('events-calendar__cell--outside');
        if (key < state.today) classes.push('events-calendar__cell--past');
        if (key === state.today) classes.push('events-calendar__cell--today');
        if (events.length) classes.push('events-calendar__cell--has-events');

        // "April 1" on the first of the month (month first, as in the design)
        const number = day === 1 ? dayLabel({ year, month, day }, { month: 'long', day: 'numeric' }, 'en-US') : String(day);

        return h(
            'td',
            { class: classes.join(' ') },
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
    };

    const renderCalendar = () =>
        h(
            'table',
            { class: 'events-calendar__table' },
            h('caption', { class: 'screen-reader-text' }, `Events in ${monthLabel(state.month)}`),
            h(
                'thead',
                {},
                h(
                    'tr',
                    {},
                    ...WEEKDAYS.map((name, index) =>
                        h('th', { class: 'events-calendar__heading', scope: 'col', 'aria-label': name }, WEEKDAY_SHORT[index])
                    )
                )
            ),
            h('tbody', {}, ...monthGrid(state.month).map((week) => h('tr', {}, ...week.map(cell))))
        );

    const row = (event) => {
        const { year, month, day } = parseDay(event.key);
        const time = timeLabel(event.start, state.timeZone);

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

    const render = () => {
        const view = small.matches ? 'list' : state.view;

        label.textContent = monthLabel(state.month);
        prev.href = urlFor(addMonths(state.month, -1), state.view);
        next.href = urlFor(addMonths(state.month, 1), state.view);

        for (const toggle of toggles) {
            const name = toggle.dataset.eventsCalendarView;
            toggle.href = urlFor(state.month, name);
            if (name === state.view) toggle.setAttribute('aria-current', 'page');
            else toggle.removeAttribute('aria-current');
        }

        root.dataset.eventsCalendarView = view;
        body.replaceChildren(view === 'list' ? renderList() : renderCalendar());
    };

    const go = async (month, view) => {
        state.month = month;
        state.view = view;
        history.replaceState(null, '', urlFor(month, view));

        try {
            await ensureLoaded(month);
        } catch (error) {
            console.warn('Events calendar: could not load older events', error);
        }

        render();
    };

    fetchJson(root.dataset.feed)
        .then(async (feed) => {
            state.timeZone = feed.timezone || state.timeZone;
            state.today = dateKey(new Date(), state.timeZone);
            addEvents(feed.events ?? []);

            prev.addEventListener('click', (event) => {
                event.preventDefault();
                go(addMonths(state.month, -1), state.view);
            });
            next.addEventListener('click', (event) => {
                event.preventDefault();
                go(addMonths(state.month, 1), state.view);
            });
            for (const toggle of toggles) {
                toggle.addEventListener('click', (event) => {
                    event.preventDefault();
                    go(state.month, toggle.dataset.eventsCalendarView);
                });
            }
            small.addEventListener('change', render);

            await ensureLoaded(state.month);
            render();
        })
        .catch((error) => {
            // Leave the server-rendered fallback list in place.
            console.warn('Events calendar: feed unavailable', error);
        });
}

dynamicElements.define('[data-events-calendar]', eventsCalendar);
