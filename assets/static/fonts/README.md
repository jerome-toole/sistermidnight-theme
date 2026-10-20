# Fonts

Self-hosted, subsetted webfonts. Declared in `assets/styles/2-base/_fonts.pcss`,
preloaded in `Theme/Modules/Core/Preloads.php`. Vite copies this folder to
`public/fonts/` unchanged.

| File | Family | Original |
| --- | --- | --- |
| `sister-midnight-regular.woff2` | SisterMidnight (display) | `assets/fonts-source/sister-midnight-regular.orig.woff2` |
| `foss-modern-bold.woff2` | FOSS Modern Bold (body) | `assets/fonts-source/foss-modern-bold.orig.woff2` |

Originals live outside `assets/static/` so they are not published with the build.

## Glyph set

Subset with `pyftsubset` (fonttools 4.63 + brotli), keeping all layout features:

```
pyftsubset <orig>.woff2 --flavor=woff2 --layout-features='*' \
  --unicodes='U+0020-007E,U+00A0-00FF,U+2013-2014,U+2018-201E,U+2026,U+20AC,U+2039-203A,U+2190,U+2192,U+2197' \
  --output-file=<name>.woff2
```

| Range | Contents |
| --- | --- |
| U+0020–007E | Basic Latin (letters, digits, ASCII punctuation, `/`) |
| U+00A0–00FF | Latin-1 Supplement (accented letters, `£`, `©`, `·`) |
| U+2013–2014 | En / em dash |
| U+2018–201E | Curly quotes |
| U+2026 | Ellipsis |
| U+20AC | `€` |
| U+2039–203A | `‹ ›` |
| U+2190, U+2192, U+2197 | `← → ↗` |

Code points the source font does not contain are skipped. Coverage after subsetting:

- SisterMidnight: ` #%&'*+,-./0-9:;=?@A-Z[]a-z £‘’‚` (58 glyphs). The source cut
  has no `! " ( ) € ‹ › ← → ↗` or accented letters; those fall back to the next
  family in the stack.
- FOSS Modern Bold: full ASCII, `¢£¤¥¦§©®°¶×÷–—‘’‚“”„…€←→↗` (121 glyphs). No `‹ ›`.

To re-subset after a font update, replace the `.orig.woff2` and re-run the command above.
