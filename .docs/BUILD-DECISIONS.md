# Build Decisions

Appended by build agents when the spec is silent. Format: `- [phase.task] decision — why`

- [plan] Radio nav item links externally to the SMFM site (URL from Header options `radio_url`, default https://radio.sistermidnight.org) — user chose defaults
- [plan] Venue and Support pages built as Page Header + Section blocks from patterns — undesigned
- [plan] News archive is a 2-column News Card grid — undesigned
- [plan] Paper texture skipped unless an asset exists in assets/static/images; CSS hook `body::before` left in place — no exported asset
- [plan] Hero built with poster image; video slot empty until a file is provided
- [plan] Seed script creates a plain GF newsletter form (email + submit); Brevo add-on mapping configured by hand later
- [plan] burnt-sienna/warm-sand used for body text only if contrast ≥ 4.5:1; otherwise limited to ≥ 24px or bold ≥ 19px text
- [0.0] Originals kept at assets/fonts-source/*.orig.woff2 instead of inside assets/static/fonts — Vite publishes all of assets/static, so that is the only way to keep them out of public/; files are small (~15KB) so they stay in git
