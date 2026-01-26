const pageNavs = document.querySelectorAll('.page-navigation');

pageNavs.forEach((nav) => {
	const links = nav.querySelectorAll('.page-navigation__link');
	const sections = [];

	links.forEach((link) => {
		const href = link.getAttribute('href');
		if (href?.startsWith('#')) {
			const section = document.getElementById(href.slice(1));
			if (section) {
				sections.push({ link, section });
			}
		}
	});

	if (sections.length === 0) return;

	const observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				const item = sections.find((s) => s.section === entry.target);
				if (!item) return;

				if (entry.isIntersecting) {
					links.forEach((l) => l.classList.remove('is-active'));
					item.link.classList.add('is-active');
				}
			});
		},
		{
			rootMargin: '-20% 0px -60% 0px',
		}
	);

	sections.forEach(({ section }) => observer.observe(section));
});
