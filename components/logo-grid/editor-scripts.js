/**
 * Register block styles for core/heading block.
 */
wp.domReady(() => {
    wp.blocks.registerBlockStyle('theme/logo-grid', {
        name: 'thin',
        label: 'Thin',
    });
});
