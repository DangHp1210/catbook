import './bootstrap';

// Auto-dismiss success flash messages: fade out after 5s and remove from DOM
document.addEventListener('DOMContentLoaded', () => {
	const flashes = document.querySelectorAll('[data-flash="success"]');
	flashes.forEach(el => {
		// ensure starting opacity and smooth transition
		el.style.opacity = '1';
		el.style.transition = 'opacity .45s ease';

		// schedule fade
		setTimeout(() => {
			el.style.opacity = '0';
		}, 5000);

		// remove after transition completes
		el.addEventListener('transitionend', (ev) => {
			if (ev.propertyName === 'opacity' && el.parentNode) {
				el.remove();
			}
		});
	});
});
