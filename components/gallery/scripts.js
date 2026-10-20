/**
 * Gallery carousel. Markup: ./template.php.
 *
 * The strip is a native horizontal scroller, so it works without JS. For a
 * carousel-style gallery Blossom is lazy loaded (its own chunk, fetched once,
 * only when a carousel is on the page) to add drag physics and snap-aware
 * prev/next. Under reduced motion Blossom is skipped, since its prev/next
 * always smooth-scroll, and the arrows jump the strip instantly instead.
 */

import dynamicElements from '../../assets/scripts/helpers/dynamicElements.js';

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

let blossomModule;
const loadBlossom = () => (blossomModule ??= import('@blossom-carousel/core'));

async function gallery(root) {
    const track = root.querySelector('[data-gallery-track]');
    if (!track) return;

    let prev;
    let next;

    if (reduceMotion) {
        const step = () => track.clientWidth * 0.8;
        prev = () => track.scrollBy({ left: -step(), behavior: 'instant' });
        next = () => track.scrollBy({ left: step(), behavior: 'instant' });
    } else {
        const { Blossom } = await loadBlossom();
        const blossom = Blossom(track, {});
        blossom.init();
        prev = () => blossom.prev({ align: 'start' });
        next = () => blossom.next({ align: 'start' });
    }

    root.querySelector('[data-gallery-prev]')?.addEventListener('click', prev);
    root.querySelector('[data-gallery-next]')?.addEventListener('click', next);
}

dynamicElements.define('.gallery--carousel', gallery);
