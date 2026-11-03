/**
 * Event previews for the calendar grid: a compact version of the featured
 * Event Card (poster, presenter, title, excerpt, date/venue row) that
 * follows the mouse over an event (or sits by a focused event link), and on
 * touch a modal sheet whose cards swipe sideways through every loaded event.
 *
 * Both repeat what the event's page link offers: the hover
 * card is decorative (aria-hidden), the sheet is a real dialog with links.
 */

import { ordinalDayLabel, parseDay } from './dates.js';
import { h } from './dom.js';

const OFFSET = 18; // px between the cursor and the card
const EASE = 0.5; // share of the remaining distance covered per frame
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

/**
 * Compact event card. `link` makes the title an anchor and adds a
 * "View event" link (the sheet); without it the card is plain text.
 */
export function eventCard(event, { link = false, icon = null, eager = false } = {}) {
    const poster = event.poster
        ? h('img', {
              class: 'events-calendar__card-poster',
              src: event.poster.src,
              srcset: event.poster.srcset || null,
              sizes: link ? 'min(480px, 100vw)' : '288px',
              alt: '',
              loading: eager || !link ? 'eager' : 'lazy',
              decoding: 'async',
          })
        : null;

    return h(
        'article',
        { class: `events-calendar__card${link ? ' events-calendar__card--sheet' : ''}` },
        poster,
        h(
            'div',
            { class: 'events-calendar__card-body' },
            h(
                'div',
                { class: 'events-calendar__card-header' },
                event.presenter ? h('p', { class: 'events-calendar__card-presenter' }, event.presenter) : null,
                h(
                    'h3',
                    { class: 'events-calendar__card-title' },
                    link ? h('a', { href: event.url, class: 'events-calendar__card-link' }, event.title) : event.title
                ),
                event.excerpt ? h('p', { class: 'events-calendar__card-excerpt' }, event.excerpt) : null
            ),
            // "13th November … Sister Midnight", as on the featured cards (the time lives on the event page)
            h(
                'p',
                { class: 'events-calendar__card-meta' },
                h(
                    'time',
                    { class: 'events-calendar__card-when', datetime: event.start.toISOString() },
                    ordinalDayLabel(parseDay(event.key))
                ),
                event.venue ? h('span', { class: 'events-calendar__card-venue' }, event.venue) : null
            ),
            link
                ? h(
                      'a',
                      { href: event.url, class: 'button button--link events-calendar__card-cta', tabindex: '-1' },
                      h('span', { class: 'button__label' }, 'View event'),
                      icon?.cloneNode(true)
                  )
                : null
        )
    );
}

/**
 * Card that trails the mouse. `show(event, x, y)` moves it towards the
 * pointer (flipping left/up near the viewport edges), `anchor(event, rect)`
 * places it beside an element for keyboard focus, `hide()` fades it out.
 */
export function hoverCard() {
    const element = h('div', { class: 'events-calendar__preview', 'aria-hidden': 'true' });
    const position = { x: 0, y: 0, tx: 0, ty: 0, flipped: false };
    const pointer = { x: 0, y: 0 };
    let current = null;
    let frame = 0;

    document.body.append(element);

    const paint = () => {
        element.style.transform = `translate3d(${position.x}px, ${position.y}px, 0)`;
    };

    const tick = () => {
        position.x += (position.tx - position.x) * EASE;
        position.y += (position.ty - position.y) * EASE;
        paint();

        frame = Math.abs(position.tx - position.x) + Math.abs(position.ty - position.y) > 0.5 ? requestAnimationFrame(tick) : 0;
    };

    const setContent = (event) => {
        if (current === event) return false;

        current = event;
        const card = eventCard(event);
        element.replaceChildren(card);

        // The poster sets the card's height once it loads: re-fit it to the viewport
        card.querySelector('img')?.addEventListener('load', () => {
            if (current === event && element.classList.contains('is-visible')) target(pointer.x, pointer.y, true);
        });

        return true;
    };

    /** Aim at (x, y), keeping the card inside the viewport. */
    const target = (x, y, jump) => {
        pointer.x = x;
        pointer.y = y;
        const { width, height } = element.getBoundingClientRect();
        const maxX = window.innerWidth - width - 8;
        const maxY = window.innerHeight - height - 8;
        const flipped = x + OFFSET > maxX;

        position.tx = Math.max(8, flipped ? x - width - OFFSET : x + OFFSET);
        position.ty = Math.max(8, Math.min(maxY, y + OFFSET));

        // Switching sides of the cursor would sweep the card across it
        if (flipped !== position.flipped) jump = true;
        position.flipped = flipped;

        if (jump || reducedMotion.matches) {
            cancelAnimationFrame(frame);
            frame = 0;
            position.x = position.tx;
            position.y = position.ty;
            paint();
        } else if (!frame) {
            frame = requestAnimationFrame(tick);
        }
    };

    return {
        show(event, x, y) {
            const wasHidden = !element.classList.contains('is-visible');
            const changed = setContent(event);
            // Place it before it shows: a new card appears at the cursor rather than gliding over from the last one
            target(x, y, wasHidden || changed);
            element.classList.add('is-visible');
        },
        anchor(event, rect) {
            setContent(event);
            target(rect.right - OFFSET, rect.bottom - OFFSET, true);
            element.classList.add('is-visible');
        },
        hide() {
            element.classList.remove('is-visible');
            current = null;
        },
        destroy() {
            cancelAnimationFrame(frame);
            element.remove();
        },
    };
}

/**
 * Modal sheet that swipes through `events`: open(events, index) fills a
 * horizontally snapping track and jumps to `index`.
 */
export function eventSheet({ icon }) {
    const count = h('p', { class: 'events-calendar__sheet-count', 'aria-live': 'polite' });
    const track = h('ul', { class: 'events-calendar__sheet-track', role: 'list' });
    const prev = h('button', { class: 'button events-calendar__sheet-nav', type: 'button', 'aria-label': 'Previous event' }, '<');
    const next = h('button', { class: 'button events-calendar__sheet-nav', type: 'button', 'aria-label': 'Next event' }, '>');
    const close = h('button', { class: 'button events-calendar__sheet-close', type: 'button', 'aria-label': 'Close' }, '×');
    const dialog = h(
        'dialog',
        { class: 'events-calendar__sheet', 'aria-label': 'Events' },
        h('div', { class: 'events-calendar__sheet-header' }, count, h('div', { class: 'events-calendar__sheet-actions' }, prev, next, close)),
        track
    );
    let total = 0;
    let index = 0;

    document.body.append(dialog);

    const update = () => {
        index = Math.round(track.scrollLeft / (track.clientWidth || 1));
        count.textContent = `${index + 1} / ${total}`;
        prev.disabled = index <= 0;
        next.disabled = index >= total - 1;

        for (const [position, slide] of [...track.children].entries()) {
            slide.inert = position !== index;
        }
    };

    const go = (to) => {
        track.scrollTo({
            left: Math.max(0, Math.min(total - 1, to)) * track.clientWidth,
            behavior: reducedMotion.matches ? 'auto' : 'smooth',
        });
    };

    let scrollFrame = 0;
    track.addEventListener('scroll', () => {
        cancelAnimationFrame(scrollFrame);
        scrollFrame = requestAnimationFrame(update);
    });
    prev.addEventListener('click', () => go(index - 1));
    next.addEventListener('click', () => go(index + 1));
    close.addEventListener('click', () => dialog.close());
    dialog.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft') go(index - 1);
        if (event.key === 'ArrowRight') go(index + 1);
    });
    // Tap on the backdrop closes
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) dialog.close();
    });

    return {
        open(events, start) {
            total = events.length;
            track.replaceChildren(
                ...events.map((event, position) =>
                    h(
                        'li',
                        { class: 'events-calendar__sheet-slide' },
                        eventCard(event, { link: true, icon, eager: Math.abs(position - start) <= 1 })
                    )
                )
            );
            dialog.showModal();
            track.scrollLeft = start * track.clientWidth;
            update();
            close.focus({ preventScroll: true });
        },
        destroy() {
            dialog.remove();
        },
    };
}
