/**
 * Disable "Fit text" on core/heading and core/paragraph. It's a block support,
 * not a theme.json setting, so it has to be removed at registration.
 */
wp.hooks.addFilter('blocks.registerBlockType', 'gust/disable-fit-text', (settings, name) => {
    if (!['core/heading', 'core/paragraph'].includes(name)) return settings;

    return {
        ...settings,
        supports: {
            ...settings.supports,
            typography: {
                ...settings.supports?.typography,
                fitText: false,
            },
        },
    };
});

/**
 * Register block styles for core/heading block.
 */
wp.domReady(() => {
    wp.blocks.registerBlockStyle('core/heading', {
        name: 'type-h2',
        label: 'H2 Appearance',
    });

    wp.blocks.registerBlockStyle('core/heading', {
        name: 'type-h3',
        label: 'H3 Appearance',
    });

    wp.blocks.registerBlockStyle('core/heading', {
        name: 'type-h4',
        label: 'H4 Appearance',
    });

    wp.blocks.registerBlockStyle('core/heading', {
        name: 'type-h5',
        label: 'H5 Appearance',
    });
});
