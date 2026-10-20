/**
 * Site header: hide while scrolling down, peek back on scroll up, and close
 * the popover panels when they should. Markup: ../template.php.
 *
 * The menu and mobile search panels open and close without this (popover +
 * popovertarget): the toggles get aria-expanded, Escape and a click outside
 * close them, and focus goes back to the toggle. This adds:
 * - [data-site-header-hidden] on the header once the user scrolls down past
 *   it, removed as soon as they scroll up. The CSS slides it out and back.
 * - Closing a panel when a link in it is clicked (same-page links would
 *   otherwise leave it open), when keyboard focus moves out of it, and when
 *   the toggles disappear (the viewport has grown to show the nav inline).
 * - Opening panels at the header's real bottom edge, which moves when the
 *   admin bar scrolls away on small screens.
 * - Focusing the search input when the search panel opens.
 * - The desktop search toggle, as a Disclosure.
 *
 * Data attributes on the header:
 *   data-site-header-threshold="px"   scroll this far in one direction before
 *                                     hiding or showing, default 8
 */

import debounce from '../../../assets/scripts/helpers/debounce.js';
import Disclosure from '../../../assets/scripts/helpers/Disclosure.js';

export default class SiteHeader {
    constructor(element) {
        this.el = element;
        this.panels = [...element.querySelectorAll('[popover]')];
        this.buttons = element.querySelector('.site-header__buttons');

        const threshold = Number.parseFloat(element.dataset.siteHeaderThreshold);
        this.threshold = Number.isFinite(threshold) ? threshold : 8;

        this.lastY = this.scrollY();
        this.frame = 0;

        const searchDesktop = element.querySelector('.site-header__search-desktop');
        if (searchDesktop) {
            this.searchDisclosure = new Disclosure(searchDesktop, {
                collapseOnFocusout: true,
                focusWithinOnExpand: true,
                expandOnHash: false,
            });
        }

        window.addEventListener('scroll', this.onScroll, { passive: true });
        window.addEventListener('resize', this.onResize);

        for (const panel of this.panels) {
            panel.addEventListener('click', this.onPanelClick);
            panel.addEventListener('focusout', this.onPanelFocusout);
            panel.addEventListener('beforetoggle', this.onPanelBeforeToggle);
            panel.addEventListener('toggle', this.onPanelToggle);
        }
    }

    show() {
        this.el.toggleAttribute('data-site-header-hidden', false);
    }

    hide() {
        this.el.toggleAttribute('data-site-header-hidden', true);
    }

    closePanels() {
        for (const panel of this.panels) {
            if (panel.matches(':popover-open')) panel.hidePopover();
        }
    }

    destroy() {
        window.removeEventListener('scroll', this.onScroll);
        window.removeEventListener('resize', this.onResize);

        for (const panel of this.panels) {
            panel.removeEventListener('click', this.onPanelClick);
            panel.removeEventListener('focusout', this.onPanelFocusout);
            panel.removeEventListener('beforetoggle', this.onPanelBeforeToggle);
            panel.removeEventListener('toggle', this.onPanelToggle);
        }

        this.onResize.cancel();
        cancelAnimationFrame(this.frame);
    }

    /** The toggle that opens a panel. */
    toggleFor(panel) {
        return this.el.querySelector(`[popovertarget="${CSS.escape(panel.id)}"]`);
    }

    /** scrollY clamped to the scrollable range, so overscroll bounces don't count. */
    scrollY() {
        const max = document.documentElement.scrollHeight - window.innerHeight;
        return Math.min(Math.max(window.scrollY, 0), max);
    }

    update() {
        const y = this.scrollY();
        const delta = y - this.lastY;
        if (Math.abs(delta) < this.threshold) return;
        this.lastY = y;

        if (delta > 0 && y > this.el.offsetHeight) {
            this.hide();
        } else if (delta < 0) {
            this.show();
        }
    }

    onScroll = () => {
        if (this.frame) return;
        this.frame = requestAnimationFrame(() => {
            this.frame = 0;
            this.update();
        });
    };

    onResize = debounce(() => {
        if (!this.buttons) return;
        // checkVisibility() is Safari 17.4+; getClientRects() is empty for display: none.
        const visible = this.buttons.checkVisibility?.() ?? this.buttons.getClientRects().length > 0;
        if (!visible) this.closePanels();
    }, 100);

    onPanelClick = (event) => {
        if (event.target instanceof Element && event.target.closest('a[href]')) {
            event.currentTarget.hidePopover?.();
        }
    };

    onPanelFocusout = (event) => {
        const panel = event.currentTarget;
        const next = event.relatedTarget;
        if (!(next instanceof Node) || !panel.matches(':popover-open')) return;
        if (panel.contains(next) || this.toggleFor(panel)?.contains(next)) return;
        panel.hidePopover();
    };

    /** Open below wherever the header's bottom edge is right now. */
    onPanelBeforeToggle = (event) => {
        if (event.newState !== 'open') return;
        const bottom = Math.max(this.el.getBoundingClientRect().bottom, 0);
        this.el.style.setProperty('--site-header__panel--top', `${bottom}px`);
    };

    /** Opening a panel brings the header back, and it stays when the panel closes. */
    onPanelToggle = (event) => {
        if (event.newState !== 'open') return;
        this.show();

        if (event.currentTarget.matches('.site-header__search-panel')) {
            event.currentTarget.querySelector('input:not([type="hidden"])')?.focus();
        }
    };
}
