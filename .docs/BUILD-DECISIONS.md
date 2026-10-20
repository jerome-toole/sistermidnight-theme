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
- [fix-foundations] Primary button focus ring is inset (`--base--focus-offset: -4px`) so it is drawn warm-sand on the burnt-sienna fill (3.5:1) rather than outside the button on the section colour, where it was ~1.1:1 on stone-beige
- [1.1] Event field group saved at theme `acf-json/group_event.json` (not `Theme/Modules/Events/acf-json`) — that is ACF's save path, so admin edits round-trip to the same file; the module's extra load path is left in place
- [1.1] `end()` treats an end time earlier than the start as next-day (e.g. 20:00 – 02:00) — spec gives no rule; events running past midnight are the common case
- [1.1] `ticket_type` defaults to `buy`; unknown/empty values are treated as `buy` so a missing field never hides a ticket link
- [1.1] Location taxonomy switched to non-hierarchical to match the spec's "Hierarchical: no" (no terms exist yet, so nothing is lost)
- [1.2] Radio fields live in a new `group_radio` on the Header options page and Newsletter fields in `group_newsletter` on General; `radio_url` and `membership_link` sit in `group_header` — no new option pages, the radio bar is part of the header and the newsletter banner is site-wide
- [1.2] "Primary"/"Footer" menus are the existing nav menu locations (`header`, `footer-1`, relabelled; unused `footer-2` removed), not ACF fields — keeps menus editable in Appearance > Menus where they are already assigned
- [1.2] Existing `footer_legal_text` serves as `legal_text`, and old footer fields (`footer_text_*`, `footer_images`, `header_call_to_action_1`) are kept until the footer/header rebuild — removing them now would break the current templates
- [1.2] `hero_illustration` / `newsletter_illustration` select choices are loaded from `Illustration::NAMES` via `acf/load_field` — one source of truth for illustration names
- [1.2] `listen_url` left empty by default; consumers fall back to `https://stream.radio.co/{station_id}/listen` — spec gives no default and the stream URL derives from the station
- [1.3] Seed uses core `post` for news (not the `article` CPT) per spec; existing Primary/Footer menus are emptied and rebuilt, existing pages reused by slug; Gravity Forms is not installed so newsletter_form_id is a placeholder 1 and the GF form must be created when the plugin is added.
- [1.4] Feed changes only mark it stale; one rebuild runs at `shutdown` — a single save fires `save_post_event` and `set_object_terms` several times, so this keeps it to one query and one file write per request
- [1.4] `start` and `end_time` are ISO 8601 with offset (`end_time` null when unset, already rolled past midnight); `timezone` is `wp_timezone_string()`; `poster` is the featured image at `medium_large` or null — spec names the keys only; ISO with offset lets the client do today/past maths without its own timezone table
- [1.4] `sm/v1/events?before=YYYY-MM` returns the 3 months before that month (exclusive) in the feed's shape; transient key includes the feed's `generated` so a rebuild invalidates old chunks — same chunk size as the feed's past window, one request per 3 months of back-navigation
- [1.4] Long cache headers for `feed.json` are left to the web server (static file in uploads); the `?v=generated` query from `Feed::url()` busts it — the theme cannot set headers on a static file
