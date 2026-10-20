/**
 * Block styles for core/paragraph: "Large" (intro copy, type-large) and
 * "Small" (legal, type-small). The class names match the .is-style-type-*
 * rules in 3-patterns/_type-styles.pcss.
 */
wp.domReady(() => {
    wp.blocks.registerBlockStyle('core/paragraph', {
        name: 'type-large',
        label: 'Large',
    });

    wp.blocks.registerBlockStyle('core/paragraph', {
        name: 'type-small',
        label: 'Small',
    });
});
