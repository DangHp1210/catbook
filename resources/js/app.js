import './bootstrap';

// Auto-dismiss flash messages: fade out, collapse smoothly, then remove from DOM.
document.addEventListener('DOMContentLoaded', () => {
	const flashes = document.querySelectorAll('[data-flash]');

	flashes.forEach((el) => {
		const duration = Number(el.dataset.flashDuration || 4000);
		const startHeight = el.scrollHeight;
		let removed = false;

		el.style.maxHeight = `${startHeight}px`;

		const removeFromDom = () => {
			if (removed) return;
			removed = true;
			el.removeEventListener('transitionend', handleTransitionEnd);
			el.remove();
		};

		const handleTransitionEnd = (event) => {
			if (event.propertyName === 'max-height' || event.propertyName === 'opacity') {
				removeFromDom();
			}
		};

		const dismiss = () => {
			if (removed) return;

			// Freeze current height first so the collapse animates instead of jumping.
			el.style.maxHeight = `${el.scrollHeight}px`;

			requestAnimationFrame(() => {
				el.classList.add('cb-flash--closing');
				el.addEventListener('transitionend', handleTransitionEnd);
			});

			window.setTimeout(removeFromDom, 700);
		};

		window.setTimeout(dismiss, duration);
	});
});
