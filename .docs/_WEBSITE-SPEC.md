# Website Specification

## Overview

**Title**: Sister Midnight
**Live URL**: https://www.sistermidnight.org
**Staging URL**: TBD
**PHP Version**: 8.5+

Community-owned music venue & radio station in Catford, Lewisham. Fundraising for a 250-capacity venue opening 2026. Target audience: local music community, potential members/investors, press.

This spec is the single source of truth for the autonomous build. Agents build from this document and the reference material in `.docs/design/`; they do not read Figma.

## Sources

- Figma: https://www.figma.com/design/FKcitHvhW7DVAohXsbEBe7/-EXT--Sister-Midnight-Delivery — page **03. Website** (components on **04. Website Components**)
- `.docs/design/figma-tree.txt` — condensed node tree of every frame (names, sizes, positions, fills, fonts, text)
- `.docs/design/figma-tree.json` — same, structured
- `.docs/design/*.jpg` — reference screenshots: `front-page`, `internal-page-about`, `event-detail`, `whats-on`, `news-single`
- **Reference build**: `/Users/jerometoole/code/clients/peckhamfestival/wp-content/themes/peckham-festival-theme` — the most recent Gust site. When in doubt about an approach, a module pattern, composer/plugin setup or plugin licence keys (`auth.json`, `composer.json` gravity repo, `.env`), copy what that theme does.
- SMFM frontend (radio site) reference: `/Users/jerometoole/code/clients/radio.sistermidnight/smfm-frontend` (its Radio.co code is obsolete; the live API is Radiocult, see Decisions)

## Decisions (fixed, do not re-ask)

| Topic          | Decision                                                                                                                                                                                                                                                                                           |
| -------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Events         | Manual ACF event post type. Calendar and list are **client-rendered** from a cached JSON feed (see Events Calendar). Featured/upcoming cards stay server-rendered.                                                                                                                                 |
| Radio bar | Live now-playing from **Radiocult**: `GET https://api.radiocult.fm/api/station/sister-midnight-fm/schedule/live` (no auth). Response `result.status` is `defaultPlaylist` when off air (show "Off air" or hide the track), otherwise a live show; `result.content.name` is the show/playlist name and `result.metadata.title`/`artist` the current track. Fetched server-side, cached 30s, fail soft. Listen link: https://radio.sistermidnight.org/ (new tab). |
| Forms          | Gravity Forms (existing module) with the **Brevo add-on** for the newsletter list. Newsletter signup renders a GF form by ID from theme options.                                                                                                                                                   |
| Mobile         | Inferred from desktop using the rules in each component. Breakpoints: `md` 768, `lg` 1024.                                                                                                                                                                                                         |
| Old components | Keep and refactor. Rename/replace freely but reuse Gust plumbing.                                                                                                                                                                                                                                  |
| Ambiguity      | Decide the simplest option consistent with this spec, log it in `.docs/BUILD-DECISIONS.md`.                                                                                                                                                                                                        |
| Editor         | Core blocks + block styles + patterns first; custom ACF blocks only for structured data or controlled markup (Section, Card, Upcoming Events, Gallery, Image Pair, FAQ, Team). ACF inline-editable markup wherever possible.                                                                       |
| JS             | No frameworks. Carousels use the **Blossom carousel** library (https://www.blossom-carousel.com/, npm `blossom-carousel`). Everything else is vanilla modules per component.                                                                                                                       |
| Targets        | Lighthouse ≥ 90 as guidance on Front Page, What's On, Event single. WCAG 2.2 AA is a hard requirement.                                                                                                                                                                                             |

---

## Required Plugins

- **ACF Pro** - Custom fields and Gutenberg blocks
- **Yoast SEO** - SEO management
- **Gravity Forms** - Newsletter and contact forms
- **Gravity Forms Brevo add-on** - Pushes newsletter signups to the Brevo list (no theme code)

---

## Design System

Design frame width is 1445px. Content container is 1237px (104px side gutter). The left gutter carries a vertical rotated section label; text-heavy sections place their copy in the right half (x≈619–643, width ≈593) leaving the left half for an illustration or image.

### Colours

Already defined in `theme-config.json`. Figma hex → token:

| Figma     | Token               | Use                                                                   |
| --------- | ------------------- | --------------------------------------------------------------------- |
| `#3B302A` | `dark-umber`        | Default page background, dark sections, footer                        |
| `#CFBDAD` | `warm-sand`         | Text on dark, footer illustrations                                    |
| `#C4B29F` | `stone-beige`       | Cards, paper sections, newsletter banner                              |
| `#9E4429` | `burnt-sienna`      | Hero/radio bar background, buttons, calendar highlight                |
| `#AB8C7F` | `clay-beige`        | Calendar cells with events                                            |
| `#704F50` | `dusty-rose`        | Frame canvas only (not used on site)                                  |
| `#E8E5E1` | (new) `paper-light` | Hover tooltip text / light input bg (use `cool-grey` if close enough) |
| `#DADAD9` | `cool-grey`         | Tooltip text                                                          |

Every section with a background uses `color-context-{token}`. Never set `background-color` alone.

### Typography

Fonts are self-hosted and preloaded already. Display: "SisterMidnight" (`--font-display`). Body: "FOSS Modern" Bold (`--font-sans`). Figma style → theme type utility:

| Figma                                                    | Size           | Utility               |
| -------------------------------------------------------- | -------------- | --------------------- |
| Section label / page title ("Upcoming", "About us")      | Sister 50      | `type-h2`             |
| Event card title (large)                                 | Sister 47–51   | `type-h2`             |
| Card heading ("Become a Member")                         | Sister 42      | `type-title-sm`       |
| Sub-heading ("Our Manifesto", "FAQ")                     | Sister 40      | `type-h3`             |
| Content heading ("Cultural Aims"), news title            | Sister 32      | `type-h4`             |
| Nav items, wordmark (small)                              | Sister 20 / 34 | `type-h5` / `type-h3` |
| Eyebrow / meta ("SMFM Presents", "23rd March", "Listen") | Sister 17      | `type-h6`             |
| Intro paragraph                                          | FOSS 25        | `type-large`          |
| Body                                                     | FOSS 21        | `type-body` (base)    |
| Small / legal                                            | FOSS 13–15     | `type-small`          |

### Spacing

Section vertical padding 78–126px (`spaceFluid(48, 126)`). Card padding 24. Grid gaps 12–40. Use `space()` / `spaceFluid()` helpers.

### Illustrations

SVGs in `assets/static/images/illustrations/` (cat, dancers, disco-ball, flames, flower-roots, guitar-acoustic, guitar-electric, moon, plants-potted, record-face, record-fist, record-flames, record-heart, saxophone, stars, sun-petals, sun-wavy, trumpet). Render inline (so `currentColor` works) via an `Illustration` component with a `name` select. Decorative: `aria-hidden="true"`. Hidden below `md` unless the component says otherwise.

### Paper texture

Figma overlays a "Paper" group on every page. Implement as a single small tiled texture PNG/WebP at low opacity on `body::before` with `pointer-events: none`, or skip if the asset is not exported. Log the decision.

---

## Content Types

### Post Types

- **event** - Gigs, residencies, radio events (exists in `Theme/Modules/Events`)
    - URL: /events/%postname%/
    - Dashicon: dashicons-calendar
    - Supports: title, editor, excerpt, thumbnail (poster, portrait)
    - Taxonomies: location
    - Single: yes
    - Archive: /whats-on/ (rename archive slug from `events`)
    - ACF fields (group `event`):
        - **presenter** (text) - Eyebrow, e.g. "Sister Midnight Presents"
        - **start** (date_time_picker, required) - Start date and time
        - **end_time** (time_picker) - Optional; shown as "17:00 – late" when empty use `end_label`
        - **end_label** (text) - Free text override e.g. "late"
        - **venue_name** (text) - Display venue, falls back to location term name
        - **ticket_type** (select: buy, signup, free) - Controls CTA label
        - **ticket_link** (link) - Button URL; label from ticket_type: "Buy Tickets" / "Sign up" / none
        - **is_past** derived, not stored: `start < now`

- **article** - News (exists in `Theme/Modules/Articles`; core `post` stays disabled to avoid WordPress post oddities)
    - URL: /news/%postname%/
    - Supports: title, editor, excerpt, thumbnail
    - Archive: /news/

### Taxonomies

- **location** - Venue for events (exists)
    - Post types: event
    - Hierarchical: no
    - Has archive: no

### Options (ACF option pages, existing `group_header`, `group_footer` extended)

- Header: **menu** (WP nav menu "Primary"), **hero_video** (file, mp4), **hero_poster** (image), **hero_illustration** (select)
- Radio: **radio_enabled** (true_false), **station_id** (text, default s35e4926a1), **listen_url** (url)
- Footer: **footer_links** (WP nav menu "Footer"), **socials** (existing social-networks group), **address** (textarea), **map_link** (url), **supporters** (repeater: logo image, name, url), **legal_text** (wysiwyg)
- Newsletter: **newsletter_form_id** (number, GF form), **newsletter_heading** (text), **newsletter_text** (textarea), **newsletter_illustration** (select)
- Membership: **membership_link** (link)

---

## Pages and Routes

### Front Page (/)

- Template: Default with hero header (`front-page.php` renders Site Header hero variant + Radio Bar, then `the_content()`, then Site Footer without the Newsletter Banner)
- Content: blocks. Seeded as: Section (intro, illustration left, "Become a member" link) → Upcoming Events block (with Announcement card content) → Section ("Join the party", layout `scatter`) containing a Member Card and a Newsletter Card (Card block).
- No page-specific ACF fields.

### Internal page (default)

- Template: Default
- Content: editor with the blocks below. Page Header partial reads title, **intro_text**, **header_media** (select: illustration | image), **header_illustration**, **header_image**.
- Followed by Newsletter Banner + Site Footer on every page.

### About (/about/), Venue (/venue/), Support (/support/)

- Template: Default. Content built from blocks. Venue and Support are not designed: build with Page Header + Section blocks.

### Radio (/radio/)

- External link to the SMFM site in the menu. No template.

### What's On (/whats-on/)

- Template: Listing
- Route: decorate:post_type:event (archive slug `whats-on`)
- Query params: `?month=YYYY-MM` (calendar month), `?view=calendar|list` (default calendar)
- Renders: Page Header (title only, label "What's On") → Featured Events (3 next upcoming, staggered) → Events Calendar (toggle + month nav + grid, or list)

### Event Single (/events/{slug}/)

- Template: Single
- Renders: Site Header → Event Header → editor content (blocks) → Newsletter Banner → Footer
- Past events render the same with a "This event has passed" note in place of the CTA.

### News archive (/news/)

- Template: Listing
- Route: decorate:post_type:article
- Not designed. Render a 2-column grid of News Cards (image, date, title, excerpt). Log decision.

### News Single (/news/{slug}/)

- Template: Single
- Renders: Site Header → Article Header → content on `stone-beige` section → Newsletter Banner → Footer

### Search, 404

- Existing controllers. Restyle with Page Header.

---

## Components

Legend: **Partial** = template-only, data from context. **Block** = ACF Gutenberg block. Each entry lists fields, behaviour, mobile rule and a11y requirement. Layout utilities (Tailwind) in templates; BEM in `styles.pcss`.

### Editor strategy

- **Core first.** Text and images in a layout are core blocks plus registered block styles. A custom block exists only when it needs structured data (repeater, post query, theme-asset select) or controlled markup. Wrappers are custom; their contents are native (InnerBlocks).
- **Inline editing.** Wherever ACF Pro supports it, blocks render fields with ACF's inline-editable markup (InnerBlocks for content; ACF's editable field attributes for text/wysiwyg where the installed version provides them) so editors change text in place rather than in the sidebar. Sidebar fields only for selects, images and repeaters.
- **Block styles** (registered in `core-blocks`, with editor CSS for parity): paragraph "Large", "Small"; heading "Eyebrow"; buttons "Primary", "Link"; image "Framed"; columns "Text right" (content in the right half); group backgrounds limited to palette colours flagged `block_editor` in `theme-config.json`.
- **Allowed blocks** stay as in `config.json`.
- **Block patterns** (registered in `patterns/`, category "Sister Midnight") so editors start from a working composition:
    - _Illustration and text_: Section (illustration left) with heading + paragraphs + button in the right column
    - _Two-column text_: Section (two-column) with two groups each of heading (h3) + paragraphs
    - _Image and text_: Section (image left) with heading + paragraphs
    - _Headed FAQ_: heading + FAQ block
    - _Join the party_: Section (scatter) with Member Card + Newsletter Card
    - _Event page body_: text Section + Image Pair + FAQ
- **Editor parity** is a deliverable: every block style and custom block has editor CSS so the editor approximates the front end.

### Site Header [Partial]

Wordmark "Sister Midnight" left (display font, `type-h3`), primary nav right (4–5 items, `type-h5`). On the front page it overlays the hero. Refactor existing `site-header`.

**Props:** `variant` (hero|default)
**Mobile:** wordmark + "Menu" button opening a native `<dialog>` full-screen menu (column list, `type-h4`). Close button, Esc closes, focus trapped by dialog semantics.
**A11y:** `<header>` landmark, `<nav aria-label="Primary">`, `aria-current="page"`, skip link remains first focusable.

### Hero [Partial]

Front page only. Full-width 690px media area on `burnt-sienna`: background video (muted, loop, playsinline, poster image, `preload="metadata"`) or poster image when no video; large illustration bottom-right (562px); pause/play button 44×44 bottom-right.
**Fields:** from Header options (hero_video, hero_poster, hero_illustration)
**Mobile:** height 70vh, illustration hidden, button remains.
**A11y:** video has no audio; pause control is a `<button aria-pressed>`; respects `prefers-reduced-motion` (no autoplay, show poster).

### Radio Bar [Partial]

64px band on `burnt-sienna`: SMFM marque + "SISTER MIDNIGHT FM" wordmark (SVG) left; "Now playing: {track}" centre-right; "Listen ↗" link right.
**Data:** `RadioService::nowPlaying()` → server-side fetch of the Radiocult live schedule endpoint (see Decisions), cached in a transient for 30s; when `status` is `defaultPlaylist` show "Off air" (no track) and keep the Listen link. Optional progressive enhancement: a tiny JS module refetches `/wp-json/sm/v1/now-playing` every 60s and swaps text (`aria-live="polite"`).
**Mobile:** two rows, marque + listen on row 1, track on row 2 with overflow ellipsis.
**A11y:** `<aside aria-label="Sister Midnight FM">`, link opens new tab with "(opens in new tab)" visually hidden text.

### Page Header [Partial]

Vertical rotated label in the left gutter (`type-h2`, `writing-mode: vertical-rl; transform: rotate(180deg)`), optional illustration (420px) or image (410×607) in the left half, intro copy (`type-large`) in the right column. Used by every internal page, What's On and archives.
**Props:** `label`, `intro` (html), `media` (illustration name | image array | null)
**Mobile:** label becomes horizontal `<h1>` above; media below the intro at 60% width; image full width.
**A11y:** the label is the page `<h1>`; rotation is CSS only.

### Section [Block] `section`

The generic content section with vertical label and two-column body. Used for Objectives, Our Process, Manifesto/Values, front page intro and Join the party.
**Fields:**

- **label** (text) - Vertical gutter label (optional)
- **background** (select: dark-umber, stone-beige) - colour context
- **media** (select: none, illustration, image)
- **illustration** (select, conditional)
- **image** (image, conditional) - Left column image (440×652 ratio)
- **layout** (select: text-right, two-column, scatter) - text-right: InnerBlocks in right column; two-column: InnerBlocks are two core/group columns; scatter: InnerBlocks are Cards in the staggered "Join the party" arrangement with up to three decorative illustrations (**scatter_illustrations**, select ×3)
- InnerBlocks: core/heading, core/paragraph, core/list, core/buttons, core/columns, core/group, Card, FAQ, Gallery, Image Pair
  **Mobile:** single column, label horizontal, media after text; scatter stacks cards.
  **A11y:** label rendered as `<h2>` when present.

### Event Card [Partial]

Paper card (`stone-beige`, padding 24): poster image (portrait, lazy, `sizes`), title (`type-h2`, 2–3 lines), optional presenter eyebrow (`type-h6`), description (excerpt, `type-body`), meta row: date (`<time>`), time, venue (`type-h6`, space-between).
**Props:** `event` (WP_Post), `size` (sm|lg), `showPresenter`
**Behaviour:** whole card is one link (`<a>` wrapping) with the title as the accessible name; image `alt=""`.
**Mobile:** full width, poster max-height 70vw.

### Card [Block] `card`

The paper card used three ways: announcement (text-only card in the Upcoming grid), member card, newsletter card. One block.
**Fields:**

- **heading** (text)
- **text** (wysiwyg, inline-editable)
- **link** (link) - rendered as a "Read more"-style link
- **illustration** (select) - small illustration top-right (86–108px)
- **show_newsletter_form** (true_false) - renders the Newsletter Form partial under the text
- **size** (select: sm, lg)
  **Mobile:** full width.

### Upcoming Events [Block] `upcoming-events`

Front page grid: 2 columns (596 / 680), cards staggered (right column offset 300px via `margin-top`), optional announcement Card first in right column, large illustration (504px) bottom-left after the last card. Query: next N events by `start` ≥ today. Also used as the Featured Events variant on What's On (`count` 3, staggered 416/406/426), rendered from the template via `UpcomingEvents::make()`.
**Fields:** **label** (text, default "Upcoming"), **count** (number, default 4), **variant** (select: grid, featured), **announcement** (group: heading, text, illustration, link; hidden if heading empty), **illustration** (select)
**Mobile:** single column, no stagger, illustration hidden.
**Empty state:** "No upcoming events" + link to What's On.

### Newsletter Form [Partial]

Inline Gravity Forms render (ajax off) of `newsletter_form_id`: single email input (underline style) + "Sign up →" button (`burnt-sienna`). Used in Newsletter Card and Newsletter Banner. Refactor existing `newsletter-signup` / `gravity-forms` components.
**A11y:** visible or visually-hidden label "Email address", GF validation messages announced, no placeholder-only labels.

### Newsletter Banner [Partial]

Full-width band on `stone-beige` (padding 40): heading "Newsletter" (`type-h3`), text, Newsletter Form; illustration (trumpet, 240px) right. Rendered after content on every page except the front page. Toggle off per page with **hide_newsletter** (true_false).
**Mobile:** stacked, illustration hidden.

### Button [Partial]

Refactor existing. Variants: `primary` (burnt-sienna fill, 200×58, label + arrow icon), `link` (underlined text + arrow). Arrow is inline SVG.

### Illustration [Partial]

Inline SVG by name, `aria-hidden`, `currentColor` fill, size via CSS. Caches file reads.

### Site Footer [Partial]

Dark footer: dancers illustration strip (full width, 313px, inline SVG or `<img>` from `dancers.svg`), then footer top (links column: Contact us, Subscribe, Membership, Support Us; social icons instagram/facebook; address block right with "Open map ↗"), then supporters row ("supported by" + logos, ≤40px high, greyscale) and legal text right.
**Mobile:** illustration strip shorter (scroll-x hidden overflow), columns stack.
**A11y:** `<footer>` landmark, `<nav aria-label="Footer">`, logos have alt = supporter name, `<address>`.

### Event Header [Partial]

Event single top: poster (496×626, bordered 3px `stone-beige`) left; right column: presenter eyebrow (`type-h6`), title (`type-h1`, 3 lines), meta row (date / time / venue, `type-h6`), excerpt (`type-body`), CTA Button (primary) from ticket_type/ticket_link.
**Mobile:** poster first at full width, then text.
**A11y:** `<h1>` title, meta uses `<dl>` or `<time datetime>`; past events show `<p>` note instead of button.

### Article Header [Partial]

News single top (padding 0/104/80): image left (531×351), right: date (`type-h6`, `<time>`), title (`type-h4` as h1), excerpt.
**Mobile:** stacked.

### Events Calendar [Partial]

Header row: view toggle "Calendar // List" (links with `aria-current`), month nav "‹ April 2026 ›" (links to `?month=`). Grid: `<table>` with 7 columns Sun–Sat, header row (`type-h6`), 5–6 week rows (cells 183×~155px). Cell: day number top-left (`type-h6`; "April 1" on the 1st; leading/trailing days of adjacent months in muted text); event titles bottom-left (`type-small`, 2 lines) + promoter. Cell background: `stone-beige` no events, `clay-beige` has events, `burnt-sienna` today. Past days muted ("A Past Event"). Cell links to the event (if one) or lists multiple.
List view: chronological list grouped by month, each row = date, title, venue, link (reuse Event Card `sm` or a simple row).
**Rendering:** client-side from a JSON feed. The server outputs the header row, an empty `<div data-events-calendar data-feed="…">` and a `<noscript>`/initial fallback: a plain server-rendered list of the next 10 events (cheap query, keeps the page useful without JS and gives crawlers something). A ~4KB vanilla module fetches the feed, builds the table/list in the DOM, handles month nav (`history.replaceState` to `?month=`), the view toggle, and "today"/"past" states using the browser date with the site timezone offset from the feed.
**Feed:** `wp-content/uploads/events/feed.json`, regenerated on `save_post_event` / trash / term change and by a daily cron as a safety net. Contents: `{ generated, timezone, events: [{ id, title, url, start, end_time, end_label, venue, presenter, excerpt, poster: {src, srcset, alt} }] }` for all future events plus the past 2 calendar months; older events stay published but aren't in the calendar. Served with long cache headers; the module appends `?v=generated` from a small inline value.
**Mobile:** the list view is forced below `md`.
**A11y:** `<table>` with `<caption>` "Events in April 2026", `<th scope="col">`, each day cell `<td>` with the day in a visually-hidden full date; month nav links have "Previous month / Next month" names.

### Gallery [Block] `gallery`

One block, two block styles the editor can switch between:

- **Masonry** (default): rows of mixed-width images (row 1: 268 / 533 / 243 / 238; row 2: 247 / 247 / 551 / 238), height 360, gap 12. CSS grid with `grid-auto-flow: dense`, span from aspect ratio (landscape = 2 cols).
- **Carousel**: horizontal strip 420px tall, natural widths, gap 16, prev/next arrows top-right. Blossom carousel (https://www.blossom-carousel.com/, npm `blossom-carousel`) initialised by the block's `scripts.js` only when the style is active; arrows wired to its prev/next API.
  **Fields:** **label** (text, vertical gutter label), **images** (gallery)
  **Mobile:** masonry → 2 equal columns; carousel unchanged.
  **A11y:** image alt from media library; carousel region `aria-roledescription="carousel"`, arrow buttons named, keyboard scroll native, `prefers-reduced-motion` → no smooth scrolling.

### Image Pair [Block] `image-pair`

Two images 599×826 side by side on padded section (126/104).
**Fields:** **images** (gallery, max 2), **background** (select)
**Mobile:** stacked.

### Team [Block] `team`

`stone-beige` section, vertical label "The Team", horizontal scroll row of portraits (≈260×340, gap 16) each with a first-name caption placed over the image; hover/focus reveals a tooltip (`burnt-sienna`) with full name and bio.
**Fields:** **label** (text), **members** (repeater: image, first_name, full_name, bio textarea, caption_position select: top-left, top-right, bottom-left, bottom-right)
**Behaviour:** tooltip is a `<button aria-expanded>` toggling a `role="tooltip"`-less description panel (so it works on touch and keyboard). Implement as `<details>`-free CSS `:focus-within` + click toggle in a tiny module.
**Mobile:** same horizontal scroll, snap.

### FAQ [Block] `faq`

Heading "FAQ" (`type-h3`) + items. Each item: question row (`type-h6` uppercase, 8px vertical padding, chevron) and answer (`type-body`, rich text). Refactor existing `accordion`.
**Fields:** **heading** (text), **items** (repeater: question text, answer wysiwyg)
**A11y:** `<details><summary>` native; `name` attribute not used (allow multiple open).

### News Card [Partial]

Image, date, title (`type-h4`), excerpt, whole-card link. For the undesigned news archive.

### Existing components to keep as-is or lightly restyle

`skip-link`, `site-main`, `core-blocks`, `editor`, `admin`, `cookie-consent`, `breadcrumbs` (unused), `pagination`, `no-content`, `heading`, `image`, `link`, `button`, `social-icons`, `menu`, `burger` (replace with dialog pattern), `gravity-forms`.

### Existing components to remove (unused by the new design)

`banner`, `cards`, `document-links`, `homepage-hero` (replaced by Hero), `instagram-feed`, `logo-grid` (folded into Footer supporters), `media-content`, `page-navigation`, `press-links`, `stats-display`, `taxonomy-filters`, `team-grid` (replaced by Team), `values-list`, `video-item`, `header-search`, `brevo-signup`. Remove only after the replacement renders; delete in the final cleanup phase.

---

## Performance Requirements

These are guidance, not budgets. Favour the simplest implementation; measure with Lighthouse and fix what actually regresses.

- No JS framework. First-party JS is fine where it earns its place (menu dialog, hero pause, events calendar, team tooltip, radio bar refresh, Blossom carousel). Keep modules per component so unused ones aren't shipped; load Blossom only when a carousel is on the page.
- Fonts: 2 files, preloaded, `font-display: swap`. **Subset both fonts** (Latin + punctuation + £/€ + the arrows used in the design) with `pyftsubset`/glyphhanger and record the glyph set in `assets/static/fonts/README.md`.
- Images: `wp_get_attachment_image` with `sizes`, `loading="lazy"` except hero/poster/LCP element (`fetchpriority="high"`), WebP via WP.
- Hero video: `preload="metadata"`, poster image is the LCP candidate, ≤ 3MB mp4, not autoplayed under reduced motion or on `prefers-reduced-data`.
- Illustrations inline SVG, optimised (SVGO), no duplication in DOM when repeated: use `<symbol>` sprite for the footer strip if used more than once.
- Radio.co requests server-side only, cached 30s, 2s timeout, never block render (fetch in a `wp_cron`/transient refresh or fail soft).
- CSS: Tailwind purge via Vite; no unused component CSS shipped (per-component bundles).
- Aim for Lighthouse ≥ 90 on Front, What's On, Event, News single at mobile preset; a drop below is a bug to investigate, not a gate.

## Accessibility Requirements

- WCAG 2.2 AA. Colour pairs validated: `warm-sand` on `dark-umber`, `dark-umber` on `stone-beige`, `warm-sand` on `burnt-sienna` (verify ratio ≥ 4.5:1; if `burnt-sienna`/`warm-sand` fails for body text use it only for ≥ 24px or bold ≥ 19px text, log it).
- Visible focus ring on every interactive element (2px `warm-sand`/`dark-umber` outline + offset), never removed.
- Landmarks: header, nav (Primary, Footer), main, aside (radio), footer. One `<h1>` per page.
- Vertical labels: CSS rotation only, reading order unchanged.
- All controls native: `<dialog>`, `<details>`, `<button>`, `<table>`. No `div` buttons.
- Motion: every animation under `@media (prefers-reduced-motion: no-preference)`.
- Forms: visible labels, error messages linked with `aria-describedby`, GF honeypot on.
- Touch targets ≥ 24×24 CSS px (44 for primary controls).
- axe-core clean (0 violations) on every template at `/_dev/` and on the real pages.

---

## Build

See `.docs/BUILD-PLAN.md` for phases, tasks, acceptance checks and the autonomous workflow.
