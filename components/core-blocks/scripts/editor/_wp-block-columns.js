/**
 * Block style for core/columns: "Text right" places the column(s) in the right
 * half of the content width, leaving the left half for an illustration.
 */
wp.domReady(() => {
    wp.blocks.registerBlockStyle('core/columns', {
        name: 'text-right',
        label: 'Text right',
    });
});
