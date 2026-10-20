# Fonts

Self-hosted, subsetted webfonts. Declared in `assets/styles/2-base/_fonts.pcss`,
preloaded in `Theme/Modules/Core/Preloads.php`. Vite copies this folder to
`public/fonts/` unchanged.

| File | Family | Original |
| --- | --- | --- |
| `sister-midnight-regular.woff2` | SisterMidnight (display) | `assets/fonts-source/sister-midnight-regular.orig.woff2` |
| `foss-modern-bold.woff2` | FOSS Modern Bold (body) | `assets/fonts-source/foss-modern-bold.orig.woff2` |

Originals live outside `assets/static/` so they are not published with the build.

## Sister Midnight fixes

The supplied cut has a few bugs, fixed by `assets/fonts-source/fix-sister-midnight.py`
before subsetting (see the "Font file fixes" frame in Figma):

- **Baseline**: outlines sat ~150 units below the baseline, so underlines cut through
  the letters. Glyphs are lifted 150 units; ascent/descent shift by the same amount so
  the line box is unchanged.
- **Apostrophe**: `'`, `’` and `‚` were mapped to a star glyph. `'`/`’` now use the
  apostrophe tick (the `‘` glyph), `‚` uses the comma, and `"`, `“`, `”` use the
  font's double-tick glyphs.
- **Kerning**: the font had no kerning. A `kern` feature (~125 pairs) is generated from
  the glyph outlines for the classic problem pairs (L, T, A, V, W, Y, F, P, round
  letters, punctuation, apostrophe). Accented letters kern like their base letter.
- **Accented letters**: the cut had none. Accent marks are built from the font's own
  rough shapes (hyphen, period, comma, O, slash) and combined with the capitals,
  covering Latin-1 and most of Latin Extended-A (`À–Ý`, `Ø`, `Ð`, `Ł`, `Č Š Ž Ő` …).
  Lowercase maps to the same glyphs, like the rest of the font. `ß` renders as `SS`.
  `Æ Œ Þ` and ogonek letters (`Ą Ę`) are not drawn and fall back. Accents sit above
  the cap height, so at `line-height: 1` or tighter they can touch the line above.
- **Icons**: the hand-drawn sun, moon and star from Figma (`assets/fonts-source/icons/`)
  are mapped to `☀` U+2600 and `☼` U+263C, `☾` U+263E, and `★` U+2605. They render
  in the text colour.

## Glyph set

Subset with `pyftsubset` (fonttools 4.63 + brotli), keeping all layout features:

```
# Sister Midnight only: apply fixes first
python3 assets/fonts-source/fix-sister-midnight.py \
  assets/fonts-source/sister-midnight-regular.orig.woff2 /tmp/sister-midnight-fixed.ttf

pyftsubset <orig or fixed font> --flavor=woff2 --layout-features='*' \
  --unicodes='U+0020-007E,U+00A0-017F,U+1E9E,U+2013-2014,U+2018-201E,U+2026,U+20AC,U+2039-203A,U+2190,U+2192,U+2197,U+2600,U+2605,U+263C,U+263E' \
  --output-file=<name>.woff2
```

| Range | Contents |
| --- | --- |
| U+0020–007E | Basic Latin (letters, digits, ASCII punctuation, `/`) |
| U+00A0–00FF | Latin-1 Supplement (accented letters, `£`, `©`, `·`) |
| U+0100–017F, U+1E9E | Latin Extended-A (`Č Ł Ő Š Ž` …), capital `ẞ` |
| U+2013–2014 | En / em dash |
| U+2018–201E | Curly quotes |
| U+2026 | Ellipsis |
| U+20AC | `€` |
| U+2039–203A | `‹ ›` |
| U+2190, U+2192, U+2197 | `← → ↗` |
| U+2600, U+2605, U+263C, U+263E | `☀ ★ ☼ ☾` (Sister Midnight icons) |

Code points the source font does not contain are skipped. Coverage after subsetting:

- SisterMidnight: ` "#%&'*+,-./0-9:;=?@A-Z[]a-z £‘’‚“”`, accented letters and
  `☀ ☼ ☾ ★` (148 glyphs). The source cut has no `! ( ) € ‹ › ← → ↗`; those fall back
  to the next family in the stack.
- FOSS Modern Bold: full ASCII, `¢£¤¥¦§©®°¶×÷–—‘’‚“”„…€←→↗` (121 glyphs). No `‹ ›`.

To re-subset after a font update, replace the `.orig.woff2` and re-run the command above.
