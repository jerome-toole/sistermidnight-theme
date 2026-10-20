/**
 * PostCSS Color System Plugin
 * Generates CSS custom properties from color configuration
 */

import { readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const CONFIG_PATH = resolve(__dirname, '../theme-config.json');

function loadColors() {
	return JSON.parse(readFileSync(CONFIG_PATH, 'utf-8')).colors;
}

// Refreshed on every PostCSS run so changes to theme-config.json are picked up in dev
let colors = loadColors();

/**
 * Convert hex to HSL
 */
function hexToHSL(hex) {
	// Remove the hash if present
	hex = hex.replace('#', '');

	// Expand shorthand hex (e.g., "fff" → "ffffff")
	if (hex.length === 3) {
		hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
	}

	// Parse r, g, b values
	const r = parseInt(hex.substring(0, 2), 16) / 255;
	const g = parseInt(hex.substring(2, 4), 16) / 255;
	const b = parseInt(hex.substring(4, 6), 16) / 255;

	const max = Math.max(r, g, b);
	const min = Math.min(r, g, b);
	let h,
		s,
		l = (max + min) / 2;

	if (max === min) {
		h = s = 0; // achromatic (grayscale)
	} else {
		const d = max - min;
		s = l > 0.5 ? d / (2 - max - min) : d / (max + min);

		switch (max) {
			case r:
				h = ((g - b) / d + (g < b ? 6 : 0)) / 6;
				break;
			case g:
				h = ((b - r) / d + 2) / 6;
				break;
			case b:
				h = ((r - g) / d + 4) / 6;
				break;
		}
	}

	h = Math.round(h * 360);
	s = Math.round(s * 100);
	l = Math.round(l * 100);

	return `${h} ${s}% ${l}%`;
}

/**
 * Resolve named color references
 */
function resolveColor(colorName) {
	const colorMap = colors.base[colorName];
	if (!colorMap) {
		return null;
	}

	// If it references another color, resolve recursively
	if (colorMap.namedColor) {
		return resolveColor(colorMap.namedColor);
	}

	return colorMap.color;
}

/**
 * Resolve the base color name (follows namedColor references)
 */
function resolveColorName(colorName) {
	const colorMap = colors.base[colorName];
	if (colorMap?.namedColor) {
		return resolveColorName(colorMap.namedColor);
	}

	return colorName;
}

/**
 * Resolve full color config (follows namedColor references)
 */
function resolveColorConfig(colorName) {
	const colorMap = colors.base[colorName];
	if (!colorMap) {
		return null;
	}

	if (colorMap.namedColor) {
		return resolveColorConfig(colorMap.namedColor);
	}

	return colorMap;
}

/**
 * Get foreground color value for a named color
 * @param {string} colorName - Color name (e.g., 'accent', 'accent')
 * @returns {string|null} - Foreground color value or null
 */
export function getForegroundColor(colorName) {
	const config = resolveColorConfig(colorName);
	return config?.foreground || null;
}

/**
 * PostCSS plugin
 */
export default function postcssColorSystem() {
	return {
		postcssPlugin: 'postcss-color-system',
		Once(root, { Rule, Declaration, AtRule }) {
			// Re-read config on every run so changes in dev are picked up immediately
			colors = loadColors();

			// Attach root.source to every programmatic node so Vite doesn't warn
			// "PostCSS plugin did not pass the from option"
			const src = root.source;
			const newDecl = (opts) => new Declaration({ ...opts, source: src });
			const newRule = (opts) => new Rule({ ...opts, source: src });
			const newAtRule = (opts) => new AtRule({ ...opts, source: src });

			// Generate @source inline for Tailwind v4 JIT to include dynamic classes
			const colorNames = Object.keys(colors.base);
			const dynamicClasses = colorNames.flatMap((name) => [
				`has-${name}-background-color`,
				`color-context-${name}`,
				`foreground-from-${name}`,
			]);
			const sourceRule = newAtRule({
				name: 'source',
				params: `inline("${dynamicClasses.join(' ')}")`,
			});
			root.prepend(sourceRule);

			// Inject colors into @theme for Tailwind utilities
			root.walkAtRules('theme', (atRule) => {
				Object.entries(colors.base).forEach(([colorName]) => {
					const prop = `--color-${colorName}`;

					// Check if already exists
					let exists = false;
					atRule.walkDecls(prop, () => {
						exists = true;
					});

					if (!exists) {
						atRule.append(
							newDecl({
								prop: prop,
								value: `var(--color-${colorName})`,
							}),
						);
					}
				});
			});

			// Generate color variables on the existing :root rule (or a new one)
			let targetRule = null;
			root.walkRules(':root', (rule) => {
				if (!targetRule) targetRule = rule;
			});

			if (!targetRule) {
				targetRule = newRule({ selector: ':root' });
			}

			// Generate custom properties for each color
			Object.entries(colors.base).forEach(([colorName, colorMap]) => {
				const propName = `--color-${colorName}`;

				// Resolve the actual color value
				const colorValue = resolveColor(colorName);

				if (colorValue) {
					// Add main color property
					targetRule.append(
						newDecl({
							prop: propName,
							value: colorValue,
						}),
					);

					// Add HSL variant
					const hslValue = hexToHSL(colorValue);
					targetRule.append(
						newDecl({
							prop: `${propName}--hsl`,
							value: hslValue,
						}),
					);

					// Add individual H, S, L components
					const [h, s, l] = hslValue.split(' ');
					targetRule.append(newDecl({ prop: `${propName}--h`, value: h }));
					targetRule.append(newDecl({ prop: `${propName}--s`, value: s }));
					targetRule.append(newDecl({ prop: `${propName}--l`, value: l }));
				}

				// Add foreground color if specified (aliases take their target's)
				const foreground = resolveColorConfig(colorName)?.foreground;
				if (foreground) {
					targetRule.append(
						newDecl({
							prop: `${propName}--foreground`,
							value: foreground,
						}),
					);
				}

				// Add additional properties if specified
				// if (colorMap.properties) {
				// 	Object.entries(colorMap.properties).forEach(([prop, value]) => {
				// 		targetRule.append(newDecl({ prop, value }));
				// 	});
				// }
			});

			// Append the rule if it was newly created
			if (!targetRule.parent) {
				root.append(targetRule);
			}

			// Generate utility classes using @utility syntax for Tailwind v4
			const baseColors = colors.base;

			// Helper function to create background context utilities
			const createBackgroundContextUtility = (className, colorName, colorMap) => {
				const utilityRule = newAtRule({
					name: 'utility',
					params: className
				});

				// Set background color via custom property. Aliases point at their base
				// color, since a context's properties may redefine role colors like --color-accent.
				if (colorName !== 'background') {
					utilityRule.append(
						newDecl({
							prop: '--color-background',
							value: `var(--color-${resolveColorName(colorName)})`,
						}),
					);
				}

				utilityRule.append(
					newDecl({
						prop: 'background-color',
						value: 'var(--color-background)',
					}),
				);

				// Set foreground color if defined
				if (colorMap.foreground) {
					utilityRule.append(
						newDecl({
							prop: '--color-foreground',
							value: colorMap.foreground,
						}),
					);
					utilityRule.append(
						newDecl({
							prop: '--focus--color',
							value: 'var(--color-foreground)',
						}),
					);
					utilityRule.append(
						newDecl({
							prop: 'color',
							value: 'var(--color-foreground)',
						}),
					);
				}

				// Set additional properties (link colors, etc.)
				if (colorMap.properties) {
					Object.entries(colorMap.properties).forEach(([prop, value]) => {
						utilityRule.append(newDecl({ prop, value }));
					});
				}

				return utilityRule;
			};

			Object.entries(baseColors).forEach(([colorName, colorMap]) => {
				// Resolve the actual color data (handles namedColor references)
				const resolvedColor = resolveColor(colorName);
				const actualColorMap = colorMap.namedColor ? colors.base[colorMap.namedColor] : colorMap;

				// Skip if we can't resolve the color
				if (!resolvedColor || !actualColorMap) return;

				// Generate color-context-{color} utilities
				const contextUtilityRule = createBackgroundContextUtility(`color-context-${colorName}`, colorName, actualColorMap);
				root.append(contextUtilityRule);

				// Generate has-{color}-background-color utilities (WordPress alias)
				const hasUtilityRule = createBackgroundContextUtility(`has-${colorName}-background-color`, colorName, actualColorMap);
				root.append(hasUtilityRule);

				// Generate foreground-from-{color} utilities
				if (actualColorMap.foreground) {
					const foregroundUtilityRule = newAtRule({
						name: 'utility',
						params: `foreground-from-${colorName}`
					});

					foregroundUtilityRule.append(
						newDecl({
							prop: '--color-foreground',
							value: actualColorMap.foreground,
						}),
					);
					foregroundUtilityRule.append(
						newDecl({
							prop: '--focus--color',
							value: 'var(--color-foreground)',
						}),
					);
					foregroundUtilityRule.append(
						newDecl({
							prop: 'color',
							value: 'var(--color-foreground)',
						}),
					);

					// Set additional properties (link colors, etc.) for foreground utilities
					if (actualColorMap.properties) {
						Object.entries(actualColorMap.properties).forEach(([prop, value]) => {
							foregroundUtilityRule.append(newDecl({ prop, value }));
						});
					}

					root.append(foregroundUtilityRule);
				}
			});
		},
	};
}

postcssColorSystem.postcss = true;
