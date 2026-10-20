"""Fix Sister Midnight font: baseline, quotes, accents, icons, kerning.

Usage: python3 fix-sister-midnight.py <in.woff2|ttf> <out.ttf> [--report]
"""
import math
import os
import sys
import unicodedata
from fontTools.ttLib import TTFont
from fontTools.ttLib.tables._g_l_y_f import Glyph, GlyphComponent
from fontTools.misc.transform import Transform
from fontTools.pens.boundsPen import BoundsPen
from fontTools.pens.cu2quPen import Cu2QuPen
from fontTools.pens.filterPen import FilterPen
from fontTools.svgLib import SVGPath
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

# --- 3. Accented letters ---------------------------------------------------
# The cut has no accent marks, so marks are built from the font's own rough
# shapes (hyphen, period, comma, O, slash) to keep the printed texture, then
# combined with the base letters as composite glyphs.
gs = f.getGlyphSet()
new_glyphs = []

def add_glyph(name, glyph, adv=0):
    """Add a glyph; marks get zero width and are drawn around x=0."""
    glyf.glyphs[name] = glyph
    glyph.recalcBounds(glyf)
    hmtx[name] = (adv, getattr(glyph, "xMin", 0))
    new_glyphs.append(name)
    f.setGlyphOrder(f.getGlyphOrder() + [name])

class WarpPen(FilterPen):
    """Apply a point function to every point."""
    def __init__(self, outPen, fn):
        super().__init__(outPen); self.fn = fn
    def moveTo(self, p): self._outPen.moveTo(self.fn(p))
    def lineTo(self, p): self._outPen.lineTo(self.fn(p))
    def qCurveTo(self, *ps): self._outPen.qCurveTo(*[self.fn(p) if p else p for p in ps])
    def curveTo(self, *ps): self._outPen.curveTo(*[self.fn(p) for p in ps])

def bbox(name):
    g = glyf[name]
    return g.xMin, g.yMin, g.xMax, g.yMax

def piece(src, sx, sy, angle=0, at=(0, 0), warp=None):
    """Draw src scaled about its centre, rotated (degrees), centred at `at`."""
    x0, y0, x1, y1 = bbox(src)
    t = (Transform().translate(*at).rotate(math.radians(angle))
         .scale(sx, sy).translate(-(x0 + x1) / 2, -(y0 + y1) / 2))
    def draw(pen):
        if warp:
            w = (x1 - x0) * sx
            pen = WarpPen(pen, lambda p: warp(p, w))
        gs[src].draw(TransformPen(pen, t))
    return draw

def make_mark(name, pieces, anchor="bottom"):
    """Build a mark centred on x=0, sitting on y=0 (or hanging from y=0)."""
    pen = TTGlyphPen(None)
    for draw in pieces:
        draw(pen)
    g = pen.glyph(); g.recalcBounds(glyf)
    dy = -g.yMin if anchor == "bottom" else -g.yMax
    dx = -(g.xMin + g.xMax) / 2
    pen = TTGlyphPen(None)
    for draw in pieces:
        draw(TransformPen(pen, (1, 0, 0, 1, dx, dy)))
    add_glyph(name, pen.glyph())

def tilde_warp(p, w):
    x, y = p
    return (x, y - 42 * math.sin(2 * math.pi * (x / w)))

STROKE = ("hyphen", 0.42, 0.66)   # short rough bar, ~230×97
make_mark("acutecomb", [piece(*STROKE, angle=25)])
make_mark("gravecomb", [piece(*STROKE, angle=-25)])
make_mark("circumflexcomb", [piece("hyphen", 0.30, 0.64, 35, (-58, 0)),
                             piece("hyphen", 0.30, 0.64, -35, (58, 0))])
make_mark("caroncomb", [piece("hyphen", 0.30, 0.64, -35, (-58, 0)),
                        piece("hyphen", 0.30, 0.64, 35, (58, 0))])
make_mark("tildecomb", [piece("hyphen", 0.5, 0.6, warp=tilde_warp)])
make_mark("macroncomb", [piece("hyphen", 0.46, 0.62)])
make_mark("dieresiscomb", [piece("period", 0.62, 0.55, at=(-92, 0)),
                           piece("period", 0.62, 0.55, at=(92, 0))])
make_mark("dotaccentcomb", [piece("period", 0.66, 0.58)])
make_mark("ringcomb", [piece("O", 0.38, 0.28)])
make_mark("hungarumlautcomb", [piece("hyphen", 0.28, 0.62, 40, (-62, 0)),
                               piece("hyphen", 0.28, 0.62, 40, (62, 0))])
make_mark("cedillacomb", [piece("comma", 0.75, 0.75)], anchor="top")

MARKS = {
    0x0300: "gravecomb", 0x0301: "acutecomb", 0x0302: "circumflexcomb",
    0x0303: "tildecomb", 0x0304: "macroncomb", 0x0307: "dotaccentcomb",
    0x0308: "dieresiscomb", 0x030A: "ringcomb", 0x030B: "hungarumlautcomb",
    0x030C: "caroncomb", 0x0327: "cedillacomb",
}
MARK_GAP = 40      # space between letter top and accent
CEDILLA_GAP = 10

def composite(name, parts, adv=None):
    """parts: [(glyph, dx, dy)]; width comes from the first part by default."""
    g = Glyph(); g.numberOfContours = -1; g.components = []
    for i, (src, dx, dy) in enumerate(parts):
        c = GlyphComponent()
        c.glyphName, c.x, c.y = src, round(dx), round(dy)
        # ROUND_XY_TO_GRID, plus USE_MY_METRICS on the base letter
        c.flags = 0x0204 if i == 0 and adv is None else 0x0004
        g.components.append(c)
    add_glyph(name, g, adv if adv is not None else hmtx[parts[0][0]][0])

variants = {}   # base glyph -> accented glyphs, for kerning
accent_cmap = {}

def map_cp(cp, name):
    accent_cmap[cp] = name
    lower = chr(cp).lower()
    if len(lower) == 1 and ord(lower) != cp:
        accent_cmap[ord(lower)] = name  # lowercase uses the caps, like the rest of the font

cmap = f.getBestCmap()
for cp in list(range(0x00C0, 0x00DF)) + list(range(0x0100, 0x0180)):
    decomp = unicodedata.decomposition(chr(cp)).split()
    if len(decomp) != 2 or decomp[0].startswith("<"):
        continue
    base_ch, mark_cp = chr(int(decomp[0], 16)), int(decomp[1], 16)
    if not ("A" <= base_ch <= "Z") or mark_cp not in MARKS:
        continue
    base, mark = cmap[ord(base_ch)], MARKS[mark_cp]
    x0, y0, x1, y1 = bbox(base)
    cx = (x0 + x1) / 2
    if mark == "cedillacomb":
        parts = [(base, 0, 0), (mark, cx + 20, y0 - CEDILLA_GAP)]
    elif cp == 0x013D:
        # Ľ: caron is written as an apostrophe beside the stem
        q = bbox("quoteleft")
        dx = x0 + 330 - q[0]
        parts = [(base, 0, 0), ("quoteleft", dx, y1 - q[3])]
    else:
        parts = [(base, 0, 0), (mark, cx, y1 + MARK_GAP)]
    name = base + mark.replace("comb", "")
    composite(name, parts)
    variants.setdefault(base, []).append(name)
    map_cp(cp, name)

# Stroked letters and the rest of Latin-1
def stroked(name, base, src, sx, sy, angle, at):
    pen = TTGlyphPen(None)
    gs[base].draw(pen)
    piece(src, sx, sy, angle, at)(pen)
    add_glyph(name, pen.glyph(), hmtx[base][0])
    variants.setdefault(base, []).append(name)

ox0, oy0, ox1, oy1 = bbox("O")
stroked("Oslash", "O", "slash", 0.9, 1.06, 0, ((ox0 + ox1) / 2, (oy0 + oy1) / 2))
dx0, dy0, _, dy1 = bbox("D")
stroked("Eth", "D", "hyphen", 0.5, 0.62, 0, (dx0 + 95, (dy0 + dy1) / 2))
lx0, ly0, _, ly1 = bbox("L")
stroked("Lslash", "L", "hyphen", 0.62, 0.62, 30, (lx0 + 110, (ly0 + ly1) / 2 + 40))
s_adv = hmtx["S"][0]
composite("germandbls", [("S", 0, 0), ("S", s_adv, 0)], adv=2 * s_adv)  # ß → SS

accent_cmap.update({
    0x00D8: "Oslash", 0x00F8: "Oslash",
    0x00D0: "Eth", 0x00F0: "Eth", 0x0110: "Eth", 0x0111: "Eth",
    0x0141: "Lslash", 0x0142: "Lslash",
    0x00DF: "germandbls", 0x1E9E: "germandbls",
    0x0131: cmap[ord("I")],  # dotless i
})

# --- 4. Icons --------------------------------------------------------------
# Hand-drawn sun, moon and star from the Figma "Font file fixes" frame,
# exported to assets/fonts-source/icons/.
ICON_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), "icons")
ICON_SIDEBEARING = 60
CAP_MIDDLE = 467
ICONS = {
    # name: (svg, height in units, codepoints)
    "sun": ("sun.svg", 940, [0x2600, 0x263C]),    # ☀ ☼
    "moon": ("moon.svg", 1030, [0x263E]),         # ☾
    "star": ("star.svg", 1230, [0x2605]),         # ★
}

for name, (svg, height, cps) in ICONS.items():
    path = SVGPath(os.path.join(ICON_DIR, svg))
    bounds = BoundsPen(None); path.draw(bounds)
    x0, y0, x1, y1 = bounds.bounds          # SVG space, y down
    s = height / (y1 - y0)
    t = (Transform().translate(ICON_SIDEBEARING, CAP_MIDDLE)
         .scale(s, -s).translate(-x0, -(y0 + y1) / 2))
    pen = TTGlyphPen(None)
    path.draw(TransformPen(Cu2QuPen(pen, 1.0, reverse_direction=True), t))
    add_glyph(name, pen.glyph(), round((x1 - x0) * s) + 2 * ICON_SIDEBEARING)
    for cp in cps:
        accent_cmap[cp] = name

for table in f["cmap"].tables:
    if table.isUnicode():
        table.cmap.update(accent_cmap)

# --- 5. Kerning ------------------------------------------------------------
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

# Accented letters kern like their base letters
for (l, r), v in list(pairs.items()):
    for vl in [l] + variants.get(l, []):
        for vr in [r] + variants.get(r, []):
            pairs.setdefault((vl, vr), v)

fea = "languagesystem DFLT dflt;\nlanguagesystem latn dflt;\nfeature kern {\n"
fea += "".join(f"  pos {l} {r} {v};\n" for (l, r), v in pairs.items())
fea += "} kern;\n"
addOpenTypeFeaturesFromString(f, fea)

f["head"].fontRevision = 1.005
f.save(OUT)
print("saved", OUT, len(pairs), "kern pairs,", len(new_glyphs), "new glyphs")
