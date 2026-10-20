"""Fix Sister Midnight font: baseline, quote mapping, kerning.

Usage: python3 -I fix_font.py <in.woff2|ttf> <out.ttf> [--report]
"""
import sys
from fontTools.ttLib import TTFont
from fontTools.pens.basePen import BasePen
from fontTools.pens.transformPen import TransformPen
from fontTools.pens.ttGlyphPen import TTGlyphPen
from fontTools.feaLib.builder import addOpenTypeFeaturesFromString

SRC, OUT = sys.argv[1], sys.argv[2]
REPORT = "--report" in sys.argv

BASELINE_SHIFT = 150  # glyphs sit ~150u below the baseline; lift them onto it
TARGET_GAP = 85       # typical gap between straight-sided letters (H|H ≈ 81)
MIN_GAP = 30          # never kern closer than this at the nearest point
STRENGTH = 0.55       # how much of the excess average gap to remove
KERN_THRESHOLD = -20  # skip small corrections so overall spacing is unchanged
BAND = (0, 935)       # vertical band (post-shift) used to compare profiles

f = TTFont(SRC)
f.flavor = None
glyf, hmtx = f["glyf"], f["hmtx"]
gs = f.getGlyphSet()

# --- 1. Baseline -----------------------------------------------------------
for name in f.getGlyphOrder():
    g = glyf[name]
    if g.numberOfContours == 0:
        continue
    pen = TTGlyphPen(gs)
    gs[name].draw(TransformPen(pen, (1, 0, 0, 1, 0, BASELINE_SHIFT)))
    glyf[name] = pen.glyph()
    glyf[name].recalcBounds(glyf)

hhea, os2 = f["hhea"], f["OS/2"]
# Keep the line box identical so existing layouts don't jump
hhea.ascent, hhea.descent = 800 + BASELINE_SHIFT, -200 + BASELINE_SHIFT
os2.sTypoAscender, os2.sTypoDescender = hhea.ascent, hhea.descent
os2.usWinAscent, os2.usWinDescent = hhea.ascent, -hhea.descent
os2.sCapHeight = os2.sxHeight = 935
f["head"].yMin += BASELINE_SHIFT
f["head"].yMax += BASELINE_SHIFT

# --- 2. Quotes -------------------------------------------------------------
# 'quotesingle' is a star; 'quoteleft' holds the real apostrophe tick.
cmap_fix = {
    0x0027: "quoteleft",   # '  straight apostrophe
    0x2019: "quoteleft",   # ’  right quote / apostrophe
    0x201A: "comma",       # ‚  low quote
    0x0022: "second",      # "  straight double quote
    0x201C: "uni2036",     # “
    0x201D: "second",      # ”
}
for table in f["cmap"].tables:
    if table.isUnicode():
        table.cmap.update(cmap_fix)

# --- 3. Kerning ------------------------------------------------------------
class FlatPen(BasePen):
    """Flatten outlines to line segments."""
    def __init__(self, glyphSet):
        super().__init__(glyphSet)
        self.segs, self.cur, self.start = [], None, None
    def _moveTo(self, p):
        self.cur = self.start = p
    def _lineTo(self, p):
        self.segs.append((self.cur, p)); self.cur = p
    def _curveToOne(self, p1, p2, p3):
        p0 = self.cur
        for i in range(1, 9):
            t = i / 8; mt = 1 - t
            pt = tuple(mt**3*a + 3*mt*mt*t*b + 3*mt*t*t*c + t**3*d
                       for a, b, c, d in zip(p0, p1, p2, p3))
            self._lineTo(pt)
    def _qCurveToOne(self, p1, p2):
        p0 = self.cur
        for i in range(1, 7):
            t = i / 6; mt = 1 - t
            pt = tuple(mt*mt*a + 2*mt*t*b + t*t*c for a, b, c in zip(p0, p1, p2))
            self._lineTo(pt)
    def _closePath(self):
        if self.cur != self.start:
            self._lineTo(self.start)

gs = f.getGlyphSet()
ROWS = list(range(BAND[0], BAND[1], 15))

def profile(name):
    """Per row: (leftmost x, rightmost x) or None."""
    pen = FlatPen(gs); gs[name].draw(pen)
    out = []
    for y in ROWS:
        xs = []
        for (x0, y0), (x1, y1) in pen.segs:
            if (y0 <= y < y1) or (y1 <= y < y0):
                xs.append(x0 + (y - y0) * (x1 - x0) / (y1 - y0))
        out.append((min(xs), max(xs)) if xs else None)
    return out

cmap = f.getBestCmap()
letters = [cmap[ord(c)] for c in "ABCDEFGHIJKLMNOPQRSTUVWXYZ"]
punct = [cmap[ord(c)] for c in ".,"] + ["quoteleft"]
profiles = {g: profile(g) for g in set(letters + punct)}

def gap_stats(l, r):
    """Gap between right edge of l and left edge of r, per shared row."""
    adv = hmtx[l][0]
    gaps = []
    for pl, pr in zip(profiles[l], profiles[r]):
        if pl and pr:
            gaps.append(adv - pl[1] + pr[0])
    return gaps

# Only kern the classic problem combinations; the font's own sidebearings
# handle everything else.
ROUND = "CGOQ"
DIAG = "VWY"
QUOTE, PUNCT = "'", ".,"
ALLOWED = {
    "L": "TVWYJ" + ROUND + QUOTE,
    "T": "AJ" + ROUND + "S" + PUNCT,
    "V": "AJ" + ROUND + "S" + PUNCT, "W": "AJ" + ROUND + "S" + PUNCT,
    "Y": "AJ" + ROUND + "S" + PUNCT,
    "A": "TVWY" + ROUND + "U" + QUOTE,
    "F": "AJ" + PUNCT, "P": "AJ" + PUNCT,
    "K": ROUND, "X": ROUND,
    "R": "TVWY",
    "C": "TVWYXAJ", "G": "TVWYXA", "O": "TVWYXAJ" + PUNCT,
    "Q": "TVWYXA", "D": "TVWYXAJ" + PUNCT,
    "U": "A", "S": "TVWY",
    QUOTE: "AJ",
    ".": "TVWY" + QUOTE, ",": "TVWY" + QUOTE,
}
def gname(c):
    return "quoteleft" if c == QUOTE else cmap[ord(c)]

pairs = {}
for lc, rights in ALLOWED.items():
    for rc in rights:
        l, r = gname(lc), gname(rc)
        if l not in profiles:
            profiles[l] = profile(l)
        gaps = gap_stats(l, r)
        if not gaps:
            # No vertical overlap (e.g. L + apostrophe): use bounding boxes
            gaps = [hmtx[l][0] - glyf[l].xMax + glyf[r].xMin + 120]
        # Average gap, capped per row so a single deep notch doesn't dominate
        avg = sum(min(g, 400) for g in gaps) / len(gaps)
        kern = -STRENGTH * (avg - TARGET_GAP)
        kern = max(kern, MIN_GAP - min(gaps))  # don't collide
        kern = min(kern, 0)                    # only tighten
        if rc in PUNCT:
            kern = max(kern, -120)  # arms over punctuation: keep some air
        kern = int(round(kern / 5) * 5)
        if kern <= KERN_THRESHOLD:
            pairs[(l, r)] = kern

if REPORT:
    for (l, r), v in sorted(pairs.items(), key=lambda kv: kv[1]):
        print(f"{l:10} {r:10} {v}")
    print(len(pairs), "pairs")

fea = "languagesystem DFLT dflt;\nlanguagesystem latn dflt;\nfeature kern {\n"
fea += "".join(f"  pos {l} {r} {v};\n" for (l, r), v in pairs.items())
fea += "} kern;\n"
addOpenTypeFeaturesFromString(f, fea)

f["head"].fontRevision = 1.005
f.save(OUT)
print("saved", OUT, len(pairs), "kern pairs")
