wp.hooks.addFilter(
    'blocks.registerBlockType',
    'gust/core-separator-styles',
    (settings, name) => {
        if (name !== 'core/separator') return settings;
        return {
            ...settings,
            styles: (settings.styles || []).filter(
                s => !['default', 'dots', 'wide'].includes(s.name)
            ),
        };
    }
);
