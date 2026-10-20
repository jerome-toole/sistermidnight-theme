# CSS Architecture

## Overview

Tailwind + BEM hybrid. Use BEM with custom patterns in per-component `styles.pcss` files. Use Tailwind sparingly directly in templates for layout purposes only. Never use raw Tailwind typography or color classes inside component CSS — always use the custom type and color systems.


### Styling Rules

**Colors**: Defined in `theme-config.json`, processed at build time into CSS custom properties. Each color generates:
- `--color-{name}` — raw hex
- `--color-{name}--foreground` — contrasting text color
- `color-context-{name}` utility — sets background AND `--color-background`, `--color-foreground`, link/focus colors together

Always use `color-context-{name}` for sections with a background — never set `background-color` alone. Within a color context use `var(--color-foreground)` / `var(--color-background)` for text and backgrounds.

**Spacing**: Use `space(px)` (converts px → rem). Responsive: `spaceFluid(min, max)`. Layout tokens: `var(--space-layout)`, `var(--container-padding)`.

**Patterns vs utilities**: Use Tailwind utilities (`flex`, `gap-4`, `hidden`) for layout directly in templates. Use `@apply` with custom patterns (`type-h2`, `color-context-name`) inside component `.pcss` files — not as inline template classes.


```php
<!-- Template: Tailwind for layout, BEM for component classes -->
<div class="card flex flex-col gap-4">
    <h2 class="card__title type-h2"><?= $title ?></h2>
    <div class="card__body prose"><?= $content ?></div>
</div>
```

```pcss
/* styles.pcss: custom patterns via @apply, BEM structure */
.card {
    &__body { color: var(--color-foreground); }
}
```

## File Structure

```
assets/styles/
├── 1-theme/         # Design tokens: space, type scale, colors, widths
├── 2-base/          # Normalize, fonts, base element styles
├── 3-patterns/      # Reusable patterns: content-grid, buttons, forms, type-styles
└── 4-utilities/     # Helpers: alignment, screen-reader, etc.
```

Component styles live in `components/{name}/styles.pcss` and are auto-bundled.

---

## Color System

Colors defined in `theme-config.json`, processed by `dev-scripts/postcss-color-system.js` at build time.

### What's generated per color

```
--color-{name}              raw hex value
--color-{name}--hsl         HSL format
--color-{name}--foreground  contrasting text color (defined in theme-config.json)
```

### Contextual properties (change with context)

```
--color-foreground   current text color (set by color-context utility)
--color-background   current background color (set by color-context utility)
```

### Color context utilities

`color-context-{name}` — the primary way to apply background colors. Sets:
- `background-color: var(--color-{name})`
- `--color-background: var(--color-{name})`
- `--color-foreground: var(--color-{name}--foreground)`
- `color: var(--color-foreground)`
- `--link--color`, `--link--color-hover`, `--focus--color`

```pcss
/* DO: color-context sets bg + fg + links together */
.my-section {
    @apply color-context-darkgreen;
}

/* DO: use semantic variables inside a context */
.my-section__text {
    color: var(--color-foreground);
    background-color: var(--color-background);
}

/* DON'T: raw background without foreground */
.my-section {
    background-color: var(--color-darkgreen); /* missing foreground! */
}
```

**Also available:**
- `has-{name}-background-color` — WordPress block editor alias (same as color-context)
- `foreground-from-{name}` — sets only foreground/text color, no background

### Available colors

Colors are defined per project in [`theme-config.json`](../../theme-config.json). Check that file for the current color names before using any `color-context-{name}`, `--color-{name}`, or `foreground-from-{name}` utilities.

---

## Typography

Type utilities in `assets/styles/3-patterns/_type-styles.pcss`. Check that file for the current names before using any `type-{name}` utility.. Each bundles font-family, size, weight, line-height, and letter-spacing. Use with `@apply` in CSS or as a class in templates. Never raw Tailwind font/size properties. Defined in `assets/styles/3-patterns/_type-styles.pcss`. Semantic names - e.g.: `type-hero`, `type-h1`–`type-h6`, `type-base`, `type-meta`.

**Responsive font sizing with `rfs()`:**
```pcss
@utility type-h3 {
    font-size: rfs(24, 36);  /* 24px at min viewport, 36px at max */
}
```

---

## Spacing

`space(px)` converts a px value to rem. Use it everywhere instead of raw values.

```pcss
.component {
    padding: space(16);           /* 1rem */
    margin-block: space(32);      /* 2rem */
}
```

**Responsive with `spaceFluid()`:**
```pcss
.component {
    padding: spaceFluid(16, 32);  /* fluid from 1rem → 2rem */
}
```

**Layout tokens:**
| Variable | Value | Use for |
|----------|-------|---------|
| `var(--space-layout)` | 32px → 64px fluid | Block/section spacing |
| `var(--container-padding)` | 16px → 30px fluid | Side padding |
| `var(--space-base)` | 16px | Inline text element spacing |

---

## Content Flow

`.content-flow` manages vertical spacing between direct children using the [Every Layout flow pattern](https://every-layout.dev/layouts/stack/).

```pcss
/* Applied automatically to > * + * */
margin-block-start: var(--flow-space, 1em);
```

Override `--flow-space` on any element to control its top spacing:

```pcss
.my-component {
    --flow-space: var(--space-base);  /* tighter spacing above this element */
}
```

Default `--flow-space` by context:

| Element | Value |
|---------|-------|
| Root default | `var(--space-layout)` |
| `p, ul, ol, h1–h6` | `var(--space-base)` |
| `.wp-block-heading` | `var(--heading--margin-top)` |

`site-main` applies `.content-flow` to its inner wrapper by default. Test at `/_dev/blocks-context/`.

---

## Content Grid

WordPress block content layout — children of `.content-grid` span the 12-col wide area by default.

```
[full-start] gutter [wide-start] 12-cols [wide-end] gutter [full-end]
```

| Class | Grid column |
|-------|-------------|
| `alignfull` | full viewport width |
| `alignwide` | content width (wide) |
| `alignleft` | e.g. columns 2–8 |
| `alignright` | e.g. columns 8–14 |
| `alignprose` | wide + max-width |

---

## Utility Functions

| Function | Example | Output |
|----------|---------|--------|
| `space(px)` | `space(16)` | `1rem` |
| `spaceFluid(min, max)` | `spaceFluid(16, 32)` | fluid clamp |
| `rfs(min, max)` | `rfs(24, 36)` | responsive font clamp |
| `rem(px)` | `rem(24)` | `1.5rem` |
| `em(px)` | `em(24)` | `1.5em` |
| `transition(props...)` | `transition(opacity, transform)` | transition shorthand |
