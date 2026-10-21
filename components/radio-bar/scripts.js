/**
 * Radio bar refresh. Markup: ./template.php.
 *
 * Polls `sm/v1/now-playing` (data-radio-bar-endpoint) every
 * data-radio-bar-interval seconds while the tab is visible and swaps the track
 * text; the track line is a polite live region. Hides the "Now playing:" label
 * while off air. Any fetch failure leaves the last text in place.
 */

import dynamicElements from '../../assets/scripts/helpers/dynamicElements.js';

/** Mirrors RadioBar::trackLabel(). */
function trackLabel({ online = false, show = '', title = '', artist = '' }) {
    if (!online) return 'Off air';

    const parts = [show, artist, title].map((part) => part.trim()).filter(Boolean);

    return parts.length ? parts.join(' – ') : 'Live now';
}

function radioBar(root) {
    const endpoint = root.dataset.radioBarEndpoint;
    const interval = Number(root.dataset.radioBarInterval) * 1000;
    const label = root.querySelector('[data-radio-bar-label]');
    const title = root.querySelector('[data-radio-bar-title]');

    if (!endpoint || !interval || !label || !title) return;

    const refresh = async () => {
        if (document.hidden) return;

        try {
            const response = await fetch(endpoint, { headers: { Accept: 'application/json' } });
            if (!response.ok) return;

            const data = await response.json();
            const text = trackLabel(data);

            if (title.textContent !== text) title.textContent = text;
            label.hidden = !data.online;
        } catch {
            // Keep the last known text.
        }
    };

    setInterval(refresh, interval);
}

dynamicElements.define('[data-radio-bar]', radioBar);
