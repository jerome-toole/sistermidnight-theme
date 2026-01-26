# Sister Midnight - Product Requirements Document

## Overview

Community-owned music venue & radio station website for Sister Midnight in Catford, Lewisham. Fundraising for 250-capacity venue opening 2026.

**Target audience**: Local music community, potential members/investors, press

---

## Task Breakdown

### Phase 1: Foundation

#### 1.1 Site Header
Build responsive site header with navigation.

Boilerplate at @components/site-header/.

**Requirements:**
- Logo (from site settings)
- Primary navigation menu
- Radio link (external, new tab)
- Mobile menu (burger)
- Optional announcement banner slot

**Pass criteria:**
- [ ] Logo displays and links to home
- [ ] Primary menu renders all items
- [ ] External links open in new tab
- [ ] Mobile menu toggles correctly
- [ ] Sticky behavior matches design

---

#### 1.2 Site Footer
Build site footer with navigation, social, legal text.

Boilerplate at @components/site-footer/.

**Requirements:**
- Footer navigation menu
- Social icons (from site settings)
- Legal text (FCA registration)
- Copyright line
- Dancing figures illustration

**Pass criteria:**
- [ ] All site settings fields render
- [ ] Social icons link correctly
- [ ] Legal text displays
- [ ] Illustration positioned per design

---

#### 1.3 Typography & Base Styles
Configure typography scale and base element styles.

Test at /_dev/globals route.

**Requirements:**
- Font stack setup (check .docs for specs)
- Heading scale (h1-h6)
- Body text styles
- Link styles (including within color contexts)
- List styles

**Pass criteria:**
- [ ] Fonts load correctly
- [ ] Heading hierarchy is clear

---

### Phase 2: Core Components

#### 2.1 Homepage Hero [NEW BLOCK]
Full-width hero with SVG illustration and floating CTA.

**Requirements:**
- SVG illustration display (svg in theme)
- Main heading
- Floating CTA button
- Background color (beige)

**ACF Fields:**
- `heading` (text)
- `cta` (link)

**Pass criteria:**
- [ ] SVG renders correctly
- [ ] Heading displays
- [ ] CTA button floats as designed
- [ ] Block preview works in editor

---

#### 2.2 Page Header [REVIEW]
Already exists - review and style to match design.

**Requirements:**
- Full-width with heading
- Optional subheading
- Optional background image
- Background color options

**Pass criteria:**
- [ ] Matches design for About/Membership pages
- [ ] Image background option works
- [ ] Text is readable on all backgrounds

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
- [ ] Stats display prominently
- [ ] Supports multiple stats
- [ ] Animates on scroll
- [ ] Matches design styling

---

#### 2.4 Media Content / Feature Section [REVIEW]
Already exists as `media-content` - review and potentially rename (at least in block editor).

**Requirements:**
- 2-column layout (media + text)
- Reversible column order
- Support for images and video
- Text includes heading, body, optional CTA

**Pass criteria:**
- [ ] Layout matches homepage sections
- [ ] Column order reversible
- [ ] Works with video embeds

---

#### 2.5 Video Embed [REVIEW]
Already exists as `video-item` - review for use.

**Requirements:**
- YouTube/Vimeo URL support
- Poster image option
- Responsive aspect ratio

**Pass criteria:**
- [ ] Embeds YouTube videos
- [ ] Poster image shows before play
- [ ] Youtube should load after play click
- [ ] Responsive sizing

---

### Phase 3: Content Components

#### 3.1 Press Links [NEW BLOCK]
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
- [ ] Dates format correctly
- [ ] Links open articles
- [ ] Matches design styling

---

#### 3.2 Team Grid [NEW BLOCK]
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
- [ ] Grid responsive (1-2-3 columns)
- [ ] Cards display all content
- [ ] Works without images

---

#### 3.3 Values List [NEW BLOCK]
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
- [ ] Numbers display prominently
- [ ] Multi-column layout on desktop
- [ ] Matches manifesto section design

---

#### 3.4 Document Links [NEW BLOCK]
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
- [ ] Files downloadable
- [ ] File type/size shown
- [ ] Matches design styling

---

#### 3.5 Page Navigation [NEW BLOCK]
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
- [ ] Links scroll to anchors
- [ ] Active state on current section (optional)
- [ ] Works with page sections

---

#### 3.6 FAQ Accordion [REVIEW]
Already exists - review and style.

**Pass criteria:**
- [ ] Expand/collapse works
- [ ] Matches design styling
- [ ] Accessible keyboard navigation

---

### Phase 4: Integration Components

#### 4.1 Newsletter Signup [NEW BLOCK]
Brevo email signup form.

**Requirements:**
- Form heading
- Email input + submit button
- Brevo API integration
- Success/error states

**ACF Fields:**
- `heading` (text)
- `brevo_list_id` (text) - optional

**Pass criteria:**
- [ ] Form submits to Brevo
- [ ] Success message displays
- [ ] Error handling works
- [ ] Matches design styling

---

#### 4.2 Instagram Feed [NEW BLOCK]
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
- [ ] Grid displays images
- [ ] Images link to Instagram
- [ ] Responsive grid layout

---

### Phase 5: Pages

#### 5.1 Homepage
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

#### 5.2 About Page
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

#### 5.3 Membership Page
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

#### 5.4 Venue Page
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

#### 5.5 Contact Page
Contact information and newsletter.

**Sections:**
1. Page Header
2. Contact info blocks
3. Newsletter signup

**Pass criteria:**
- [ ] Contact details display
- [ ] Newsletter form works

---

#### 5.6 404 Page
Not found page.

**Requirements:**
- Friendly message
- Search or navigation back to home

**Pass criteria:**
- [ ] Displays on 404
- [ ] Has navigation back to site

---

#### 5.7 Search Results
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
