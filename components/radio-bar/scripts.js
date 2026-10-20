/**
 * Radio bar refresh. Markup: ./template.php.
 *
 * Polls `sm/v1/now-playing` (data-radio-bar-endpoint) every
 * data-radio-bar-interval seconds while the tab is visible and swaps the track
 * text; the track line is a polite live region. Hides the line while the
 * station is offline and shows it again when it comes back. Any fetch failure
 * leaves the last text in place.
 */

import dynamicElements from '../../assets/scripts/helpers/dynamicElements.js';

/** Mirrors RadioBar::trackLabel(). */
function trackLabel({ title = '', artist = '' }) {
    const parts = [artist.trim(), title.trim()].filter(Boolean);

    return parts.length ? parts.join(' – ') : 'Live now';
}

function radioBar(root) {
    const endpoint = root.dataset.radioBarEndpoint;
    const interval = Number(root.dataset.radioBarInterval) * 1000;
    const track = root.querySelector('[data-radio-bar-track]');
    const title = root.querySelector('[data-radio-bar-title]');

    if (!endpoint || !interval || !track || !title) return;

    const refresh = async () => {
        if (document.hidden) return;

        try {
            const response = await fetch(endpoint, { headers: { Accept: 'application/json' } });
            if (!response.ok) return;

            const data = await response.json();
            const label = trackLabel(data);

            if (title.textContent !== label) title.textContent = label;
            track.hidden = !data.online;
        } catch {
            // Keep the last known text.
        }
    };

    setInterval(refresh, interval);
}

dynamicElements.define('[data-radio-bar]', radioBar);
