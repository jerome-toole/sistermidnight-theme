/**
 * Team caption toggles. Markup: ./template.php.
 *
 * CSS already shows a member's panel on hover and :focus-within. This keeps
 * the aria-expanded state in step with that, and lets a click or tap pin the
 * panel open (touch devices have no hover). Escape closes and returns focus
 * to the button (and overrides the :focus-within hover/focus display until
 * focus leaves the member); a click outside closes every open panel.
 */

import dynamicElements from '../../assets/scripts/helpers/dynamicElements.js';

function team(root) {
    const toggles = [...root.querySelectorAll('.team__toggle[aria-expanded]')];
    if (!toggles.length) return;

    const set = (toggle, open) => toggle.setAttribute('aria-expanded', String(open));
    const isOpen = (toggle) => toggle.getAttribute('aria-expanded') === 'true';
    const closeAll = (except) => toggles.forEach((toggle) => toggle !== except && set(toggle, false));

    toggles.forEach((toggle) => {
        const member = toggle.closest('.team__member');

        toggle.addEventListener('click', () => {
            const open = !isOpen(toggle);
            delete member.dataset.dismissed;
            closeAll(toggle);
            set(toggle, open);
        });

        toggle.addEventListener('focus', () => {
            if ('dismissed' in member.dataset) return;
            closeAll(toggle);
            set(toggle, true);
        });

        member.addEventListener('focusout', (event) => {
            if (member.contains(event.relatedTarget)) return;
            delete member.dataset.dismissed;
            set(toggle, false);
        });

        member.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape' || !isOpen(toggle)) return;
            event.preventDefault();
            // CSS keeps a focused member's panel open (:focus-within); the
            // dismissed flag overrides that until focus leaves the member.
            member.dataset.dismissed = '';
            set(toggle, false);
            toggle.focus();
        });
    });

    document.addEventListener('pointerdown', (event) => {
        if (!root.contains(event.target)) closeAll();
    });
}

dynamicElements.define('.team', team);
