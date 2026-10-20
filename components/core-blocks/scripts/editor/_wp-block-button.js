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

/**
 * Block styles for core/button: "Primary" (default, burnt-sienna fill with an
 * arrow) and "Link" (underlined text with an arrow). Styled in
 * core-blocks/styles/_wp-block-buttons.pcss.
 */
wp.domReady(() => {
    wp.blocks.registerBlockStyle('core/button', {
        name: 'primary',
        label: 'Primary',
        isDefault: true,
    });

    wp.blocks.registerBlockStyle('core/button', {
        name: 'link',
        label: 'Link',
    });
});
