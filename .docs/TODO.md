# TODO

Refinement checklist worked by agents from the spec. One line per item; tick when committed. Add items at the bottom.

## Batch 1 (2026-10-21)

- [x] News uses the `article` CPT (Theme/Modules/Articles) instead of core `post`: archive at /news/, singles at /news/{slug}/, News Card, search, seed script and the About/front page links all point at articles; migrate seeded posts; disable the posts post type again
- [x] Radio: service fixed against the SMFM repo setup; radio bar "Listen" and the header "Radio" item link to https://radio.sistermidnight.org/
- [x] Header "Radio" is the last nav item, opens in a new tab with the arrow; one standard external-link style (arrow icon + visually hidden "(opens in new tab)") used everywhere a link opens a new tab
- [x] Favicon set built from the moon illustration in the palette (SVG + PNG + apple-touch + manifest if enabled)
- [x] Footer supporter logos render in warm-sand (the context foreground colour)
- [x] Events calendar matches the What's On design (cells, colours, type, month nav, toggle)

## Backlog

- [x] Remove the illustration from the homepage hero video
- [x] Fix the icon in the radio bar. It's in the figma file.
- [x] Now playing/off air text in the radio bar should be centered
- [x] Import the arrow from the figma file for the external link icon
- [ ] is-style-type-large is used in the first text block on the homepage but doesn't match the design sizing.
- [ ] vertical section labels are rotated the wrong way. They shouldn't need a rotate because they have writing-mode: vertical-lr applied to them. The text is upside down.
- [ ] sister midnight font headings shouldn't get underlines when they are links. For card links, let's slightly lighten the card background color
- [x] Let's find a way of positioning the illustration at the end of upcoming-events so that it never overlaps the event cards.
- [x] Event cards should be max-width 600px
- [ ] The slider nav buttons should be positioned absolute so that the images align with the top of the section heading.
- [ ] Import the images from the figma file for the team section. The hover text should follow the cursor. The person's name should be controlled inthe CMS with position and rotation and you should set it up to match the design.
- [x] Replace some event images with the ones used in the design. Make sure the event header matches the design. There's padding missing.
- [x] Improve the news page with intro text and a newsletter signup at the top.
