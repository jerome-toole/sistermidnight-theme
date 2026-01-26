# Website Specification

## Overview

**Title**: Sister Midnight
**Live URL**: https://www.sistermidnight.org
**Staging URL**: TBD
**PHP Version**: 8.2+

Community-owned music venue & radio station in Catford, Lewisham. Fundraising for a 250-capacity venue opening 2026. Target audience: local music community, potential members/investors, press.

## Sources:
- Colors: @.docs/figma-tokens.md - set up in @assets/theme-config.json

---

## Required Plugins

- **ACF Pro** - Custom fields and Gutenberg blocks
- **Yoast SEO** - SEO management
- **Brevo** - Newsletter/mailing list

---

## Content Types

### page
Static pages.

- URL: /%pagename%/
- Dashicon: dashicons-admin-page
- Supports: title, editor, thumbnail
- Taxonomies: none
- Has archive: no

---

## Taxonomies

None required.

---

## Standalone Routes

### Homepage (/)
- Template: Default
- Sections:
  - Homepage Hero (SVG illustration, heading, floating CTA)
  - Membership pitch (2-col: text + stats widget)
  - Video embed
  - Press section (2-col: image + links)
  - Instagram feed + newsletter signup (2-col)

### About Us (/about/)
- Template: Article
- Sections: intro, team, manifesto, venue teaser, history

### Membership (/invest/)
- Template: Article
- Sections: stats, CTA, explanation, documents, FAQ

### Venue (/venue/)
- Template: Article
- Sections: gallery, announcement, building info, features

### Contact (/contact/)
- Template: Default
- Sections: contact emails, newsletter form

### Search Results (/search/)
- Template: Default
- Route: decorate:search

### 404
- Template: Default
- Route: decorate:404

---

## Site Settings

- **logo** (image, return: array) - Site logo
- **logo_alt** (image, return: array) - Alternative logo (white version)
- **footer_legal_text** (textarea) - FCA registration text
- **footer_copyright** (text) - Copyright line
- **social_links** (repeater)
  - **platform** (select: facebook, instagram, twitter)
  - **url** (url)
- **radio_url** (url) - Link to SMFM radio site
- **membership_form_url** (url) - Tally form URL for share purchases

---

## Menus

- **primary** - Main navigation in site header
- **footer** - Footer navigation links (if needed)

---

## Components

### Homepage Hero [Block]

Homepage-specific hero with SVG illustration and floating CTA.

**Fields:**
- **heading** (text) - Main heading
- **illustration** (image, return: array) - SVG/illustration asset (set in code)
- **cta** (link) - Floating CTA button

### Page Header [Block] - Already built

Full-width hero section with heading for inner pages.

**Fields:**
- **heading** (text) - Main heading
- **subheading** (textarea) - Supporting text (optional)
- **image** (image, return: array) - Background image (optional)

### Page Navigation [Block]

Manual jump links/TOC for page sections.

**Fields:**
- **links** (repeater)
  - **label** (text) - Link text
  - **anchor** (text) - Target anchor ID (without #)

### Team Grid [Block]

Grid of team member cards.

**Fields:**
- **heading** (text) - Section heading
- **team_members** (repeater)
  - **name** (text) - Member name
  - **role** (text) - Role/title
  - **bio** (textarea) - Biography
  - **image** (image, return: array) - Image (optional)

### Values List [Block]

Numbered values/principles display.

**Fields:**
- **heading** (text) - Section heading
- **intro** (textarea) - Intro text
- **values** (repeater)
  - **title** (text) - Value title
  - **description** (textarea) - Value description

### FAQ Accordion [Block] - Already built

Expandable Q&A list.

**Fields:**
- **heading** (text) - Section heading
- **items** (repeater)
  - **question** (text) - Question
  - **answer** (wysiwyg) - Answer

### Image Gallery [Block] - Already built

Image carousel/gallery.

**Fields:**
- **images** (gallery, return: array) - Gallery images
- **layout** (select: carousel, grid)

### Press Links [Block]

List of press coverage with dates.

**Fields:**
- **heading** (text) - Section heading
- **links** (repeater)
  - **date** (date_picker) - Publication date
  - **publication** (text) - Publication name
  - **url** (url) - Article URL

### Video Embed [Block] - there is a version already built, review it

Embedded video player.

**Fields:**
- **video_url** (url) - Video URL (YouTube/Vimeo)
- **poster_image** (image, return: array) - Poster/thumbnail

### Stats Display [Block]

Fundraising progress or key stats. Manual entry for now, future Airtable sync via cron.

**Fields:**
- **stats** (repeater)
  - **value** (text) - Stat value (e.g., "£365,050")
  - **label** (text) - Stat label (e.g., "Raised")

### Document Links [Block]

List of downloadable documents.

**Fields:**
- **heading** (text) - Section heading
- **documents** (repeater)
  - **title** (text) - Document title
  - **file** (file, return: array) - PDF file

### Contact Info [Block] - already built - info-box

Contact details display.


### Newsletter Signup [Block]

Email signup form via Brevo.

**Fields:**
- **heading** (text) - Form heading
- **brevo_list_id** (text) - Brevo list ID (optional, use default if empty)
-

### Instagram Feed [Block]

Instagram posts grid. Static ACF for now, will become server-side cached dynamic feed.

**Fields:**
- **heading** (text) - Section heading (optional)
- **posts** (repeater)
  - **image** (image, return: array) - Post image
  - **url** (url) - Link to Instagram post
  - **caption** (textarea) - Post caption (optional)

### Feature Section [Block] - already built - media-content rename the block

### Timeline - History section with dates/events (already exists, will be added)

---

## Integrations

- **Sister Midnight FM** - External radio site (radio.sistermidnight.org)
  - Linked from header banner and nav
- **Tally** - External form for share purchases
  - URL stored in site settings
- **Brevo** - Mailing list signup
  - Newsletter signup form integration

---

## Other Functionality

- **PDF hosting** - Share offer documents, business plan, society rules
- **External link handling** - Radio links open in new tab
- **Anchor navigation** - Jump links on About page

---

## Future Considerations

- **Events** - Content type to be added later
- **Updates** - Content type to be added later
- **Airtable integration** - Stats Display block will fetch from Airtable via cron
- **Instagram Feed** - Will become server-side cached dynamic feed
