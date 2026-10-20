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
