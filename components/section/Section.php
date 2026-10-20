<?php

namespace Gust\Components;

use Gust\ComponentBase;

/**
 * Section Component (ACF block `theme/section`)
 *
 * The generic content section: a vertical label in the left gutter (the
 * section <h2>), a colour context, and one of three bodies:
 *
 *   text-right   InnerBlocks in the right half, optional illustration or
 *                image (440×652) in the left half.
 *   two-column   InnerBlocks are two core/group columns.
 *   scatter      InnerBlocks are Cards in a staggered grid with up to three
 *                decorative illustrations around them.
 *
 * Rendered as a block the InnerBlocks placeholder is left for ACF to fill;
 * pass `content` to render it with ready-made HTML (examples, templates).
 *
 * Usage:
 *   use Gust\Components\Section;
 *
 *   echo Section::make(label: 'Objectives', media: 'flames', content: $html);
 *   echo Section::make(layout: 'two-column', background: 'stone-beige', content: $html);
 *   echo Section::make(label: 'Join the party', layout: 'scatter', scatter_illustrations: ['sun-wavy', 'cat'], content: $html);
 */
class Section extends ComponentBase
{
    protected static string $name = 'section';

    public const BACKGROUNDS = ['dark-umber', 'stone-beige'];

    public const LAYOUTS = ['text-right', 'two-column', 'scatter'];

    /** Direct children allowed inside the block, per layout. Unregistered names are dropped at render. */
    public const ALLOWED_BLOCKS = [
        'text-right' => ['core/heading', 'core/paragraph', 'core/list', 'core/buttons', 'core/columns', 'core/group', 'theme/card', 'theme/faq', 'theme/gallery', 'theme/image-pair'],
        'two-column' => ['core/group'],
        'scatter' => ['theme/card'],
    ];

    /** Starting blocks per layout (InnerBlocks template). */
    public const TEMPLATES = [
        'text-right' => [
            ['core/heading', ['level' => 3]],
            ['core/paragraph'],
        ],
        'two-column' => [
            ['core/group', [], [['core/heading', ['level' => 3]], ['core/paragraph']]],
            ['core/group', [], [['core/heading', ['level' => 3]], ['core/paragraph']]],
        ],
        'scatter' => [],
    ];

    protected static function getDefaults(): array
    {
        return [
            'background' => 'dark-umber',
            'media' => 'none',
            'layout' => 'text-right',
        ];
    }

    /**
     * Create a new Section component.
     *
     * @param  string  $label  Vertical gutter label, rendered as the <h2>. Optional.
     * @param  string|null  $background  One of self::BACKGROUNDS.
     * @param  string|null  $layout  One of self::LAYOUTS.
     * @param  string|null  $media  none | illustration | image (text-right only). An illustration name is accepted as a shortcut.
     * @param  string|null  $illustration  Illustration name when media is "illustration".
     * @param  array|int|null  $image  Image array or attachment ID when media is "image".
     * @param  array  $scatter_illustrations  Up to three illustration names (scatter only).
     * @param  string|null  $content  Inner HTML. Null leaves the <InnerBlocks /> placeholder for ACF.
     */
    public static function make(
        string $label = '',
        ?string $background = null,
        ?string $layout = null,
        ?string $media = null,
        ?string $illustration = null,
        array|int|null $image = null,
        array $scatter_illustrations = [],
        ?string $content = null,
        bool $is_preview = false,
        array $classes = [],
        array $attributes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function transform(array $args): array
    {
        // Block fields are prefixed (section_label…) so their names stay unique for inline editing.
        foreach (['label', 'background', 'layout', 'media', 'illustration', 'image', 'scatter_illustration_1', 'scatter_illustration_2', 'scatter_illustration_3'] as $field) {
            if (array_key_exists("section_{$field}", $args)) {
                $args[$field] = $args["section_{$field}"] ?? $args[$field] ?? null;
                unset($args["section_{$field}"]);
            }
        }

        $args['label'] = trim((string) ($args['label'] ?? ''));
        $args['attributes'] ??= [];

        if (! in_array($args['background'], self::BACKGROUNDS, true)) {
            $args['background'] = self::BACKGROUNDS[0];
        }

        if (! in_array($args['layout'], self::LAYOUTS, true)) {
            $args['layout'] = self::LAYOUTS[0];
        }

        $layout = $args['layout'];

        // Media: only the text-right body has a left half to fill.
        $media = (string) ($args['media'] ?? 'none');

        if (in_array($media, Illustration::NAMES, true)) {
            $args['illustration'] = $media;
            $media = 'illustration';
        }

        $args['illustration'] = $layout === 'text-right' && $media === 'illustration' && in_array($args['illustration'] ?? '', Illustration::NAMES, true)
            ? $args['illustration']
            : '';

        $args['image'] = $layout === 'text-right' && $media === 'image'
            ? static::image($args['image'] ?? null)
            : null;

        unset($args['media']);

        // Scatter illustrations: the three ACF selects or the array arg, valid names only, max three.
        $names = $args['scatter_illustrations'] ?? [];

        foreach ([1, 2, 3] as $i) {
            $names[] = $args["scatter_illustration_{$i}"] ?? null;
            unset($args["scatter_illustration_{$i}"]);
        }

        $args['scatter_illustrations'] = $layout === 'scatter'
            ? array_slice(array_values(array_filter($names, fn ($name) => is_string($name) && in_array($name, Illustration::NAMES, true))), 0, 3)
            : [];

        // InnerBlocks configuration for the editor.
        $registry = \WP_Block_Type_Registry::get_instance();
        $args['allowed_blocks'] = array_values(array_filter(
            self::ALLOWED_BLOCKS[$layout],
            fn ($name) => $registry->is_registered($name),
        ));
        $args['template'] = self::TEMPLATES[$layout];

        $args['classes'] = array_merge([
            'content-section',
            'content-section--'.$layout,
            'alignfull',
            'has-background',
            'has-'.$args['background'].'-background-color',
        ], $args['classes'] ?? []);

        // In the editor the label is always rendered (inline-editable placeholder).
        if ($args['label'] !== '' || ! empty($args['is_preview'])) {
            $args['classes'][] = 'content-section--labelled';
        }

        if ($args['illustration'] || $args['image']) {
            $args['classes'][] = 'content-section--media';
        }

        return $args;
    }

    /** Build the 440×652 left-half image from an ACF image array or attachment ID. */
    protected static function image(array|int|null $image): ?Image
    {
        $id = is_array($image) ? (int) ($image['ID'] ?? $image['id'] ?? 0) : (int) $image;

        if ($id <= 0 || ! \wp_attachment_is_image($id)) {
            return null;
        }

        return Image::make(
            id: $id,
            size: 'large',
            sizes: '(min-width: 768px) 440px, 100vw',
            classes: ['content-section__img'],
        );
    }

    /**
     * ACF inline text editing attributes for a block field (name without the
     * section_ prefix). Empty outside an editor preview: ACF 6.8 sets its
     * "doing block preview" flag on front-end renders too, so is_preview is
     * checked here rather than trusting the helper.
     */
    public function inlineText(string $field, string $placeholder): string
    {
        if (! $this->is_preview || ! \function_exists('acf_inline_text_editing_attrs')) {
            return '';
        }

        return \acf_inline_text_editing_attrs("section_{$field}", ['placeholder' => $placeholder]);
    }

    /** ACF inline toolbar attributes for a set of block fields (names without the section_ prefix). */
    public function inlineToolbar(array $fields, string $title): string
    {
        if (! $this->is_preview || ! \function_exists('acf_inline_toolbar_editing_attrs')) {
            return '';
        }

        $fields = array_map(fn (string $field) => "section_{$field}", $fields);

        return (string) \acf_inline_toolbar_editing_attrs($fields, ['toolbar_title' => $title]);
    }
}
