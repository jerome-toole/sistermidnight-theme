/**
 * Team caption toggles and pointer-following panels. Markup: ./template.php.
 *
 * CSS already shows a member's panel on hover and :focus-within. This keeps
 * the aria-expanded state in step with that, and lets a click or tap pin the
 * panel open (touch devices have no hover). Escape closes and returns focus
 * to the button (and overrides the :focus-within hover/focus display until
 * focus leaves the member); a click outside closes every open panel.
 *
 * With a mouse or pen the panel follows the pointer: on pointermove it is
 * fixed next to the cursor (offset 12px, flipped to the other side when it
 * would leave the viewport). It stays anchored to the portrait while the
 * member has focus or is pinned open, on touch, and under reduced motion.
 */

import dynamicElements from '../../assets/scripts/helpers/dynamicElements.js';

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const noHover = window.matchMedia('(hover: none)');

const CURSOR_CLASS = 'team__panel--cursor';
const OFFSET = 12;
const EDGE = 8;

function team(root) {
    const toggles = [...root.querySelectorAll('.team__toggle[aria-expanded]')];
    if (!toggles.length) return;

    const set = (toggle, open) => toggle.setAttribute('aria-expanded', String(open));
    const isOpen = (toggle) => toggle.getAttribute('aria-expanded') === 'true';
    const closeAll = (except) => toggles.forEach((toggle) => toggle !== except && set(toggle, false));

    toggles.forEach((toggle) => {
        const member = toggle.closest('.team__member');
        const panel = member.querySelector('[data-team-panel]');

        let pressed = false;

        const anchored = () => isOpen(toggle) || member.contains(document.activeElement);

        const unfollow = () => {
            panel.classList.remove(CURSOR_CLASS);
            panel.style.removeProperty('--team--panel-x');
            panel.style.removeProperty('--team--panel-y');
        };

        const follow = (event) => {
            if (reduceMotion || event.pointerType === 'touch' || anchored()) {
                unfollow();
                return;
            }

            panel.classList.add(CURSOR_CLASS);

            const { width, height } = panel.getBoundingClientRect();
            let x = event.clientX + OFFSET;
            let y = event.clientY + OFFSET;

            if (x + width > window.innerWidth - EDGE) x = Math.max(EDGE, event.clientX - OFFSET - width);
            if (y + height > window.innerHeight - EDGE) y = Math.max(EDGE, event.clientY - OFFSET - height);

            panel.style.setProperty('--team--panel-x', `${Math.round(x)}px`);
            panel.style.setProperty('--team--panel-y', `${Math.round(y)}px`);
        };

        member.addEventListener('pointerenter', follow);
        member.addEventListener('pointermove', follow);
        member.addEventListener('pointerleave', unfollow);

        toggle.addEventListener('pointerdown', () => {
            pressed = true;
        });

        // Touch: the whole portrait is the tap target (the button stays the accessible control)
        member.addEventListener('click', (event) => {
            if (!noHover.matches || toggle.contains(event.target) || panel.contains(event.target)) return;
            toggle.click();
        });

        toggle.addEventListener('click', () => {
            pressed = false;
            const open = !isOpen(toggle);
            delete member.dataset.dismissed;
            closeAll(toggle);
            set(toggle, open);
            unfollow();
        });

        toggle.addEventListener('focus', () => {
            unfollow();
            const fromPointer = pressed;
            pressed = false;
            if (fromPointer || 'dismissed' in member.dataset) return;
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
