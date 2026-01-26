import debounce from 'lodash.debounce';
import isElementVisible from '../../../assets/scripts/helpers/isElementVisible.js';

export default class SiteHeader {
    constructor(element) {
        this.el = element;
        this.burgerEl = this.el.querySelector('.site-header__burger');
        this.menuPanelEl = this.el.querySelector('.site-header__panel');
        this.menuTogglerEls = this.el.querySelectorAll('.js-site-header-toggle');

        this.init();
    }

    init() {
        window.addEventListener(
            'resize',
            debounce(() => {
                if (!this.isMobileMode()) {
                    this.closeMenu();
                }
            }, 50)
        );

        this.menuTogglerEls.forEach((toggle) => {
            toggle.addEventListener('click', () => this.toggleMenu());
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeMenu();
            }
        });
    }

    toggleMenu() {
        if (this.menuPanelEl?.inert === false) {
            this.closeMenu();
        } else {
            this.openMenu();
        }
    }

    openMenu() {
        if (this.menuPanelEl) {
            this.menuPanelEl.inert = false;
        }

        this.menuTogglerEls.forEach((toggle) => {
            toggle.setAttribute('aria-expanded', 'true');
            toggle.setAttribute('aria-label', 'Close menu');
        });

        document.documentElement.classList.add('no-scroll');
        this.setInertOnSiblings(true);
    }

    closeMenu() {
        if (this.menuPanelEl) {
            this.menuPanelEl.inert = true;
        }

        this.menuTogglerEls.forEach((toggle) => {
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'Open menu');
        });

        document.documentElement.classList.remove('no-scroll');
        this.setInertOnSiblings(false);
    }

    setInertOnSiblings(inert) {
        const parent = this.el.parentElement;
        if (!parent) return;

        Array.from(parent.children).forEach((sibling) => {
            if (sibling !== this.el && sibling.nodeType === Node.ELEMENT_NODE) {
                sibling.inert = inert;
            }
        });
    }

    isMobileMode() {
        return isElementVisible(this.burgerEl);
    }
}
