/**
 * Animate on scroll. Sets [data-playing] on [data-animate] elements as they
 * scroll into view; styles.pcss does the rest.
 *
 *   <div class="animate" data-animate data-animate-stagger="80">
 *       <p class="animate__item">…</p>
 *   </div>
 *
 * data-animate-stagger="ms" delays each .animate__item by its index × ms.
 *
 * The animation replays only when the block goes back below the viewport
 * (the user scrolled up past it), not when it scrolls off the top.
 *
 * Relies on the no-js → js class swap in <head> (Gust\WordPress\Head) so items
 * start hidden only when this script will run.
 */

import dynamicElements from '../../assets/scripts/helpers/dynamicElements.js';

const observer = new IntersectionObserver(
    (entries) => {
        for (const entry of entries) {
            if (entry.isIntersecting) {
                entry.target.toggleAttribute('data-playing', true);
            } else if (entry.boundingClientRect.top > 0) {
                entry.target.toggleAttribute('data-playing', false);
            }
        }
    },
    // Extend the root a full viewport upwards, so blocks scrolled past stay played.
    { rootMargin: '100% 0px -30px 0px' }
);

function stagger(element) {
    const step = Number.parseFloat(element.dataset.animateStagger);
    if (!Number.isFinite(step) || step === 0) return;

    element.querySelectorAll('.animate__item').forEach((item, index) => {
        item.style.setProperty('--animate__item--animation-delay', `${index * step}ms`);
    });
}

dynamicElements.define('[data-animate]', (element) => {
    stagger(element);
    observer.observe(element);
});
