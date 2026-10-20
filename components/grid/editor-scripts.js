/**
 * Grid — native Gutenberg block (no ACF).
 *
 * Features:
 * - Single-row grid visualizer (hidden once first row is full)
 * - InspectorControls for columns (range slider) + breakpoint (button group)
 */

const { InnerBlocks, InspectorControls, useBlockProps, useInnerBlocksProps } =
	wp.blockEditor;
const {
	PanelBody,
	RangeControl,
	__experimentalToggleGroupControl: ToggleGroupControl,
	__experimentalToggleGroupControlOption: ToggleGroupControlOption,
} = wp.components;
const { createElement: el } = wp.element;
const { useSelect } = wp.data;

const blockName = 'theme/grid';

/* ── Block edit component ── */

const settings = {
	edit({ attributes, setAttributes, clientId }) {
		const { columns, breakpoint } = attributes;

		const innerBlockCount = useSelect(
			(select) => select('core/block-editor').getBlocks(clientId).length,
			[clientId],
		);

		const blockProps = useBlockProps({
			className: 'grid-block grid-simple alignwide',
			'data-cols': columns,
			'data-stack': breakpoint,
		});

		const innerBlocksProps = useInnerBlocksProps(blockProps, {
			orientation: 'horizontal',
		});

		const { children: innerBlocksChildren, ...wrapperProps } = innerBlocksProps;

		// Show placeholder cells for remaining slots in the first row only
		const emptyCells = innerBlockCount >= columns ? 0 : columns - innerBlockCount;
		const placeholders = Array.from({ length: emptyCells }, (_, i) =>
			el('div', { key: `ph-${i}`, className: 'grid-block__visualizer-cell' }),
		);

		return el(
			'div',
			wrapperProps,
			el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: 'Grid Settings' },
					el(RangeControl, {
						label: 'Columns',
						value: columns,
						min: 1,
						max: 6,
						step: 1,
						onChange: (val) => setAttributes({ columns: val }),
					}),
					el(ToggleGroupControl, {
						label: 'Stack below',
						value: breakpoint,
						isBlock: true,
						help: 'Breakpoint below which columns stack vertically',
						onChange: (val) => setAttributes({ breakpoint: val }),
					},
						el(ToggleGroupControlOption, { value: 'phone', label: 'Phone' }),
						el(ToggleGroupControlOption, { value: 'tablet', label: 'Tablet' }),
						el(ToggleGroupControlOption, { value: 'laptop', label: 'Laptop' }),
						el(ToggleGroupControlOption, { value: 'never', label: 'Never' }),
					),
				),
			),
			innerBlocksChildren,
			...placeholders,
		);
	},

	save() {
		return el(InnerBlocks.Content);
	},
};

wp.domReady(() => {
	if (wp.blocks.getBlockType(blockName)) {
		wp.blocks.unregisterBlockType(blockName);
	}
	wp.blocks.registerBlockType(blockName, settings);
});
