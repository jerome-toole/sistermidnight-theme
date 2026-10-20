wp.hooks.addFilter(
    'blocks.registerBlockType',
    'gust/core-button-styles',
    (settings, name) => {
        if (name !== 'core/button') return settings;
        return {
            ...settings,
            styles: (settings.styles || []).filter(
                s => !['default', 'fill', 'outline', 'squared'].includes(s.name)
            ),
        };
    }
);
