# Build Plan

Autonomous build of the Sister Midnight site from `.docs/_WEBSITE-SPEC.md`. Read the spec first; this file only says how the work is split and verified.

## Principles

1. **The spec is the contract.** Agents build from the spec and `.docs/design/`; never from Figma. If the spec is silent, choose the simplest option consistent with it and append to `.docs/BUILD-DECISIONS.md` (one line: `- [phase/task] decision — why`).
2. **One task = one fresh agent with a narrow brief.** Each brief contains: the spec section(s) verbatim, the relevant design screenshot path, the Gust docs to read (`.claude/agent_docs/components.md`, `css.md`), and the acceptance checks. Nothing else.
3. **Verify before done.** A task is done only when its acceptance checks pass. Agents run them, they do not assert them.
4. **Review after each phase.** A separate reviewer agent checks the phase output against the spec and the checks, and files fix tasks. Fixes are applied by a fresh agent, then the reviewer re-checks once.
5. **No scope creep.** Don't restyle things outside the task. Don't add libraries. Don't touch deploy scripts.

## Environment

- Reference build for patterns and plugin/licence setup: `/Users/jerometoole/code/clients/peckhamfestival/wp-content/themes/peckham-festival-theme`.
- Local site: `APP_URL` from `.env` (https://sistermidnight.test). Component previews at `$APP_URL/_dev/`.
- Build: `pnpm run build` (Vite). Lint: `pnpm run lint:fix && pnpm run fix:php`.
- PHP errors: `tail -50 ../../debug.log` after loading a page.
- WP CLI: `wp --path=../../../ …`
- Browser checks: Chrome DevTools MCP (snapshot DOM, console, Lighthouse). Screenshot only for visual comparison against `.docs/design/*.jpg`.
- Seed content: phase 1 creates it with WP CLI so later phases have real pages to test.

## Acceptance checks (reusable)

| ID | Check |
|---|---|
| C1 | `pnpm run build` succeeds, `pnpm run lint` clean, `./vendor/bin/pint --test` clean |
| C2 | No new lines in `debug.log` after loading the affected pages |
| C3 | Component renders at `/_dev/{component}` with its `example.php` and at least one real page |
| C4 | DOM snapshot matches the spec's structure (landmarks, heading levels, native elements, link names) |
| C5 | axe-core: 0 violations on the affected page (run via DevTools `evaluate_script` injecting axe from cdnjs) |
| C6 | Visual: screenshot at 1440 and 390 wide compared against the design reference; layout proportions and colour contexts match |
| C7 | Keyboard: every interactive element reachable and operable, focus visible (snapshot `:focus-visible` styles) |
| C8 | Lighthouse mobile: a11y ≥ 95; perf/best-practices/SEO ≥ 90 as guidance, report the numbers |
| C9 | JS bundle sizes reported per module; Blossom only loaded when a carousel is present |

## Phases

### Phase 0 — Foundations
Tasks:
- **0.0 Font subsetting**: subset SisterMidnight and FOSS Modern Bold to the design glyph set, swap files, verify no missing glyphs on seeded pages. Checks: C1, C6.
- **0.1 Tokens & base**: confirm colour tokens, add `paper-light` if needed, type utilities per spec table, focus-ring pattern, `color-context` for `clay-beige`, vertical-label pattern (`.vertical-label`), content-grid helper for the "right column" layout, paper texture decision. Checks: C1, C2.
- **0.2 Illustration component**: inline SVG by name, cached, `aria-hidden`, SVGO pass over `assets/static/images/illustrations/`. Checks: C1, C3.
- **0.3 Button + arrow icon** refactor. Checks: C1, C3, C7.

### Phase 1 — Data
- **1.1 Event fields**: ACF JSON for the `event` group; archive slug `whats-on`; `EventRepository` with `upcoming($limit)`, `forMonth(Y, m)`, `isPast()`; admin columns for start date. Checks: C1, C2, `wp post list --post_type=event` works.
- **1.2 Options**: extend header/footer/newsletter/radio option groups per spec. Checks: C1, ACF JSON synced.
- **1.3 Seed content**: WP CLI script `dev-scripts/seed.sh` creating menus, 8 events (mix of past/upcoming across 2 months, with posters from `assets/static/images/` placeholders), 3 news posts, About/Venue/Support/What's On pages, option values, a GF newsletter form if GF CLI available. Checks: C2, pages exist.
- **1.4 Events feed**: JSON feed generator (on save + daily cron), REST `sm/v1/events?before=`, cache headers. Checks: C1, feed regenerates via `wp eval`, JSON validates.
- **1.5 RadioService**: Radio.co fetch + transient + status check + REST endpoint `sm/v1/now-playing`. Checks: C1, unit-style check via `wp eval`.

### Phase 2 — Global shell
- **2.1 Site Header** (default + hero variants, dialog menu). Checks: C1–C7.
- **2.2 Hero** (video/poster, pause button, reduced motion). Checks: C1–C7.
- **2.3 Radio Bar** (+ optional refresh module). Checks: C1–C7.
- **2.4 Site Footer** (dancers strip, links, socials, address, supporters, legal). Checks: C1–C7.
- **2.5 Newsletter Form + Newsletter Banner**. Checks: C1–C7.
- **2.6 Page Header partial + default page template wiring** (header → content → newsletter → footer). Checks: C1–C7.
- Review 2.

### Phase 3 — Editor & content blocks
- **3.1 Block styles + editor parity base**: register paragraph Large/Small, heading Eyebrow, buttons Primary/Link, image Framed, columns Text-right; palette limited to `block_editor` colours; editor CSS in `core-blocks/_editor-styles.pcss`. Checks: C1, styles visible in the editor.
- **3.2 Section block** (label, background, media, layout incl. scatter, InnerBlocks, inline-editable). Checks: C1–C7, editor preview.
- **3.3 Card block**. Checks: C1–C7.
- **3.4 FAQ block** (`<details>`, refactor `accordion`). Checks: C1–C7.
- **3.5 Gallery block** (Masonry + Carousel styles, Blossom). Checks: C1–C7.
- **3.6 Image Pair block**. Checks: C1–C7.
- **3.7 Team block** (scroll row + tooltip toggle). Checks: C1–C7.
- **3.8 Block patterns**: the six patterns in the spec, registered in `patterns/`. Checks: C1, patterns insert cleanly and render.
- **3.9 Build the About page** from seed content using patterns/blocks to match `internal-page-about.jpg`. Checks: C6 against the reference.
- Review 3.

### Phase 4 — Events & news
- **4.1 Event Card partial**. Checks: C1–C7.
- **4.2 Upcoming Events block (grid + featured variants) + Front Page template + seeded front page blocks**. Checks: C1–C8.
- **4.3 Event Header + Event single template** (past state). Checks: C1–C8.
- **4.4 Events Calendar (client-rendered table + list + month nav from the feed, server-rendered fallback list) + What's On template**. Checks: C1–C8, month boundaries tested for a 6-week month and year rollover in the JS module (small test via `node`), no-JS fallback renders.
- **4.5 Article Header + News single + News Card + News archive**. Checks: C1–C8.
- **4.6 Search + 404 restyle**. Checks: C1–C5.
- Review 4.

### Phase 5 — Hardening & cleanup
- **5.1 Performance pass**: Lighthouse on the four key pages, fix regressions, confirm C8/C9, image `sizes` audit, preload audit.
- **5.2 Accessibility pass**: axe on every template, manual keyboard script, contrast check of the three colour pairs, reduced-motion check.
- **5.3 Cleanup**: remove the components listed in the spec's "remove" list, confirm nothing references them (`grep -r`), rebuild, re-run C1–C2 on all pages.
- **5.4 Report**: `.docs/BUILD-REPORT.md` with what was built, Lighthouse numbers, open decisions from `BUILD-DECISIONS.md`, and anything left out.
- Final review.

## Autonomous run

The run is a single Workflow script (`.docs/build-workflow.js` when written) executing the phases in order:

```
for each phase:
  parallel( tasks with no shared files )   // e.g. 2.1–2.5 can run in parallel; 2.6 after
  reviewer(phase)  → fix tasks → reviewer re-check (max 1 loop)
  if reviewer still fails: mark phase "needs human", continue to next phase that does not depend on it
```

Agent budget guideline: ~30 build agents + 5 reviewers + fix agents. Each build agent gets a narrow brief (see Principles §2) and must return a short structured result: files touched, checks run with pass/fail, decisions logged.

Dependencies: 0 → 1 → 2 → {3, 4 in parallel} → 5. Within phases, tasks touching the same component or template run sequentially.

Human checkpoints: none during the run (chosen). You review `.docs/BUILD-REPORT.md` and `.docs/BUILD-DECISIONS.md` at the end, plus the site itself.

## Token economy

- Figma is read once (done). Agents only read `.docs/design/` text and one screenshot.
- Briefs quote spec sections instead of pointing at the whole spec.
- Reviewers get the diff (`git diff --stat` + changed files) and the checks, not the conversation.
- Screenshots are taken at reduced scale and only for C6.
- Agents report in ≤ 300 words.
