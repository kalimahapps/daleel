const findNext = (el, selector) => {
	let placeholder = el.nextElementSibling;
	let sibling = null;
	while (placeholder) {
		if (placeholder.classList.contains(selector)) {
			sibling = placeholder;
			break;
		}

		placeholder = placeholder.nextElementSibling;
	}

	return sibling;
};

document.querySelectorAll('.toggle').forEach((toggle) => {
	toggle.addEventListener('click', () => {
		const isToggled = toggle.getAttribute("data-expanded") === 'true';
		toggle.setAttribute("data-expanded", isToggled ? 'false' : 'true');

		const targetId = toggle.getAttribute("data-target");
		const targetGroup = document.querySelector(`[data-group="${targetId}"]`);
		targetGroup.setAttribute("data-show", isToggled ? 'false' : 'true');
	})
});


// Update theme on click
const toggleElements = document.querySelectorAll('.dark-toggle');
toggleElements.forEach((toggleElement) => {
	toggleElement.addEventListener('click', () => {
		updateTheme(true);
	});
});

/**
 * Handle scroll TOC change
 */
const tocMarker = document.querySelector('#toc-marker');
const observer = new IntersectionObserver(entries => {
	entries.forEach(entry => {
		const id = entry.target.getAttribute('id');
		const navElement = document.querySelector(`#toc a[href="#${id}"]`);

		const navElementPosition = navElement.offsetTop;
		const { top: sectionTop } = entry.boundingClientRect;

		// Check if the section is above the viewport
		if (sectionTop < 0) {
			tocMarker.style.top = `${navElementPosition}px`;
		}
	});
});

// Track all sections that have an `id` applied
document.querySelectorAll('h2[id], h3[id]').forEach((section) => {
	observer.observe(section);
});

/**
 * Toggle toc on small screens
 */
const tocToggle = document.querySelector('#toc-toggle');
const tocContent = document.querySelector('#toc .toc-content');
if (tocToggle) {
	tocToggle.addEventListener('click', () => {
		tocContent.classList.toggle('hidden');
	});
}

/**
 * Toggle sidebar on small screens
 */
const sidebarToggle = document.querySelector('#sidebar-toggle');
sidebarToggle.addEventListener('click', () => {
	const body = document.querySelector('body');
	const currentStatus = body.getAttribute('data-show-aside') === 'true';
	body.setAttribute('data-show-aside', currentStatus === 'true' ? 'false' : 'true');
});


/**
 * Toggle links on small screens
 */
const linksToggle = document.querySelector('#links-toggle');
linksToggle.addEventListener('click', () => {
	const body = document.querySelector('body');
	const currentState = body.getAttribute('data-show-links') === 'true';
	body.setAttribute('data-show-links', currentState ? 'false' : 'true');
});

// Hide sidebar and links on backdrop click
const backdrop = document.querySelector('#backdrop');
backdrop.addEventListener('click', () => {
	const body = document.querySelector('body');
	body.setAttribute('data-show-aside', 'false');
	body.setAttribute('data-show-links', 'false');
});


/**
 * Handle scroll to top
 */
const scrollToTop = document.querySelector('#scroll-to-top');
const mainWrapper = document.querySelector('#main-wrapper');

// Show scroll to top button when scrolled down
mainWrapper.addEventListener('scroll', (e) => {
	const currentScroll = e.target.scrollTop;
	const isScrolledDown = currentScroll > 100;
	document.querySelector('body').setAttribute('data-show-scroll-top', isScrolledDown ? 'true' : 'false');
});

scrollToTop.addEventListener('click', () => {
	mainWrapper.scrollTo({
		top: 0,
		behavior: 'smooth'
	});
});