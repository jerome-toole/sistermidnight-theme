wp.hooks.addFilter(
    'blocks.registerBlockType',
    'gust/core-quote-styles',
    (settings, name) => {
        if (name !== 'core/quote') return settings;
        return {
            ...settings,
            styles: (settings.styles || []).filter(
                s => !['default', 'plain'].includes(s.name)
            ),
        };
    }
);
