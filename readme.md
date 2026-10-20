# Gust

A WordPress theme framework for custom development. Build portable components and editor blocks with co-located CSS, JS and templating. Auto-loading modules keep features self-contained. A cohesive CSS system of design tokens, layout utilities and contextual color, built on top of Tailwind v4. A routing system links editable block content to dynamic pages (archives, search, 404) and also supports custom static routes.

_Personal, opinionated framework – not a polished product._

- **Components** - Co-located template, logic, styles, and scripts in portable directories
- **Modules** - Self-contained features that auto-load (e.g., ACF integration, post types)
- **Router** - Static routes and editable content for dynamic pages (archives, search, 404) via auto-generated WordPress pages with slots
- **CSS Framework** - BEM-style components, custom utilities and patterns (`content-grid`, `stack-*`, `grid-auto`).
- **Color System** - A contextual color system with automatic foreground calculation and CSS variables.
- **Tailwind v4** - Tailwind used for custom utilities and available globally, but used sparingly in templates.
- **ACF Blocks** - Components become Gutenberg blocks with co-located field definitions
- **Vite Build** - HMR, glob imports, manifest-based cache busting
- **Dev Kit** - `/_dev` browser-based environment for component testing, style guide, and QA
- **WordPress Optimizations** - Asset handling, color system, image helpers, SVG support, menu utilities
- **JS Utilities** - Accessible UI helpers including `Disclosure` (expandable sections, accordions, nav menus) and `Dialog` (modal dialogs), plus viewport, mutation/resize observer, and dynamic element utilities
- **AI Development** - Claude Code skills for AI-assisted development workflows (component scaffolding, testing, website spec generation)

## Key Features

### Component System

Typed PHP component classes with IDE autocomplete, validation, and transformation hooks.

```php
use Gust\Components\Accordion;
use Gust\Components\Card;

// Named arguments with type support
echo Accordion::make(
    heading: 'FAQ',
    accordion_items: $items,
);

// From WP_Post
echo Card::make(object: $post, show_read_more: false);
```

Each component lives in `components/` with co-located files:

```
components/accordion/
├── Accordion.php      # Typed class with ::make() factory
├── template.php       # Template markup
├── styles.pcss        # Component styles (bundled)
├── scripts.js         # Component JS (bundled)
├── block.json        # ACF block registration (optional)
└── group_*.json      # ACF field group (optional)
```

Components support:

- `validate()` - return false to skip rendering
- `transform()` - modify args before render
- `getDefaults()` - default values
- WordPress filters via `gust/component/{name}`

### Router System

Add editable block content to archives, search results, and 404 pages. Router creates WordPress pages linked to these routes—editors customize them in the block editor, and slots render dynamic content like post loops.

Integrates with WordPress' template hierarchy, query handling, and admin. No custom tables or separate admin screens.

```php
use Gust\Router;

// Cross-cutting routes live in Theme/Routes/routes.php
Router::decorateSearch(SearchController::class)
    ->withPage('search')
    ->withSlot('template-content', fn() => SearchController::renderResults());

// Post type modules self-register their own routes in module.php
// Theme/Modules/Events/module.php
Router::decoratePostType('event', static::class)
    ->withPage('events')
    ->withSlot('template-content', [static::class, 'renderArchive']);

// Custom URLs when you need them
Router::route('/tools/demo', fn() => DemoController::index())
    ->template('tools/demo');
```

- **Router Pages** - Auto-created WordPress pages, protected from deletion
- **Slots** - Named injection points for dynamic content (e.g. `template-content`)
- **Controllers** - Class-based or closure handlers
- **Template Resolution** - Follows WordPress hierarchy (`archive-{type}.php`, etc.)

**Route Types:**

- `post_type:{name}` - CPT archive (also matches blog home)
- `taxonomy:{name}` - Taxonomy archive
- `search` - Search results
- `404` - Not found

### Powerful CSS Framework w/ Tailwind v4 base

Tailwind v4 with theme-specific custom utilities for layout, spacing, and animation.

**Layout:**

- `content-grid` - 12-column grid with full/wide/prose alignment
- `grid-simple` / `grid-auto` - Flexible CSS grid utilities
- `flex-grid` - Gap-based flex layouts
- `stack-*` - Vertical rhythm spacing (e.g., `stack-24`)

**Spacing:**

- `--gap`, `--col-gap`, `--row-gap` CSS properties
- `--cols` for grid column control
- Fluid spacing utilities

**Color:**

- `color-context-{name}` - Sets background, foreground, link, and focus colors based on theme config
- `has-{name}-background-color` - WordPress block editor alias for color contexts
- `foreground-from-{name}` - Sets just the foreground/text color

Browse all utilities with live examples at `/_dev/utilities` (development only).

### ACF Blocks

Components become Gutenberg blocks by adding `block.json`:

```json
{
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "theme/accordion",
    "title": "Accordion",
    "description": "An accordion block",
    "category": "theme-blocks",
    "icon": "arrow-down-alt2",
    "acf": {
        "mode": "auto",
        "renderCallback": "Gust\\Components\\Accordion::renderBlock"
    },
    "supports": {
        "anchor": true,
        "align": ["wide", "full"],
        "color": {
            "background": true,
            "text": false,
            "gradients": false
        }
    }
}
```

- `renderBlock` is inherited from `ComponentBase` — no additional PHP needed
- Block field groups (`group_component_*.json`) auto-load from component directories
- ACF fields are passed to the component via `get_fields()` in `renderBlock()`
- Supports anchor, alignment, and color controls via the `supports` key

---

## Getting Started

### Requirements

- PHP >=8.2
- Node 24 (see `.nvmrc`)

### Setup

```bash
cp .env.example .env              # Update with your APP_URL
cp auth.json.example auth.json    # Add your ACF Pro key and site URL

pnpm run setup                    # Install deps + build
pnpm run site-setup               # Configure WP site for dev (optional, see dev-scripts/site-setup.sh)
pnpm run dev                      # Start Vite dev server
```

`auth.json` gives Composer access to the ACF Pro package. Set `username` to your ACF Pro licence key and `password` to the site URL the licence is activated for (e.g. `https://example.test`). It's gitignored.

Access WordPress at your normal URL (.env `APP_URL`). Don't use localhost:5173 directly.

### Commands

| Command                      | Description                     |
| ---------------------------- | ------------------------------- |
| `pnpm run dev`               | Vite dev server with HMR        |
| `pnpm run build`             | Production build                |
| `pnpm run deploy:production` | Build + deploy to production    |
| `pnpm run deploy:staging`    | Build + deploy to staging       |
| `pnpm run scaffold <name>`   | Scaffold new component          |
| `pnpm run pot`               | Generate translation files      |
| `pnpm run lint`              | Check code (Biome)              |
| `pnpm run fix`               | Fix all code (Biome + PHP Pint) |

### Site Setup Script

`dev-scripts/site-setup.sh` configures a fresh WordPress install for development. Requires WP-CLI.

Enables debugging, adds the ACF Pro licence key from `auth.json` to `wp-config.php` (`ACF_PRO_LICENSE`), sets UK locale, disables comments/pings, creates homepage, removes default content/plugins/themes, sets pretty permalinks, and activates all composer-installed plugins.

### Deployment

Deploy via rsync to staging or production. Environment config (SSH host, path) is defined in `wp-sync.yml`.

```bash
pnpm run deploy:staging         # build + deploy to staging
pnpm run deploy:staging:dry     # dry-run (no files transferred)
pnpm run deploy:production      # build + deploy to production
pnpm run deploy:production:dry  # dry-run
```

The production build runs `pnpm install && composer install --no-dev && vite build` before rsync.

---

## Architecture

### Directory Structure

```
Gust/                  # Core framework
  WordPress/           # WP integrations (Admin, Cleanup, Enqueue, Gutenberg, etc.)
  Router/              # Routing system classes
  Component.php        # Component discovery & ACF blocks
  ComponentBase.php    # Base class for typed components
  Config.php           # Framework config loader
  Helpers.php          # Global helper functions
  Module.php           # Module loader
  Vite.php             # Dev server integration

Theme/                 # Custom theme functionality
  Controllers/         # Route controllers (Archive, NotFound, Search)
  Modules/             # Feature modules (auto-loaded)
    ACF/               # ACF Pro integration
    Blog/              # Blog post type & category taxonomy
    ...
  Routes/              # Route definitions (routes.php)
  Utils/               # Utilities (YearShortcode, ObjectMeta)

components/            # UI components (PHP + assets)
.docs/                 # Project documentation
  _WEBSITE-SPEC.md     # Website spec (components, data, pages)
dev-scripts/           # Build & dev tooling
assets/
  main.pcss            # Main stylesheet entry
  main.js              # Main script entry
  styles/              # CSS architecture (ITCSS)
    1-theme/           # Design tokens
    2-base/            # Base elements
    3-patterns/        # Reusable patterns
    4-utilities/       # Utility classes
public/build/          # Compiled assets
```

### Module System

Modules in `Theme/Modules/*/module.php` auto-load via `Gust\Module::init()`. Each has a `Module` class with `init()` method. Disable via `gust/modules/disabled` filter.

### Theme Configuration

`theme-config.json` defines design tokens used across:

- CSS variables (`--color-accent`)
- Tailwind classes (`text-accent`)
- Block editor color palette

### Color System

The theme includes a robust color system with color contexts, foreground calculation, and CSS variables/utilities.
Color configuration is in `theme-config.json`.

**Generated CSS Variables** (per color):

```css
--color-{name}              /* hex value */
--color-{name}--hsl         /* HSL: "210 50% 40%" */
--color-{name}--h/s/l       /* individual components */
--color-{name}--foreground  /* contrasting text color */
```

**Generated Utilities**:

- `color-context-{name}` - Sets background, foreground, focus, and link colors (and other custom properties)
- `has-{name}-background-color` - WordPress block editor alias
- `foreground-from-{name}` - Sets only foreground/text color

**Usage:**

```html
<!-- Full color context (bg + text + links) -->
<section class="color-context-accent">
    <p>White text on accent color, links inherit</p>
</section>

<!-- Just foreground color -->
<span class="foreground-from-accent">Colored text</span>
```

**Config structure** (`theme-config.json`):

```json
{
    "colors": {
        "base": {
            "blue": {
                "color": "#0707a3",
                "name": "Blue",
                "block_editor": true,
                "foreground": "var(--color-white)",
                "properties": {
                    "--link--color": "var(--color-white)"
                }
            },
            "accent": { "namedColor": "blue" }
        }
    }
}
```

### Configuration Files

- `config.json` - Framework settings (comments, images, jQuery, etc.)
- `theme.json` - WordPress FSE/block settings
- `vite.config.js` - Vite build configuration
- `postcss.config.js` - PostCSS plugins
- `biome.json` - Biome linting/formatting
- `pint.json` - Laravel Pint PHP style

### Build System

- **Vite 7** for bundling with HMR
- **Tailwind CSS v4** via PostCSS
- **Biome** for JS/CSS linting
- Glob imports for component scripts
- Manifest-based cache busting

---

## Component Development

### Generate Component

```bash
pnpm run scaffold my-component
```

### Component Class Structure

```php
namespace Gust\Components;

use Gust\ComponentBase;

class MyComponent extends ComponentBase
{
    protected static string $name = 'my-component';

    public static function make(
        string $title = '',
        string $type = 'default',
        array $classes = [],
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    protected static function validate(array $args): bool
    {
        return !empty($args['title']);
    }

    protected static function transform(array $args): array
    {
        if (!empty($args['type'])) {
          $args['classes'][] = 'my-component--' . $args['type'];
        }

        return $args;
    }
}
```

### Template Access

```php
// my-component/template.php
<div class="<?= classes('my-component', $this->classes) ?>">
    <h2><?= esc_html($this->title) ?></h2>
</div>
```

### Templating Helpers

Two global functions are available in all PHP templates.

#### `classes(...$args): string`

Builds an escaped `class` attribute value from variadic strings or arrays. Flattens nested arrays, deduplicates, and escapes with `esc_attr()`.

```php
classes('banner', 'wp-block', 'alignfull')
// → "banner wp-block alignfull"

// Mix of strings and arrays — append $this->classes last so callers can extend
classes('card', 'animate__item', $this->classes)
// → "card animate__item caller-class"

// Conditional — empty strings are filtered out
classes('menu-item', $isActive ? 'is-active' : '', $this->classes)
// → "menu-item is-active"
```

#### `attributes(array $attributes = []): string`

Builds a space-separated string of escaped HTML attributes from a key-value array.

```php
attributes(['disabled' => true])
// → 'disabled'

attributes(['id' => 'my-nav', 'aria-label' => 'Main navigation'])
// → 'id="my-nav" aria-label="Main navigation"'

attributes(['style' => ['color' => 'red', 'font-size' => '1rem']])
// → 'style="color: red; font-size: 1rem;"'
```

---

## Dev Kit (`/_dev`)

Browser-based reference environment for component testing and QA. **Only available when `WP_ENVIRONMENT_TYPE=development`** (set in `.env`).

### Routes

| URL                       | Description                              |
| ------------------------- | ---------------------------------------- |
| `/_dev`                   | Dev Kit index                            |
| `/_dev/components`        | List all components with examples        |
| `/_dev/components/{name}` | View specific component examples         |
| `/_dev/globals`           | Theme colours, design tokens             |
| `/_dev/utilities`         | CSS utility and pattern class reference  |
| `/_dev/content-flow`      | WP block spacing and alignment behaviour |

### Component Examples

Add `example.php` to any component to make it testable:

```php
// components/my-component/example.php
<?php
use Gust\Components\MyComponent;
?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Default</h2>
    <p class="component-example-section__description">Basic usage.</p>
    <div class="component-example-section__preview">
        <?= MyComponent::make(
            title: 'Example Title',
            content: '<p>Example content</p>',
        ); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Minimal</h2>
    <div class="component-example-section__preview">
        <?= MyComponent::make(title: 'Title Only'); ?>
    </div>
</section>
```

Use the standard `component-example-section` classes for consistent styling.

---

## AI Development (Claude Code)

The theme includes Claude Code skills in `.claude/skills/` for AI-assisted development.

### Available Skills

| Skill          | Description                                                    |
| -------------- | -------------------------------------------------------------- |
| `gust-dev`     | Component development workflows (scaffold, test, debug, setup) |
| `website-spec` | Fill in the website specification                              |

Skills can be invoked naturally in conversation (e.g. "scaffold a card component from the spec") or explicitly with `/gust-dev`.

### gust-dev Workflows

**Scaffold** - Create components from spec:

```
scaffold component from spec → reads .docs/_WEBSITE-SPEC.md → generates files
```

**Testing** - Verify changes work:

```bash
# Quick test
: > ../../debug.log && curl -sL $APP_URL -o /dev/null && cat ../../debug.log

# Full test with Chrome DevTools MCP
navigate → snapshot DOM → check console → check debug.log
```

**Components** - Patterns and usage reference for component development.

**Setup** - Create post types, taxonomies, routes from website spec.

---

## Website Specification

`.docs/_WEBSITE-SPEC.md` is the single source of truth for a project's content model, routes, and components. It is used by AI-assisted workflows to scaffold code and keep implementation aligned with the design intent.

### Structure

| Section                 | Description                                         |
| ----------------------- | --------------------------------------------------- |
| **Overview**            | Project title, URLs, PHP version                    |
| **Required Plugins**    | Composer-managed plugins for the project            |
| **Content Types**       | Custom post types with URL, fields, archive routing |
| **Taxonomies**          | Taxonomy definitions with archive routing           |
| **Standalone Routes**   | Pages and routes not tied to a content type         |
| **Site Settings**       | Global ACF options page fields                      |
| **Menus**               | Registered nav menu locations                       |
| **Components**          | Block/partial definitions with ACF field groups     |
| **Integrations**        | Third-party services and API keys                   |
| **Other Functionality** | Cron jobs, CLI commands, custom behaviors           |

### Usage

Fill in the spec before starting development. The `website-spec` skill helps write or expand it:

```
"Add an Events post type to the spec with start date, end date, and venue fields"
"Define a Page Header component as an ACF block with heading, subheading, and CTA"
```

Once the spec is populated, the `gust-dev` scaffold workflow reads it to generate components, post types, taxonomies, and routes.

---

## Acknowledgements

This theme draws a lot from [Granola](https://gitlab.com/wholegrain/granola), a theme framework I worked on at Wholegrain Digital. A lot of that theme, in particular the component system, owes much to [Joshua Stopper's](https://joshstopper.com.au/) foundational work.
