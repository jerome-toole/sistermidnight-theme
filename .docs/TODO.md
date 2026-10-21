# TODO

Refinement checklist worked by agents from the spec. One line per item; tick when committed. Add items at the bottom.

## Batch 1 (2026-10-21)

- [x] News uses the `article` CPT (Theme/Modules/Articles) instead of core `post`: archive at /news/, singles at /news/{slug}/, News Card, search, seed script and the About/front page links all point at articles; migrate seeded posts; disable the posts post type again
- [x] Radio: service fixed against the SMFM repo setup; radio bar "Listen" and the header "Radio" item link to https://radio.sistermidnight.org/
- [x] Header "Radio" is the last nav item, opens in a new tab with the arrow; one standard external-link style (arrow icon + visually hidden "(opens in new tab)") used everywhere a link opens a new tab
- [x] Favicon set built from the moon illustration in the palette (SVG + PNG + apple-touch + manifest if enabled)
- [x] Footer supporter logos render in warm-sand (the context foreground colour)
- [x] Events calendar matches the What's On design (cells, colours, type, month nav, toggle)

## Done

- [x] Remove the illustration from the homepage hero video
- [x] Fix the icon in the radio bar. It's in the figma file.
- [x] Now playing/off air text in the radio bar should be centered
- [x] Import the arrow from the figma file for the external link icon
- [x] is-style-type-large is used in the first text block on the homepage but doesn't match the design sizing.
- [x] vertical section labels are rotated the wrong way. They shouldn't need a rotate because they have writing-mode: vertical-lr applied to them. The text is upside down.
- [x] sister midnight font headings shouldn't get underlines when they are links. For card links, let's slightly lighten the card background color
- [x] Let's find a way of positioning the illustration at the end of upcoming-events so that it never overlaps the event cards.
- [x] Event cards should be max-width 600px
- [x] The slider nav buttons should be positioned absolute so that the images align with the top of the section heading.
- [x] Import the images from the figma file for the team section. The hover text should follow the cursor. The person's name should be controlled inthe CMS with position and rotation and you should set it up to match the design.
- [x] Replace some event images with the ones used in the design. Make sure the event header matches the design. There's padding missing.
- [x] Improve the news page with intro text and a newsletter signup at the top.

## Audit (2026-10-21)

- [x] Contact page (/contact/): legacy layout — no Page Header intro, huge empty header area, old Brevo `[sibwp_form id=1]` shortcode renders an unstyled full-width input with no label (axe `label` critical + `heading-order`) jammed against the Newsletter Banner → rebuild with Page Header intro + Section blocks (General / Radio emails) and drop the Brevo form (the Newsletter Banner already covers signup)
- [x] Membership page (/membership/): legacy content (bare paragraphs full width at x30, no intro, no Section layout) linked from the footer → rebuild with Page Header intro + Section/FAQ/Gallery blocks like Support
- [ ] [human] Membership page: "Business Plan", "Share Offer Document" and "Society Rules" buttons have no href (dead buttons) → supply the document files/URLs
- [ ] Footer "Subscribe" (/#newsletter): no element has `id="newsletter"` on any page, so it lands at the top of the home page → give the Newsletter Banner and the front-page "Stay in touch" card `id="newsletter"` and link to the in-page anchor
- [ ] [human] Membership links are inconsistent: front-page "Become a member" → /support/, Support "Invest in community shares"/"Read more" → old site https://www.sistermidnight.org/invest (301s to the old /membership), footer → /membership/ → pick one destination and point all of them at it
- [x] /radio/: WP's canonical guess 301s it to the news article "Radio schedule: autumn shows" → redirect /radio/ to https://radio.sistermidnight.org/ (spec: Radio is external, no page)
- [ ] [human] Leftover public pages: `section-test` (shows in search results), `blog`, `events`/`events-2`, `search`/`search-2`, `404-2`/`404-3` all render as empty published pages → keep only the router pages the Router actually resolves, set the rest to draft
- [x] Search (/?s=): no search form on the results page (and no search entry anywhere on the site); an empty `?s=` lists every page including "Section block test" → add a search form to the search page header, treat an empty query as "enter a search term"
- [x] 404: only "The page you are looking for could not be found." with no way on → add links to Home / What's On and a search form
- [x] Header nav: "Events" loses `aria-current`/current style on /whats-on/?view=list, /whats-on/?month=…, and on event singles → mark it current for every event archive URL and event single
- [ ] [human] Cookie consent: component renders nothing (no consent text in options) and still uses the old `has-accent-background-color` styling; it also outputs before the skip link → decide whether it's needed (Brevo "mailin"/WonderPush plugin is active); if yes, add copy, restyle in the palette and move it after the skip link
- [ ] [human] Brevo "mailin" plugin writes `[DEBUG] Updating application cache` lines to debug.log on page loads and only serves the old Contact form/push notifications → decide whether to deactivate it (newsletter runs through Gravity Forms)
- [ ] [human] Images: every About/Venue/Membership section, image-pair and gallery image has `alt=""` → add alt text in the media library (spec: gallery alt comes from the library)
- [ ] Section + Page Header: right-column copy with a vertical label sits at x782 (About intro, Objectives, Our Process, event "About the event") while unlabelled ones sit at x740 (front intro, Manifesto/Values, FAQ); design ≈743 throughout → one right-column x for all (1440)
- [ ] Mobile gutters: vertical-label headings and galleries sit at 16px but body copy, Page Header media and FAQ at 32px → one side gutter for everything (500)
- [ ] Section vertical padding: dark text-right sections use 126px top/bottom; design uses 78–80 (front intro p80 = 483 tall vs 646 built; Objectives/Our Process/FAQ copy at y78) → match the design padding per section (1440)
- [ ] "Become a member" link button (front-page intro, About Manifesto): sits 64px below the copy, 21px with an arrow → design is an underlined FOSS 25 link straight under the paragraph (~16px gap), no arrow
- [ ] Gallery masonry: renders 3 columns (511/250/511) with holes in the last row (About 8 images, Venue 7) → design rows of 4 mixed widths (268/533/243/238, 247/247/551/238), 360 tall, dense with no gaps (1440)
- [ ] Gallery carousel: every slide is cropped to 633×420 → natural widths at 420 tall (278–672 wide in the design), gap 16
- [ ] Event single "About the event": the long vertical label sets the section height, leaving ~600px of empty dark band under two paragraphs → don't let the label drive the section height (clamp/shorten the label or let it overflow into padding)
- [x] What's On featured events: meta wraps (date + time on one row, venue on a second); design is one row "23rd March … Catford House"; titles are 50px and wrap to 5 lines vs design Sister 38–47 at ~3 lines; all cards 405 wide vs 416/406/426 (1440)
- [ ] Footer address: one line "Sister Midnight, Brookdale Road, Catford, SE6 4JY" → design stacks four right-aligned lines with "Open map ↗" beneath (1440)
- [ ] [human] Footer legal line: design reads "Sister Midnight is a Community Benefit Society, FCA #8598 / Imprint | Privacy — Terms ©2025"; built text has no FCA number and no Imprint/Privacy/Terms links (Privacy Policy page is a draft) → confirm the number and pages, then add them
- [ ] Footer supporters: the Co-operatives UK logo wraps onto its own row and renders 160×35, much bigger than the rest → keep all logos on one row at the same visual height (1440)
- [ ] Newsletter "Sign up" button (banner + cards): shows the ↗ external arrow though it submits in place → → arrow per spec/design
- [ ] Newsletter Banner: 320px tall with content inset 40 → design 266 tall, content inset 64 (1440)
- [ ] Venue Page Header: image renders landscape 410×272 at x238 → spec 410×607 portrait crop (object-fit: cover), aligned with the section image below (x223)
- [ ] Venue: stone-beige "Find us" section runs straight into the stone-beige Newsletter Banner with no break → use the dark-umber banner variant when the last section is stone-beige (as on news singles)
- [ ] Event header: poster at x102 and copy at x740 → design 154 / 775 (1440)
- [x] News single: the excerpt shown in the Article Header is repeated as the first paragraph of the body (all three seeded articles) → remove the duplicate paragraph from the seed content
- [ ] [human] Double signup: /news/ and /support/ show a "Stay in touch" card and the Newsletter Banner → hide the banner on those pages (hide_newsletter) or drop the card
- [ ] [human] FAQ: design shows the answers expanded; built FAQs are all collapsed → decide whether the first item opens by default
- [x] What's On list view: still shows the "‹ October 2026 ›" month nav while the list spans several months → hide the month nav in list view (or scope the list to the month)
- [x] Event slugs don't match titles (records-rye = "Otta. No Home…", open-mic-night = "Rael. Max Winter…", soul-revue = "Cityscape Ensemble…", radio-takeover = "Sister Midnight FM live…") → regenerate slugs from titles in the seed
- [ ] [human] Venue "Find us" gives the postcode SE6 4JJ; the footer says SE6 4JY → confirm which is right
- [ ] [human] Newsletter Banner copy "Sign up for news, events and radio schedules from Sister Midnight." differs from the design's "Sign up to the Sister Midnight newsletter to hear about upcoming events and news." → confirm copy
- [ ] [human] SEO: no meta description on any page, and og:image only on events → set a default share image and page descriptions in Yoast
- [ ] [human] Support: "Donate" and "Volunteer" have no links or CTAs, and the record-heart illustration is used twice on the page → supply links and pick a second illustration
- [ ] [human] Placeholder media still live: hero video/poster gradient, generated "Poster 1" event images, "News 1–3" article images; supporter logos and About/Venue photos load from the staging host (Plunkett logo needs a transparent version) → replace or migrate the real assets

## Backlog

- [x] Update the calendar to use the dark mode that's in the figma.
- [x] The names of the team members shouldn't get cut off if they are a bit above the top of the image. Add some padding? (but keep the spacing as per design)
- [x] Add mobile support for the team hover items.
- [x] Do a full run throught of the site and identify any design inconsistencies, missing elements, or other issues that need to be fixed. write them into the todos.
