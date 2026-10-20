/**
 * Remove any embed variations that aren't in the allow list.
 * Filtered at registration: the variations are registered with the block, so a
 * domReady unregister runs too early to catch them.
 * Note: The generic embed block (which allows any provider) can't be removed.
 */
const allowedEmbedVariations = ['youtube', 'vimeo'];

wp.hooks.addFilter('blocks.registerBlockType', 'gust/embed-variations', (settings, name) => {
    if (name === 'core/embed') {
        settings.variations = settings.variations?.filter((v) => allowedEmbedVariations.includes(v.name));
    }
    return settings;
});
