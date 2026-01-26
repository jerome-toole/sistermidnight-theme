import dynamicElements from '../../assets/scripts/helpers/dynamicElements.js';

class NewsletterSignup {
    constructor(element) {
        this.el = element;
        this.form = element.querySelector('.newsletter-signup__form');
        this.message = element.querySelector('.newsletter-signup__message');
        this.endpoint = element.dataset.endpoint;
        this.nonce = element.dataset.nonce;
        this.listId = element.dataset.listId;

        this.init();
    }

    init() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    async handleSubmit(e) {
        e.preventDefault();

        const email = this.form.querySelector('[name="email"]').value.trim();

        if (!email || !this.isValidEmail(email)) {
            this.showMessage('Please enter a valid email address.', 'error');
            return;
        }

        this.setLoading(true);
        this.hideMessage();

        try {
            const body = { email };
            if (this.listId) {
                body.list_id = parseInt(this.listId, 10);
            }

            const response = await fetch(this.endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': this.nonce,
                },
                body: JSON.stringify(body),
            });

            const data = await response.json();

            if (data.success) {
                this.showMessage(data.message, 'success');
                this.form.reset();
            } else {
                this.showMessage(data.message || 'Something went wrong.', 'error');
            }
        } catch {
            this.showMessage('Could not connect. Please try again.', 'error');
        } finally {
            this.setLoading(false);
        }
    }

    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    setLoading(loading) {
        this.el.classList.toggle('newsletter-signup--loading', loading);
        this.form.querySelector('button').disabled = loading;
    }

    showMessage(text, type) {
        this.message.textContent = text;
        this.message.className = `newsletter-signup__message newsletter-signup__message--${type}`;
        this.message.hidden = false;
    }

    hideMessage() {
        this.message.hidden = true;
    }
}

dynamicElements.define('[data-newsletter-signup]', (el) => new NewsletterSignup(el));
