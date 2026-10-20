/**
 * Customise core/image block.
 */
wp.hooks.addFilter(
    'blocks.registerBlockType',
    'indigo/image-settings',
    (settings, name) => {
        if (name !== 'core/image') return settings;
        return {
            ...settings,
            styles: (settings.styles || []).filter(
                s => s.name !== 'default' && s.name !== 'rounded'
            ),
            supports: {
                ...settings.supports,
                align: ['wide', 'full'],
            },
        };
    }
);

/**
 * Block style for core/image: "Framed" draws a thin rule around the image.
 */
wp.domReady(() => {
    wp.blocks.registerBlockStyle('core/image', {
        name: 'framed',
        label: 'Framed',
    });
});
