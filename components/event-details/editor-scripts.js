/**
 * Event Details — open ACF's expanded editor when the block is clicked.
 *
 * ACF has no API for opening the expanded editor, so a click on the panel
 * (outside an inline-editable value) selects the block and presses its
 * "Edit Block" toolbar button. The editor canvas is an iframe that can be
 * replaced, so the listener is attached to each new canvas document.
 */

const { subscribe, select, dispatch } = wp.data;

const INLINE_SELECTOR = '[data-acf-inline-contenteditable], [data-acf-inline-fields]';
const attached = new WeakSet();

function editButton() {
	const label = window.acf?.__ ? window.acf.__('Edit Block') : 'Edit Block';

	return [...document.querySelectorAll('.block-editor-block-toolbar button')].find(
		(button) => button.getAttribute('aria-label') === label,
	);
}

/** The toolbar renders after selection; try for a few frames. */
function pressEditButton(attempts = 20) {
	const button = editButton();

	if (button) {
		button.click();
	} else if (attempts > 0) {
		requestAnimationFrame(() => pressEditButton(attempts - 1));
	}
}

function onCanvasClick(event) {
	const panel = event.target.closest?.('.event-details');

	if (!panel || event.target.closest(INLINE_SELECTOR)) {
		return;
	}

	const clientId = panel.closest('[data-block]')?.dataset.block;

	if (!clientId) {
		return;
	}

	if (select('core/block-editor').getSelectedBlockClientId() !== clientId) {
		dispatch('core/block-editor').selectBlock(clientId);
	}

	pressEditButton();
}

function attachToCanvas() {
	const canvas = document.querySelector('iframe[name="editor-canvas"]')?.contentDocument;

	if (canvas && !attached.has(canvas)) {
		attached.add(canvas);
		canvas.addEventListener('click', onCanvasClick);
	}
}

subscribe(attachToCanvas);
