# Component Development

Create and modify components in the Gust framework.

**Reference:** [.claude/agent_docs/components.md](../../agent_docs/components.md) for component structure, class patterns, template patterns, ACF field types, and block registration.

## Scaffold New Component

```bash
pnpm run scaffold my-component              # Class + template only
pnpm run scaffold my-component --styles     # Add styles.pcss
pnpm run scaffold my-component --block      # Add block.json
pnpm run scaffold my-component --all        # All optional files
```

Options: `--styles`, `--scripts`, `--block`, `--all`

## Scaffold from Spec Workflow

### 1. Read Spec

Find component definition in `.docs/_WEBSITE-SPEC.md`:

```markdown
### Component Name [Block|Partial]
Description

**Fields:**
- **field_name** (type) - Description
```

### 2. Run Scaffold

```bash
# For blocks (has ACF fields)
pnpm run scaffold component-name --styles --block

# For partials (no ACF, data from context)
pnpm run scaffold component-name --styles
```

### 3. Create ACF Field Group

For blocks, create `components/component-name/group_component_component_name.json`. See [components.md](../../agent_docs/components.md#field-group-structure) for the JSON structure and field templates.

### 4. Implement Class & Template

- Add typed parameters to `::make()` matching spec fields
- Add `validate()` if component should skip rendering when empty
- Add `transform()` for object mapping or nested components
- Implement template with proper escaping per field type

See [components.md](../../agent_docs/components.md#component-class) for class patterns and [template patterns](../../agent_docs/components.md#template-patterns) for output examples.

### 5. Add Styles

```pcss
.component-name {
    display: grid;
    gap: space(8);

    .component-name__heading {
        @apply type-h2;
    }
}
```

See [css.md](../../agent_docs/css.md) for spacing, color, and typography utilities.

### 6. Create Example File

Add `example.php` for dev preview at `/_dev/`. See [components.md](../../agent_docs/components.md#example-file) for the example structure.

## Testing Components

After creating/modifying:

```bash
APP_URL=$(grep '^APP_URL' .env | cut -d= -f2)
: > ../../debug.log
pnpm run build   # if styles/scripts changed
```

1. **Navigate** to a page using the component (or `$APP_URL/_dev/` for the preview suite):
   `mcp__chrome-devtools__navigate_page` with `$APP_URL/_dev/`
2. **Check PHP errors**: `cat ../../debug.log`
3. **Check console**: `mcp__chrome-devtools__list_console_messages` for JS errors
4. **Inspect DOM**: `mcp__chrome-devtools__take_snapshot` to verify structure and classes
5. **Screenshot**: `mcp__chrome-devtools__take_screenshot`
6. **Ask the user**: share the screenshot and ask "Does this look right, or should I adjust anything?"

## Verify Build

```bash
# Clear log
: > ../../debug.log

# Build assets
pnpm run build

# Load page with component
curl -sL $APP_URL -o /dev/null

# Check for errors
cat ../../debug.log
```

For CSS patterns, see [patterns.md](patterns.md).
