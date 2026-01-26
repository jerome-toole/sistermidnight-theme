import Swiper from 'swiper';
import { Navigation, Keyboard, Mousewheel } from 'swiper/modules';

class Gallery extends HTMLElement {
	connectedCallback() {
		const swiperEl = this.querySelector('.media-gallery__swiper');
		if (!swiperEl) return;

		this.swiper = new Swiper(swiperEl, {
			modules: [Navigation, Keyboard, Mousewheel],
			slidesPerView: 'auto',
			spaceBetween: 16,
			speed: 500,
			grabCursor: true,
			keyboard: {
				enabled: true,
				onlyInViewport: true,
			},
			mousewheel: {
				enabled: true,
				forceToAxis: true,
			},
			navigation: {
				nextEl: this.querySelector('.media-gallery__next'),
				prevEl: this.querySelector('.media-gallery__prev'),
			},
			breakpoints: {
				768: {
					spaceBetween: 24,
				},
			},
		});
	}

	disconnectedCallback() {
		this.swiper?.destroy();
	}
}

customElements.define('media-gallery', Gallery);
