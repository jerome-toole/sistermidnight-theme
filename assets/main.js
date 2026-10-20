// Main entry point

// Popover polyfill for browsers without native support (Safari < 17,
// Firefox < 125, Chrome < 114). Vite splits it into its own chunk, so other
// browsers never download it. CSS must match its .\:popover-open class too:
// see assets/styles/4-utilities/_popover-polyfill.pcss.
if (!('popover' in HTMLElement.prototype)) {
    window.POPOVER_POLYFILL_OPTIONS = { layerName: 'base.popover-polyfill' };
    import('@oddbird/popover-polyfill');
}

import './scripts/helpers/Disclosure.js';

import '../components/*/scripts.js';
