/**
 * Hero video: autoplay and the pause/play toggle. Markup: ./template.php.
 *
 * The <video> carries no autoplay attribute. This starts it unless the visitor
 * prefers reduced motion or reduced data, in which case the poster stays and
 * the toggle offers to play. The toggle is a pressed-state button: aria-pressed
 * "true" means paused. `data-hero-started` on the root reveals the video over
 * the poster once the first frame has played, and stays set so a paused video
 * holds its frame instead of snapping back to the poster.
 */

import dynamicElements from '../../assets/scripts/helpers/dynamicElements.js';

function hero(root) {
    const video = root.querySelector('[data-hero-video]');
    const toggle = root.querySelector('[data-hero-toggle]');

    if (!video || !toggle || typeof video.play !== 'function') return;

    const setPaused = (paused) => toggle.setAttribute('aria-pressed', String(paused));

    video.addEventListener('play', () => setPaused(false));
    video.addEventListener('pause', () => setPaused(true));
    video.addEventListener('playing', () => (root.dataset.heroStarted = ''), { once: true });

    toggle.addEventListener('click', () => {
        if (video.paused) {
            video.play().catch(() => setPaused(true));
        } else {
            video.pause();
        }
    });

    toggle.hidden = false;

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const reduceData = window.matchMedia('(prefers-reduced-data: reduce)').matches || navigator.connection?.saveData;

    if (!reduceMotion && !reduceData) {
        video.play().catch(() => setPaused(true));
    }
}

dynamicElements.define('[data-hero]', hero);
