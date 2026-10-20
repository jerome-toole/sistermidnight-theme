# 5.1 Performance pass

Lighthouse 12.8.2, mobile preset (Moto G Power emulation, simulated throttling), run against the production build (`pnpm run build`, Vite dev server disabled) on the local site. Numbers are single runs; treat ±3 as noise.

## C8 — Lighthouse mobile, before → after

| Page | Perf | LCP | FCP | Speed Index | Total bytes | A11y | Best practices | SEO |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| `/` | 83 → **98** | 4.3 s → **2.3 s** | 1.7 s → 1.5 s | 3.8 s → 1.6 s | 1,080 KiB → 599 KiB | 100 | 100 | 45 * |
| `/whats-on/` | 98 → **99** | 2.3 s → 2.1 s | 1.5 s → 1.4 s | 1.5 s → 1.4 s | 401 KiB → 310 KiB | 100 | 100 | 54 * |
| `/events/records-rye/` | 99 → **99** | 2.0 s → 1.7 s | 1.5 s → 1.4 s | 1.5 s → 1.4 s | 331 KiB → 296 KiB | 100 | 100 | 54 * |
| `/news/we-are-now-a-community-benefit-society/` | 99 → **99** | 2.1 s → 1.7 s | 1.5 s → 1.4 s | 1.5 s → 1.4 s | 317 KiB → 294 KiB | 100 | 100 | 54 * |

TBT 0 ms and CLS 0 on every page before and after.

\* SEO is capped by the local site: `blog_public=0` adds `noindex, nofollow` (`is-crawlable`) and no Yoast meta description is set on the seeded pages (`meta-description`). The front page also has one "Read more" link (`link-text`) in the support Card copy — content, not template. None of these are theme bugs; expect ≥ 90 on staging with indexing on and descriptions filled.

## What was fixed

1. **Hero double poster download** — `<video poster>` pointed at the full-size 318 KB JPEG while the `<img class="hero__poster">` already served the responsive LCP candidate. The attribute is gone; the image is the poster. This was the home LCP regression (render delay 2.7 s → 1.7 s, bytes −480 KiB).
2. **WebP** — `image_editor_output_format` maps JPEG → WebP (`Theme/Modules/Core/Images.php`); seeded uploads regenerated. Hero 768w: 72 KB JPEG → 19 KB WebP; card posters ~36 KB → ~7 KB. `modern-image-formats` now passes everywhere.
3. **What's On LCP was lazy-loaded** — `EventCard` takes `eager:`; `UpcomingEvents` takes `eager_first:` and the archive passes it, so the first featured card is `loading="eager" fetchpriority="high"`. `lcp-lazy-loaded` passes.
4. **Intermediate image size** — `gust_small` (600×600) gives card posters a 480w candidate between 300w and 768w (`uses-responsive-images` passes for cards).
5. **Render-blocking plugin assets** — the Brevo plugin's `mailin-front.js` + CSS (and with them jQuery) were in `<head>` on every page. Dequeued unless a `[sibwp_form]` shortcode is present (`Theme/Modules/Newsletter/BrevoAssets.php`); jQuery now loads in the footer as a Gravity Forms dependency. Only `main.css` remains render blocking.

## Still flagged, intentionally left

- `uses-long-cache-ttl`: no cache headers from the local server. Server config.
- `uses-responsive-images` 17 KiB: the footer supporter logos, served from the staging host by *be-media-from-production* (local only). The `medium` size is the smallest WP generates for them.
- `unused-css-rules`: Gravity Forms `basic.min.css` (10 KB) — keeps honeypot / `.gform_hidden` hidden.
- `label-content-name-mismatch` on Event Card links (axe best-practice, a11y still 100): the card `<a aria-labelledby>` names the link by the title while the visible text includes presenter and meta. News Card was switched to a stretched link for this in 4.5; Event Card can follow in the cleanup phase.
- CSS from components slated for removal (`banner`, `cards`, `logo-grid`, …) is still bundled (main.css 239 KB raw / 32 KB gzip). Removing them is the cleanup task.

## C9 — JS bundles (production build)

| Chunk | Raw | Gzip | Loaded |
| --- | --- | --- | --- |
| `main.js` (all component modules, Disclosure helper) | 25.3 KB | 8.0 KB | every page, `type="module" defer` |
| `blossom-carousel-core.js` (dynamic import) | 12.8 KB | 4.5 KB | only when a `[data-gallery-track]` carousel is on the page — verified: absent on `/`, `/whats-on/`, event, post; fetched on `/_dev/components/gallery/` |
| `popover.js` polyfill (dynamic import) | 11.1 KB | 3.8 KB | only in browsers without native `popover` |
| `main.css` | 239 KB | 32 KB | every page |
| Third party: jQuery 30.6 KB + Gravity Forms theme scripts (~78 KB) + wp-dom-ready/hooks/i18n/a11y (~6 KB) | | | every page (newsletter form in the footer), all in the footer, deferred |

Component modules inside `main.js`: animate, cookie-consent, events-calendar, gallery, hero, page-navigation, radio-bar, site-header, team, video-item (page-navigation and video-item belong to components slated for removal).

Fonts: 2 files, preloaded, subset — `sister-midnight-regular.woff2` 19.9 KB, `foss-modern-bold.woff2` 12.3 KB. Inline SVG: illustrations already SVGO-optimised (svgo --multipass saves < 0.1 %), none repeated on a page so no sprite needed; the footer strip is 20 KB inline on every page (compresses well with the HTML).
