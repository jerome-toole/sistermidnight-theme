# 5.2 Accessibility pass

axe-core 4.10.2 injected via DevTools on the local production build; keyboard paths driven with real key events in Chrome; contrast computed from the token hex values (WCAG relative luminance).

## C5 — axe (0 violations after fixes)

| Template | URL | Before | After |
| --- | --- | --- | --- |
| Front page | `/` | 0 | 0 |
| About (Section, Team, FAQ, Gallery) | `/about/` | 0 | 0 |
| What's On (list at 500px, table at 1440px) | `/whats-on/` | 0 | 0 |
| Event single | `/events/records-rye/` | 0 | 0 |
| News archive | `/news/` | 0 | 0 |
| Post single | `/news/we-are-now-a-community-benefit-society/` | 0 | 0 |
| Search | `/?s=jazz` | results empty (missing template part); 1 `heading-order` once cards rendered | 0 |
| 404 | `/this-page-does-not-exist/` | 0 | 0 |

`incomplete` (needs-review) items were only `color-contrast` on elements axe could not resolve (text over the hero poster, Team toggles over portraits); both measured by hand below.

Every page: one `<h1>`; landmarks `header`, `nav[Primary]` (inline + inside the closed `<dialog>`, so only one is exposed at a time), `main`, `aside[Sister Midnight FM]` (front page), `footer`, `nav[Footer]`; What's On adds `nav[Calendar view]` and `nav[Month]`.

## C7 — keyboard

| Path | Result |
| --- | --- |
| Menu dialog (390px): Tab → "Menu", Enter | Opens, focus lands on "Close" (autofocus), ring visible (2px warm-sand). Escape closes and returns focus to "Menu". Links 98×44. |
| FAQ `<details>` | Tab to `<summary>` (36px tall), ring visible, Enter/Space toggles, answer shown. |
| Gallery carousel arrows | Two 44×44 `<button>`s before the strip, ring visible, Enter scrolls the track (536px). Under reduced motion Blossom is not loaded and the jump is instant. |
| Team tooltips | Toggle `<button aria-expanded aria-controls>`, panel opens on focus, Escape now actually hides it (was: aria-expanded=false but still visible through `:focus-within`) and keeps focus on the toggle; moving focus to the next member resets. |
| Calendar nav | Previous/Next month 32×44 links with `aria-label`, month name `aria-live="polite"`, Calendar/List switch links with `aria-current`, table has caption and `th` day headers, event links 178×45 with black ring on clay-beige. |
| Hero video toggle | 44×44 `<button aria-pressed>`, ring visible, Enter pauses the video. |
| Newsletter form | Visible label, honeypot on, submitting empty gives `aria-invalid="true"` + `aria-describedby="validation_message_1_1"` ("This field is required.", warm-sand on dark-umber). |

Focus ring: `focus-base` utility (2px solid `--focus--color`, offset per context) on every control; the only `outline: none` rules are on card links whose ring is redrawn on the card via `:has(:focus-visible)`, the GF scroll anchor (`tabindex="-1"`) and custom checkbox/radio inputs which draw a box-shadow ring instead.

## Contrast (spec pairs)

| Pair | Ratio | Verdict |
| --- | --- | --- |
| `warm-sand` #CFBDAD on `dark-umber` #3B302A | **7.02:1** | AA body text |
| `dark-umber` #3B302A on `stone-beige` #C4B29F | **6.22:1** | AA body text |
| `warm-sand` #CFBDAD on `burnt-sienna` #9E4429 | **3.48:1** | Fails body text; passes large text only. Used only at ≥ 19px bold: primary button label `max(19px, …)`, Radio Bar brand/Listen fixed 20px, hero wordmark. Smaller text on burnt-sienna already uses `cool-grey` #DADAD9 (4.53:1): calendar today cell, pagination current page, Team panel. |

Also measured: black on `clay-beige` 6.79:1 (calendar cells with events; dark-umber would be 4.13:1).

## Reduced motion

Emulated `prefers-reduced-motion: reduce`: every `transition-duration`/`animation-duration` resolves to 0.01ms (new global rule in `assets/styles/2-base/_base.pcss`), `scroll-behavior: auto`, Blossom chunk not requested, hero video stays paused (existing script). Previously only the hero, header, FAQ, gallery, team and animate components gated their motion; link/button/card underline and arrow transitions ran regardless.

## Fixed

1. `Theme/Controllers/SearchController.php` — results rendered a non-existent `template-parts/card`, so search showed an empty `<div>`. Now Event Cards (`heading_level: 2`) and News Cards in the news grid, with Pagination and a "No results" message.
2. `components/team/scripts.js` + `styles.pcss` — Escape did not visibly close the panel (`:focus-within` kept it open); `data-dismissed` overrides until focus leaves.
3. `assets/styles/2-base/_base.pcss` — global reduced-motion rule.
4. Target size: `.button--link`, `.radio-bar__listen`, `.events-calendar__view` get `min-height: 24px` (were 20px line boxes). No visual change.

## Left as is

- The Hero (`div.hero`, poster + video toggle) sits between the header and `<main>` outside any landmark, per the front-page decision; axe does not flag it. Could be wrapped in `<section aria-label>` later if landmark-only navigation needs the pause button.
- Footer supporter logos load from the staging host (local *be-media-from-production*); alt text is the supporter name.
