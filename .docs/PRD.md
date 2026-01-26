# Sister Midnight - Product Requirements Document

## Overview

Community-owned music venue & radio station website for Sister Midnight in Catford, Lewisham. Fundraising for 250-capacity venue opening 2026.

**Target audience**: Local music community, potential members/investors, press

## Sources:
- Colors: @.docs/figma-tokens.md - set up in @assets/theme-config.json
- Front Page Design: @.docs/front-page.png
- Standard Page Design: @.docs/page.png

---

## Task Breakdown

### Phase 1: Foundation

#### 1.1 Site Header
Build responsive site header with navigation.

Boilerplate at @components/site-header/.

**Requirements:**
- Logo (from site settings)
- Primary navigation menu ✓ _menu created, assigned to `header` location_
- Radio link (external, new tab) ✓ _added to Primary menu_
- Mobile menu (burger)
- Optional announcement banner slot

**Pass criteria:**
- [x] Logo displays and links to home
- [x] Primary menu renders all items
- [x] External links open in new tab
- [x] Mobile menu toggles correctly
- [x] Sticky behavior matches design

---

#### 1.2 Site Footer
Build site footer with navigation, social, legal text.

Boilerplate at @components/site-footer/.

**Requirements:**
- Footer navigation menu ✓ _menu created, assigned to `footer-1` location_
- Social icons (from site settings)
- Legal text (FCA registration)
- Copyright line
- Dancing figures illustration

**Pass criteria:**
- [x] All site settings fields render
- [x] Social icons link correctly
- [x] Legal text displays
- [x] Illustration positioned per design

---

#### 1.3 Typography & Base Styles
Configure typography scale and base element styles.

Test at /_dev/globals route.

**Requirements:**
- Font stack setup (check .docs for specs)
- Color setup @assets/theme-config.json
- Heading scale (h1-h6)
- Body text styles
- Link styles (including within color contexts)
- List styles

**Pass criteria:**
- [x] Fonts load correctly
- [x] Heading hierarchy is clear
- [x] Colors match the design

---

### Phase 2: Core Components

#### 2.1 Homepage Hero [NEW BLOCK]
Full-width hero with SVG illustration and floating CTA.

**Requirements:**
- SVG illustration display (svg in theme)
- Main heading
- Floating CTA button
- Background color (off-white)

**ACF Fields:**
- `heading` (text)
- `cta` (link)

**Pass criteria:**
- [x] SVG renders correctly
- [x] Heading displays
- [x] CTA button floats as designed
- [x] Block preview works in editor

---

#### 2.2 Page Header [REVIEW]
Already exists - review and style to match design.

**Requirements:**
- Full-width with heading
- Optional subheading
- Optional background image
- Background color options

**Pass criteria:**
- [x] Matches design for About/Membership pages
- [x] Image background option works
- [x] Text is readable on all backgrounds

---

#### 2.3 Stats Display [NEW BLOCK]
Fundraising stats widget.

**Requirements:**
- Repeater of value/label pairs
- Large prominent values
- Compact display option
- Should animate on scroll into view

**ACF Fields:**
- `stats` (repeater)
  - `value` (text)
  - `label` (text)

**Pass criteria:**
- [x] Stats display prominently
- [x] Supports multiple stats
- [x] Animates on scroll
- [x] Matches design styling

---

#### 2.4 Media Content / Feature Section [REVIEW]
Already exists as `media-content` - review and potentially rename (at least in block editor).

**Requirements:**
- 2-column layout (media + text)
- Reversible column order
- Support for images and video
- Text includes heading, body, optional CTA

**Pass criteria:**
- [x] Layout matches homepage sections
- [x] Column order reversible
- [x] Works with video embeds

---

#### 2.5 Video Embed [REVIEW]
Already exists as `video-item` - review for use.

**Requirements:**
- YouTube/Vimeo URL support
- Poster image option
- Responsive aspect ratio

**Pass criteria:**
- [x] Embeds YouTube videos
- [x] Poster image shows before play
- [x] Youtube should load after play click
- [x] Responsive sizing

---

### Phase 3: Content Components

#### 3.1 Press Links ✓
List of press coverage with dates.

**Requirements:**
- List of press articles
- Date, publication name, URL
- Chronological display

**ACF Fields:**
- `heading` (text)
- `links` (repeater)
  - `date` (date_picker)
  - `publication` (text)
  - `url` (url)

**Pass criteria:**
- [x] Dates format correctly
- [x] Links open articles
- [x] Matches design styling

---

#### 3.2 Team Grid ✓
Grid of team member cards.

**Requirements:**
- Grid layout
- Image, name, role, bio per member
- Optional image (show placeholder if empty)

**ACF Fields:**
- `heading` (text)
- `team_members` (repeater)
  - `name` (text)
  - `role` (text)
  - `bio` (textarea)
  - `image` (image)

**Pass criteria:**
- [x] Grid responsive (1-2-3 columns)
- [x] Cards display all content
- [x] Works without images

---

#### 3.3 Values List ✓
Numbered principles/values display.

**Requirements:**
- Section heading + intro
- Numbered list of values
- Title + description per value

**ACF Fields:**
- `heading` (text)
- `intro` (textarea)
- `values` (repeater)
  - `title` (text)
  - `description` (textarea)

**Pass criteria:**
- [x] Numbers display prominently
- [x] Multi-column layout on desktop
- [x] Matches manifesto section design

---

#### 3.4 Document Links ✓
Downloadable document list.

**Requirements:**
- Section heading
- List of PDF downloads
- File size display

**ACF Fields:**
- `heading` (text)
- `documents` (repeater)
  - `title` (text)
  - `file` (file)

**Pass criteria:**
- [x] Files downloadable
- [x] File type/size shown
- [x] Matches design styling

---

#### 3.5 Page Navigation ✓
Jump links / table of contents.

**Requirements:**
- List of anchor links
- Smooth scroll behavior
- Sticky option

**ACF Fields:**
- `links` (repeater)
  - `label` (text)
  - `anchor` (text)

**Pass criteria:**
- [x] Links scroll to anchors
- [ ] Active state on current section (optional)
- [x] Works with page sections

---

#### 3.6 FAQ Accordion ✓
Already exists - reviewed and styled.

**Pass criteria:**
- [x] Expand/collapse works
- [x] Matches design styling
- [x] Accessible keyboard navigation

---

### Phase 4: Integration Components

#### 4.1 Newsletter Signup ✓
Brevo email signup form.

**Requirements:**
- Form heading
- Email input + submit button
- Brevo API integration
- Success/error states

**ACF Fields:**
- `heading` (text)
- `brevo_list_id` (number) - optional

**Pass criteria:**
- [x] Form submits to Brevo
- [x] Success message displays
- [x] Error handling works
- [x] Matches design styling

---

#### 4.2 Instagram Feed ✓
Instagram posts grid (static for now).

**Requirements:**
- Grid of post images
- Link to Instagram post
- Optional caption
- Future: dynamic server-side feed

**ACF Fields:**
- `heading` (text) - optional
- `posts` (repeater)
  - `image` (image)
  - `url` (url)
  - `caption` (textarea) - optional

**Pass criteria:**
- [x] Grid displays images
- [x] Images link to Instagram
- [x] Responsive grid layout

---

### Phase 5: Pages

#### 5.1 Homepage ✓ _page created (ID 23), set as front page_
Assemble homepage using blocks.

**Sections:**
1. Homepage Hero
2. Membership pitch (media-content + stats)
3. Video embed
4. Press section (media-content)
5. Instagram + Newsletter (2-col)

**Pass criteria:**
- [ ] All sections present
- [ ] Layout matches design
- [ ] All blocks functional

---

#### 5.2 About Page ✓ _page created (ID 24)_
Article template with team, manifesto, history.

**Sections:**
1. Page Header
2. Intro text
3. Team Grid
4. Values List (Manifesto)
5. Feature sections

**Pass criteria:**
- [ ] All sections present
- [ ] Page navigation works
- [ ] Content editable via blocks

---

#### 5.3 Membership Page ✓ _page created (ID 25)_
Share offer / investment information.

**Sections:**
1. Page Header
2. Stats Display
3. CTA to Tally form
4. Explanation content
5. Document Links
6. FAQ Accordion

**Pass criteria:**
- [ ] Stats prominent
- [ ] CTA links to Tally form
- [ ] Documents downloadable
- [ ] FAQ functional

---

#### 5.4 Venue Page ✓ _page created (ID 26)_
Venue information and gallery.

**Sections:**
1. Page Header
2. Image Gallery
3. Feature sections

**Pass criteria:**
- [ ] Gallery works
- [ ] Content editable
- [ ] Responsive layout

---

#### 5.5 Contact Page ✓ _page created (ID 27)_
Contact information and newsletter.

**Sections:**
1. Page Header
2. Contact info blocks
3. Newsletter signup

**Pass criteria:**
- [ ] Contact details display
- [ ] Newsletter form works

---

#### 5.6 404 Page ✓ _page exists (ID 10)_
Not found page.

**Requirements:**
- Friendly message
- Search or navigation back to home

**Pass criteria:**
- [ ] Displays on 404
- [ ] Has navigation back to site

---

#### 5.7 Search Results ✓ _page exists (ID 9)_
Search results page.

**Requirements:**
- Search form
- Results listing
- No results state

**Pass criteria:**
- [ ] Results display correctly
- [ ] Pagination works
- [ ] No results message shows

---

### Phase 6: Polish & QA

#### 6.1 Responsive Testing
Test all pages on mobile/tablet/desktop.

**Pass criteria:**
- [ ] Mobile menu works
- [ ] No horizontal scroll
- [ ] Touch targets adequate
- [ ] Images responsive

---

#### 6.2 Accessibility Audit
Basic accessibility review.

**Pass criteria:**
- [ ] Keyboard navigation works
- [ ] Focus states visible
- [ ] Images have alt text
- [ ] Color contrast adequate

---

#### 6.3 SEO Setup
Yoast configuration and meta.

**Pass criteria:**
- [ ] Yoast configured
- [ ] Meta titles/descriptions set
- [ ] Social sharing images

---

## Site Settings Required

Set up in ACF Options:
- [ ] `logo` - Site logo
- [ ] `logo_alt` - White logo version
- [ ] `footer_legal_text` - FCA text
- [ ] `footer_copyright` - Copyright
- [ ] `social_links` - Social media links
- [ ] `radio_url` - SMFM link
- [ ] `membership_form_url` - Tally form URL

---

## Dependencies

**Plugins:**
- ACF Pro
- Yoast SEO
- Brevo (for newsletter)

**External:**
- Sister Midnight FM (radio.sistermidnight.org)
- Tally (share purchase form)
- Brevo API

---

## Future Scope (Not in v1)

- Events content type
- Updates/News content type
- Airtable stats sync
- Dynamic Instagram feed
- Events calendar (see events.png design)
