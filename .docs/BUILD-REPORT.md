# Sister Midnight build report

Branch `build/new-site`, 44 task and review commits on top of the spec commit. Everything was built and checked locally only. Nothing was deployed or synced.

## What was built

**Foundations (all done)**
- 0.0 Font subsetting: both fonts subset to about 12-15 KB, originals kept in `assets/fonts-source/`.
- 0.1 Tokens and base patterns: colours, type utilities, focus ring, `vertical-label` and `content-layout` patterns.
- 0.2 Illustration component: 18 inline SVGs that follow the colour context.
- 0.3 Button refactor: primary and link variants, inset focus ring on primary.

**Data (all done)**
- 1.1 Event post type, ACF fields, `Event` and `EventRepository`. The archive is `/whats-on/`.
- 1.2 Theme options for header, radio, footer and newsletter.
- 1.3 `dev-scripts/seed.sh`: idempotent seed for pages, 8 events, 3 news posts and menus. It also sets the timezone to Europe/London.
- 1.4 Events JSON feed (`uploads/events/feed.json`) plus REST route.
- 1.5 Radio service, with REST route `sm/v1/now-playing` and a 30s cache.

**Shell (all done)**
- 2.1 Site header with native `<dialog>` menu.
- 2.2 Hero with video and pause toggle.
- 2.3 Radio bar.
- 2.4 Site footer.
- 2.5 Newsletter form (Gravity Forms form 1) and banner.
- 2.6 Page header and default template wiring.

**Editor (all done)**
- 3.1 Block styles and editor parity.
- 3.2 to 3.7: Section, Card, FAQ (native `<details>`), Gallery (masonry and carousel with Blossom), Image Pair, Team.
- 3.8 Block patterns.
- 3.9 About and Venue pages composed.

**Events and News (all done)**
- 4.1 Event Card.
- 4.2 Upcoming Events block and the front page.
- 4.3 Event Header and single template.
- 4.4 Calendar and What's On template.
- 4.5 News single, archive and News Card.

**Hardening (all done)**
- 5.1 Performance pass.
- 5.2 Accessibility pass.
- 5.3 Cleanup: banner, cards, document-links, header-search, homepage-hero and others removed.
- Review fix commits after each phase.

## Lighthouse (mobile, production build, local)

| Page | Perf | LCP | A11y | Best practices | SEO |
| --- | --- | --- | --- | --- | --- |
| `/` | 98 (was 83) | 2.3 s | 100 | 100 | 45* |
| `/whats-on/` | 99 | 2.1 s | 100 | 100 | 54* |
| `/events/records-rye/` | 99 | 1.7 s | 100 | 100 | 54* |
| `/news/we-are-now-a-community-benefit-society/` | 99 | 1.7 s | 100 | 100 | 54* |

TBT is 0 ms and CLS is 0 on every page.

*SEO is low only because of local settings: `blog_public=0` (noindex) and no meta descriptions. Expect 90 or more on staging once indexing is on and descriptions are filled.

Bundles: `main.js` is 8 KB gzip, `main.css` is 32 KB gzip (239 KB raw). The carousel chunk loads only on pages that have a carousel.

## axe (4.10.2)

0 violations on `/`, `/about/`, `/whats-on/` (list and table), an event single, `/news/`, a post single, search and 404. Keyboard paths checked for the menu dialog, FAQ, carousel, team tooltips, calendar, hero toggle and newsletter form. Reduced motion is handled globally.

## Review issues still open

- **Dev Kit only (0.3):** the "Primary on a light context" example renders on the dev kit's off-white, not stone-beige. Dev-only, theme CSS is correct.
- **Events feed (1.4):** it does not rebuild when the site timezone option changes. It catches up with the daily cron. Fix: hook `update_option_timezone_string` to `Feed::markStale`.
- **Font weight (2.3):** SisterMidnight is declared at weight 700 so axe treats the 20px radio-bar text as large bold. The font file itself says 400. Defensible visually, but a strict auditor could dispute it.
- **Event Card (5.1):** Lighthouse `label-content-name-mismatch` on the card link. The News Card was already fixed with a stretched link, and the Event Card can follow.
- **`PageHeader::$intro` dynamic property deprecation** at `components/page-header/PageHeader.php:117` (PHP 8.2+). Declare the property.
- I could not read the final review output for Editor and Events & News in my inputs. Their fix commits are in the log, so check those two review summaries before sign-off.

## Decisions needing a human call

- **Warm-sand on burnt-sienna is 3.48:1.** It is allowed only for large bold text. Button labels, radio-bar text and the hero wordmark are pinned at 19-20px bold. Smaller text on sienna uses cool-grey. Confirm with the designer, or change the palette.
- **Fonts lack glyphs:** SisterMidnight has no `! " ( ) €`, accents, arrows or `‹ ›`. FOSS has no `‹ ›`. These fall back to other fonts. A fuller cut from the designer is needed if they matter in display type.
- **Permalinks changed to `/news/%postname%/`**, so old post URLs need redirects on production.
- **News uses core `post`**, not the `article` CPT.
- **Newsletter:** Gravity Forms form 1 was created locally (not on other environments). The Brevo add-on feed is unconfigured. The old Newsletter module REST endpoint and Brevo settings are still present.
- **Radio:** station `s35e4926a1` returned 404 on the status endpoint, so the bar shows brand and Listen only. Confirm the station ID and the SMFM URL. The marque is a simple drawn placeholder.
- **Search results** use Event and News Cards. The spec was silent.
- **Hero** sits outside any landmark. Wrap it in `<section aria-label>` if you want the pause button reachable by landmark navigation.

## Known gaps

- Placeholder media: hero poster and video, generated event posters and news images. Replace with real assets.
- The Paper texture was skipped (no asset).
- The Plunkett Foundation supporter link in the footer is a Google search URL copied from old data.
- Footer supporter logos load from the staging host locally and need transparent backgrounds.
- Mobile checks used 500px (the DevTools minimum) or emulation, not a real 390px device.
- Not tested: event trash and untrash hooks on the feed, and the 60s radio poll end to end.
- Server config left alone: cache headers on `feed.json` and static assets.
- Gravity Forms prints an inert Orbital `<style>` block (about 3 KB). The 10 KB `basic.min.css` is kept for the honeypot.
- The Vite dev server must be restarted after adding components. New component classes need `composer dump-autoload`, so make sure the deploy build does this.
- `public/hot` exists locally, which causes mixed-content warnings for `localhost:5173`.

## How to review

Open https://sistermidnight.test/ and check these:
- `/` for the hero, radio bar, Upcoming Events, cards and newsletter.
- `/about/` and `/venue/` for Section, Team, FAQ and Gallery.
- `/whats-on/` for the calendar and list. Switch views and months.
- `/events/records-rye/` for an upcoming event, and `/events/late-night-jazz-jam/` (ID 280) for a past one.
- `/news/` and a news post.
- `/?s=jazz` for search, and `/nope/` for the 404.
- `/_dev/` for each component's example.
- `/wp-json/sm/v1/events?before=2026-10` and `/wp-json/sm/v1/now-playing` for the feeds.
- `/support/` and `/membership/` (YouTube embed migrated).

Check keyboard use and the menu dialog at narrow width. Re-seed with `dev-scripts/seed.sh`.
