import debounce from 'lodash.debounce';
import isElementVisible from '../../../assets/scripts/helpers/isElementVisible.js';

export default class SiteHeader {
    constructor(element) {
        this.el = element;
        this.barEl = this.el.querySelector('.site-header__bar');
        this.burgerEl = this.el.querySelector('.site-header__burger');
        this.menuPanelEl = this.el.querySelector('.site-header__menu-panel');
        this.searchPanelEl = this.el.querySelector('.site-header__search-panel');
        this.searchDesktopEl = this.el.querySelector('.site-header__search-desktop');

        this.menuTogglerEls = this.el.querySelectorAll('.js-site-header-toggle');
        this.searchTogglerEls = this.el.querySelectorAll('.js-search-toggle');
        this.searchCloseEl = this.el.querySelector('.js-search-close');

        this.init();
    }

    init() {
        window.addEventListener(
            'resize',
            debounce(() => {
                if (!this.isMobileMode()) {
                    this.closeAllPanels();
                }
            }, 50)
        );

        this.menuTogglerEls.forEach((toggle) => {
            toggle.addEventListener('click', () => this.toggleMenu());
        });

        this.searchTogglerEls.forEach((toggle) => {
            toggle.addEventListener('click', () => this.toggleSearch());
        });

        if (this.searchCloseEl) {
            this.searchCloseEl.addEventListener('click', () => this.closeSearch());
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllPanels();
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
        this.closeSearch();

        if (this.menuPanelEl) {
            this.menuPanelEl.inert = false;
        }

        this.menuTogglerEls.forEach((toggle) => {
            toggle.setAttribute('aria-expanded', 'true');
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
        });

        document.documentElement.classList.remove('no-scroll');
        this.setInertOnSiblings(false);
    }

    toggleSearch() {
        const isSearchOpen = this.isMobileMode()
            ? this.searchPanelEl?.inert === false
            : !this.searchDesktopEl?.hidden;

        if (isSearchOpen) {
            this.closeSearch();
        } else {
            this.openSearch();
        }
    }

    openSearch() {
        this.closeMenu();

        this.searchTogglerEls.forEach((toggle) => {
            toggle.setAttribute('aria-expanded', 'true');
        });

        if (this.isMobileMode()) {
            if (this.searchPanelEl) {
                this.searchPanelEl.inert = false;
                const input = this.searchPanelEl.querySelector('input');
                if (input) {
                    input.focus();
                }
            }
            document.documentElement.classList.add('no-scroll');
            this.setInertOnSiblings(true);
        } else if (this.searchDesktopEl) {
            this.searchDesktopEl.removeAttribute('hidden');
            const input = this.searchDesktopEl.querySelector('input');
            if (input) {
                input.focus();
            }
        }
    }

    closeSearch() {
        this.searchTogglerEls.forEach((toggle) => {
            toggle.setAttribute('aria-expanded', 'false');
        });

        if (this.searchPanelEl) {
            this.searchPanelEl.inert = true;
        }

        if (this.searchDesktopEl) {
            this.searchDesktopEl.setAttribute('hidden', '');
        }

        document.documentElement.classList.remove('no-scroll');
        this.setInertOnSiblings(false);
    }

    closeAllPanels() {
        this.closeMenu();
        this.closeSearch();
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
