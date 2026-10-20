/**
 * Site header: the mobile menu is a native <dialog>. Markup: ./template.php.
 *
 * The dialog does most of the work itself: showModal() traps focus, Escape
 * closes it and focus returns to the "Menu" button. In browsers with invoker
 * commands (command / commandfor on the buttons) the open and close clicks
 * need no JS either. This module adds:
 * - the open and close clicks where invoker commands are not supported,
 * - closing on a link click (a same-page link would otherwise leave it open),
 * - closing when the viewport grows past --site-header--breakpoint and the
 *   nav shows inline in the bar.
 */

import dynamicElements from '../../assets/scripts/helpers/dynamicElements.js';

function siteHeader(header) {
    const dialog = header.querySelector('.site-header__dialog');
    const opener = header.querySelector('[data-site-header-open]');

    if (!dialog || !opener) return;

    if (!('commandForElement' in opener)) {
        opener.addEventListener('click', () => dialog.showModal());
        dialog.querySelector('[data-site-header-close]')?.addEventListener('click', () => dialog.close());
    }

    dialog.addEventListener('click', (event) => {
        if (event.target.closest('a[href]')) dialog.close();
    });

    const breakpoint = getComputedStyle(header).getPropertyValue('--site-header--breakpoint').trim() || '768px';
    const inline = window.matchMedia(`(min-width: ${breakpoint})`);

    inline.addEventListener('change', () => {
        if (inline.matches && dialog.open) dialog.close();
    });
}

dynamicElements.define('[data-site-header]', siteHeader);
