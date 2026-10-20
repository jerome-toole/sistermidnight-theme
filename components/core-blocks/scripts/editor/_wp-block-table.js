wp.hooks.addFilter(
    'blocks.registerBlockType',
    'gust/core-table-styles',
    (settings, name) => {
        if (name !== 'core/table') return settings;
        return {
            ...settings,
            styles: (settings.styles || []).filter(
                s => !['regular', 'stripes'].includes(s.name)
            ),
        };
    }
);
