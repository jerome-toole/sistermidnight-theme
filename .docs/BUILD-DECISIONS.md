# Build Decisions

Appended by build agents when the spec is silent. Format: `- [phase.task] decision — why`

- [plan] Radio nav item links externally to the SMFM site (URL from Header options `radio_url`, default https://radio.sistermidnight.org) — user chose defaults
- [plan] Venue and Support pages built as Page Header + Section blocks from patterns — undesigned
- [plan] News archive is a 2-column News Card grid — undesigned
- [plan] Paper texture skipped unless an asset exists in assets/static/images; CSS hook `body::before` left in place — no exported asset
- [plan] Hero built with poster image; video slot empty until a file is provided
- [plan] Seed script creates a plain GF newsletter form (email + submit); Brevo add-on mapping configured by hand later
- [plan] burnt-sienna/warm-sand used for body text only if contrast ≥ 4.5:1; otherwise limited to ≥ 24px or bold ≥ 19px text
- [0.0] Originals kept at assets/fonts-source/*.orig.woff2 instead of inside assets/static/fonts — Vite publishes all of assets/static, so that is the only way to keep them out of public/; files are small (~15KB) so they stay in git
- [0.1] No `paper-light` colour added; `cool-grey` (#DADAD9) stands in for Figma #E8E5E1 — close enough per spec, one fewer token
- [0.1] `type-body` added as an alias of `type-base` — spec names `type-body`, theme already used `type-base`; both now work
- [0.1] Focus ring is `2px solid var(--color-foreground)` with 3px offset, root `--focus--color` now follows the colour context (warm-sand on dark, dark-umber on light) instead of the accent — matches the spec's warm-sand/dark-umber outline
- [0.1] Paper texture not implemented (no asset exported); hook left commented on `body::after` in `2-base/_base.pcss` because `body::before` is already the backdrop overlay
- [0.1] Layout helper is `content-layout` (two halves, `--container-content: 1237px`, gap 20–40, `__media` / `__text` children, `--reverse` modifier); media half hidden below md unless `__media--always` — spec says illustrations hide below md
- [0.2] Illustration `size` is an optional modifier (`sm` 120–200, `md` 240–400, `lg` 320–562 via spaceFluid); no size fills the container width — spec says "size via CSS", the design frame uses 562px; sizes exposed as `--illustration--size` so callers can override
- [0.2] SVGO run with `convertColors currentColor` and `removeDimensions`; the fixed `#c4b29f` (warm-sand) fill is now `currentColor` and the SVGs scale from `viewBox` — needed for the spec's currentColor rule
- [0.3] Base `.button` stays unstyled (only a reset + focus ring); the design lives on `button--primary` / `button--link` modifiers — the accordion and sub-menu togglers already use `Button::make` as a plain control and must not pick up the fill
- [0.3] `Button::make` gains `variant`, `url`, `target`, `arrow`; with `url` it renders an `<a>` (rel="noopener" for `_blank`) — the design's buttons are mostly links (tickets, map), and a single component keeps the markup identical
- [0.3] One right-arrow inline SVG serves both variants (no separate ↗ for external links) — spec names a single "arrow icon"; primary uses `space-between` so the arrow sits at the right edge like the Figma 200×58 frame
