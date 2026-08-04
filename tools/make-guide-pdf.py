#!/usr/bin/env python3
"""
Builds the Mela stall bookings user guide as a PDF.

Written against the raw PDF format on purpose: this box has no wkhtmltopdf,
weasyprint, LibreOffice, pip or sudo, and adding a PDF library to the project's
composer dependencies for one document would be worse. Uses the base-14
Helvetica faces, which every PDF reader has built in, so nothing is embedded.

Run:  python3 tools/make-guide-pdf.py
Out:  docs/LCNL-Mela-Stall-Bookings-Guide.pdf
"""

import os
import zlib

# ---------------------------------------------------------------- font metrics
# Standard Adobe AFM advance widths (per 1000 em) for ASCII 32..126.
_HELV = [
    278, 278, 355, 556, 556, 889, 667, 191, 333, 333, 389, 584, 278, 333, 278, 278,
    556, 556, 556, 556, 556, 556, 556, 556, 556, 556, 278, 278, 584, 584, 584, 556,
    1015, 667, 667, 722, 722, 667, 611, 778, 722, 278, 500, 667, 556, 833, 722, 778,
    667, 778, 722, 667, 611, 722, 667, 944, 667, 667, 611, 278, 278, 278, 469, 556,
    333, 556, 556, 500, 556, 556, 278, 556, 556, 222, 222, 500, 222, 833, 556, 556,
    556, 556, 333, 500, 278, 556, 500, 722, 500, 500, 500, 334, 260, 334, 584,
]
_HELV_BOLD = [
    278, 333, 474, 556, 556, 889, 722, 238, 333, 333, 389, 584, 278, 333, 278, 278,
    556, 556, 556, 556, 556, 556, 556, 556, 556, 556, 333, 333, 584, 584, 584, 611,
    975, 722, 722, 722, 722, 667, 611, 778, 722, 278, 556, 722, 611, 833, 722, 778,
    667, 778, 722, 667, 611, 722, 667, 944, 667, 667, 611, 333, 278, 333, 584, 556,
    333, 556, 611, 556, 611, 556, 333, 611, 611, 278, 278, 556, 278, 889, 611, 611,
    611, 611, 389, 556, 333, 611, 556, 778, 556, 556, 500, 389, 280, 389, 584,
]


def char_width(ch: str, bold: bool) -> int:
    table = _HELV_BOLD if bold else _HELV
    o = ord(ch)
    if 32 <= o <= 126:
        return table[o - 32]
    # Non-ASCII we actually use, keyed by Unicode codepoint. cp1252 maps each
    # of these to a single byte that WinAnsiEncoding renders correctly.
    return {
        0x00A3: 556,   # £
        0x2013: 556,   # en dash
        0x2014: 1000,  # em dash
        0x2018: 191, 0x2019: 191,   # curly single quotes
        0x201C: 333, 0x201D: 333,   # curly double quotes
        0x2022: 350,   # bullet
    }.get(o, 556)


def text_width(s: str, size: float, bold: bool) -> float:
    return sum(char_width(c, bold) for c in s) * size / 1000.0


def wrap(s: str, size: float, bold: bool, max_w: float):
    """Greedy word wrap to max_w points."""
    out, line = [], ""
    for word in s.split():
        trial = word if line == "" else line + " " + word
        if text_width(trial, size, bold) <= max_w:
            line = trial
        else:
            if line:
                out.append(line)
            line = word
    if line:
        out.append(line)
    return out or [""]


# ---------------------------------------------------------------- pdf plumbing
def esc(s: str) -> bytes:
    b = s.encode("cp1252", "replace")
    return b.replace(b"\\", b"\\\\").replace(b"(", b"\\(").replace(b")", b"\\)")


A4_W, A4_H = 595.276, 841.890
MARGIN = 54.0
CONTENT_W = A4_W - 2 * MARGIN

MAROON = (0.478, 0.114, 0.235)
GOLD = (0.831, 0.686, 0.216)
INK = (0.10, 0.09, 0.11)
MUTED = (0.42, 0.40, 0.44)
RULE = (0.85, 0.82, 0.84)
BOXBG = (0.976, 0.965, 0.970)
WARNBG = (0.988, 0.945, 0.902)
WARNED = (0.804, 0.404, 0.0)


class Doc:
    def __init__(self):
        self.pages = []
        self.ops = []
        self.y = 0.0
        self._new_page()

    # -- page handling ------------------------------------------------------
    def _new_page(self):
        if self.ops:
            self.pages.append(self.ops)
        self.ops = []
        self.y = A4_H - MARGIN
        self._page_furniture()

    def _page_furniture(self):
        # thin gold rule along the top
        r, g, b = GOLD
        self.ops.append(f"{r:.3f} {g:.3f} {b:.3f} rg "
                        f"{MARGIN:.1f} {A4_H - MARGIN + 16:.1f} {CONTENT_W:.1f} 3 re f")

    def space(self, h: float):
        self.y -= h

    def need(self, h: float):
        if self.y - h < MARGIN + 28:
            self._new_page()

    # -- primitives ---------------------------------------------------------
    def _text(self, s, x, y, size, bold=False, color=INK, italic=False):
        font = "/F2" if bold else ("/F3" if italic else "/F1")
        r, g, b = color
        self.ops.append(
            f"BT {font} {size} Tf {r:.3f} {g:.3f} {b:.3f} rg 1 0 0 1 {x:.2f} {y:.2f} Tm "
            f"({esc(s).decode('latin-1')}) Tj ET"
        )

    def _rect(self, x, y, w, h, fill=None, stroke=None, lw=0.8):
        if fill:
            r, g, b = fill
            self.ops.append(f"{r:.3f} {g:.3f} {b:.3f} rg {x:.1f} {y:.1f} {w:.1f} {h:.1f} re f")
        if stroke:
            r, g, b = stroke
            self.ops.append(f"{r:.3f} {g:.3f} {b:.3f} RG {lw} w "
                            f"{x:.1f} {y:.1f} {w:.1f} {h:.1f} re S")

    # -- block elements -----------------------------------------------------
    def title(self, s, sub=""):
        self.need(72)
        for ln in wrap(s, 21, True, CONTENT_W):
            self._text(ln, MARGIN, self.y - 21, 21, bold=True, color=MAROON)
            self.y -= 26
        if sub:
            for ln in wrap(sub, 11, False, CONTENT_W):
                self._text(ln, MARGIN, self.y - 11, 11, color=MUTED)
                self.y -= 15
        self.y -= 10

    def h2(self, s):
        self.need(46)
        self.y -= 12
        for ln in wrap(s, 13.5, True, CONTENT_W):
            self._text(ln, MARGIN, self.y - 13.5, 13.5, bold=True, color=MAROON)
            self.y -= 18
        r, g, b = RULE
        self.ops.append(f"{r:.3f} {g:.3f} {b:.3f} RG 0.7 w "
                        f"{MARGIN} {self.y + 4:.1f} m {MARGIN + CONTENT_W} {self.y + 4:.1f} l S")
        self.y -= 8

    def para(self, s, size=10.5, color=INK, indent=0.0):
        for ln in wrap(s, size, False, CONTENT_W - indent):
            self.need(size + 5)
            self._text(ln, MARGIN + indent, self.y - size, size, color=color)
            self.y -= size + 4.2
        self.y -= 3

    def step(self, n, s):
        """Numbered step with a maroon disc."""
        size = 10.5
        lines = wrap(s, size, False, CONTENT_W - 26)
        self.need(len(lines) * (size + 4.2) + 6)
        top = self.y
        r, g, b = MAROON
        self.ops.append(f"{r:.3f} {g:.3f} {b:.3f} rg "
                        f"{MARGIN + 7.5:.1f} {top - 8:.1f} m "
                        f"{MARGIN + 7.5:.1f} {top - 8:.1f} 7.5 0 360 arc f")
        # arc is not a PDF operator; draw the disc as a small filled circle
        self.ops.pop()
        self._circle(MARGIN + 7.5, top - 7.5, 7.5, MAROON)
        self._text(str(n), MARGIN + 7.5 - text_width(str(n), 8, True) / 2,
                   top - 10.5, 8, bold=True, color=(1, 1, 1))
        for i, ln in enumerate(lines):
            self._text(ln, MARGIN + 26, self.y - size, size)
            self.y -= size + 4.2
        self.y -= 4

    def _circle(self, cx, cy, r, color):
        k = 0.5523 * r
        cr, cg, cb = color
        self.ops.append(
            f"{cr:.3f} {cg:.3f} {cb:.3f} rg "
            f"{cx - r:.2f} {cy:.2f} m "
            f"{cx - r:.2f} {cy + k:.2f} {cx - k:.2f} {cy + r:.2f} {cx:.2f} {cy + r:.2f} c "
            f"{cx + k:.2f} {cy + r:.2f} {cx + r:.2f} {cy + k:.2f} {cx + r:.2f} {cy:.2f} c "
            f"{cx + r:.2f} {cy - k:.2f} {cx + k:.2f} {cy - r:.2f} {cx:.2f} {cy - r:.2f} c "
            f"{cx - k:.2f} {cy - r:.2f} {cx - r:.2f} {cy - k:.2f} {cx - r:.2f} {cy:.2f} c f"
        )

    def bullet(self, s, size=10.5):
        lines = wrap(s, size, False, CONTENT_W - 16)
        self.need(len(lines) * (size + 4.2))
        self._circle(MARGIN + 4, self.y - size + 3.2, 1.9, MAROON)
        for ln in lines:
            self._text(ln, MARGIN + 16, self.y - size, size)
            self.y -= size + 4.2

    def box(self, heading, lines, tone="info"):
        bg = WARNBG if tone == "warn" else BOXBG
        edge = WARNED if tone == "warn" else MAROON
        size = 10
        wrapped = []
        for ln in lines:
            wrapped += wrap(ln, size, False, CONTENT_W - 28)
        h = 18 + (len(wrapped) * (size + 4)) + (16 if heading else 0)
        self.need(h + 10)
        top = self.y
        self._rect(MARGIN, top - h, CONTENT_W, h, fill=bg)
        r, g, b = edge
        self.ops.append(f"{r:.3f} {g:.3f} {b:.3f} rg {MARGIN:.1f} {top - h:.1f} 3.2 {h:.1f} re f")
        y = top - 16
        if heading:
            self._text(heading, MARGIN + 14, y, 10.5, bold=True, color=edge)
            y -= 15
        for ln in wrapped:
            self._text(ln, MARGIN + 14, y, size)
            y -= size + 4
        self.y = top - h - 12

    def kv(self, rows):
        """Two-column reference table."""
        size = 10
        label_w = 165
        for k, v in rows:
            vlines = wrap(v, size, False, CONTENT_W - label_w - 8)
            self.need(len(vlines) * (size + 4) + 4)
            self._text(k, MARGIN, self.y - size, size, bold=True)
            for i, ln in enumerate(vlines):
                self._text(ln, MARGIN + label_w, self.y - size, size)
                self.y -= size + 4
            self.y -= 2
        self.y -= 4

    # -- output -------------------------------------------------------------
    def build(self, path):
        self.pages.append(self.ops)

        total = len(self.pages)
        streams = []
        for i, ops in enumerate(self.pages, start=1):
            # via esc() like all other text: the stream is later encoded latin-1,
            # which cannot represent an en dash, but esc() has already reduced it
            # to its single cp1252 byte.
            label = esc("LCNL Mela Stall Bookings – guide for organisers").decode("latin-1")
            foot = (f"BT /F1 8.5 Tf 0.42 0.40 0.44 rg 1 0 0 1 {MARGIN} {MARGIN - 12:.1f} Tm "
                    f"({label}) Tj ET")
            pno = f"Page {i} of {total}"
            foot += (f"BT /F1 8.5 Tf 0.42 0.40 0.44 rg 1 0 0 1 "
                     f"{A4_W - MARGIN - text_width(pno, 8.5, False):.1f} {MARGIN - 12:.1f} Tm "
                     f"({pno}) Tj ET")
            streams.append("\n".join(ops) + "\n" + foot)

        objs = {}
        n_font1, n_font2, n_font3 = 3, 4, 5
        first_page_obj = 6

        objs[1] = b"<< /Type /Catalog /Pages 2 0 R >>"
        kids = " ".join(f"{first_page_obj + i * 2} 0 R" for i in range(total))
        objs[2] = (f"<< /Type /Pages /Count {total} /Kids [{kids}] >>").encode()
        objs[n_font1] = b"<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>"
        objs[n_font2] = b"<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>"
        objs[n_font3] = b"<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Oblique /Encoding /WinAnsiEncoding >>"

        for i, s in enumerate(streams):
            pobj = first_page_obj + i * 2
            cobj = pobj + 1
            objs[pobj] = (
                f"<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {A4_W:.2f} {A4_H:.2f}] "
                f"/Resources << /Font << /F1 {n_font1} 0 R /F2 {n_font2} 0 R /F3 {n_font3} 0 R >> >> "
                f"/Contents {cobj} 0 R >>"
            ).encode()
            data = zlib.compress(s.encode("latin-1", "replace"))
            objs[cobj] = (b"<< /Length " + str(len(data)).encode()
                          + b" /Filter /FlateDecode >>\nstream\n" + data + b"\nendstream")

        out = bytearray(b"%PDF-1.4\n%\xe2\xe3\xcf\xd3\n")
        offsets = {}
        for num in sorted(objs):
            offsets[num] = len(out)
            out += f"{num} 0 obj\n".encode() + objs[num] + b"\nendobj\n"

        xref_at = len(out)
        maxobj = max(objs) + 1
        out += f"xref\n0 {maxobj}\n".encode()
        out += b"0000000000 65535 f \n"
        for num in range(1, maxobj):
            out += f"{offsets.get(num, 0):010d} 00000 n \n".encode()
        out += (f"trailer\n<< /Size {maxobj} /Root 1 0 R "
                f"/Info << /Title (LCNL Mela Stall Bookings Guide) "
                f"/Producer (LCNL) >> >>\nstartxref\n{xref_at}\n%%EOF\n").encode()

        os.makedirs(os.path.dirname(path), exist_ok=True)
        with open(path, "wb") as fh:
            fh.write(out)
        return len(out), total


# ---------------------------------------------------------------- the content
LINK = "https://lcnl.org/mela-stalls-2026-b7f3a9c2"

d = Doc()

d.title("Mela Stall Bookings",
        "A step-by-step guide for organisers – LCNL Golden Jubilee 50th Anniversary, "
        "Event 3: Mela, Monday 31 August 2026")

d.para("This guide explains how stall holders book a stall and what you need to do. "
       "You do not need to know anything technical. Read the two pages, and keep it "
       "handy for the payment checks.")

d.box("The one job that matters", [
    "The website records bookings, but it cannot see your bank account.",
    "Somebody must check the bank statement and tick off who has paid.",
    "A stall is not confirmed until you do that.",
])

# ------------------------------------------------------------------ section 1
d.h2("1. The booking link")

d.para("There is one link. Give it only to stall holders you have spoken to and are happy "
       "to have at the Mela.")

d.box("", [LINK], tone="info")

d.para("This page is hidden. It is not in the menu, not on the website anywhere, and will "
       "not appear in Google. The only way to find it is to be given the address.")

d.para("Important: hidden is not the same as locked. Anyone who is sent the link can use "
       "it, so if a stall holder forwards it to a WhatsApp group, everyone in that group "
       "can book. This is why you still check every booking. If the link gets out, tell "
       "Sunny and it can be changed to a new address in a couple of minutes.")

# ------------------------------------------------------------------ section 2
d.h2("2. What the stall holder does")

d.step(1, "Opens the link on their phone or computer.")
d.step(2, "Reads the details: date, venue, times, £75 fee, 2m x 2m pitch, set-up from "
          "10:30am, and the rule about moving vehicles by 11:30am.")
d.step(3, "Pays £75 by bank transfer using the reference shown on the page. The page "
          "builds the reference for them as they type their company name, so it should "
          "match what you see on the statement.")
d.step(4, "Fills in the form: company name, type of stall, what they are selling, their "
          "name, phone and email, and optionally a vehicle registration.")
d.step(5, "If they sell food or drink, they tick the food box and upload their Food "
          "Hygiene Certificate. They cannot submit without it. A phone photo is fine.")
d.step(6, "Ticks the two confirmation boxes and presses submit.")

d.para("They then see a thank-you page with their booking reference and the bank details "
       "again, and they receive a confirmation email within a minute or two.")

# ------------------------------------------------------------------ section 3
d.h2("3. What you receive")

d.para("Every time somebody books, an email is sent to Madhu, Sheetal and Sunny. It "
       "contains everything they entered, how many documents they uploaded, and the exact "
       "payment reference to look for on the bank statement.")

d.para("If a food stall has somehow submitted without a certificate, that email says so "
       "in red.")

# ------------------------------------------------------------------ section 4
d.h2("4. Checking bookings on the website")

d.step(1, "Go to the LCNL website and log in as normal.")
d.step(2, "In the dark blue admin bar along the top, click “Mela Stalls”.")
d.step(3, "You will see four boxes at the top: how many stalls are booked, how many have "
          "paid, how many are still awaiting payment, and how many are food stalls.")
d.step(4, "Below that is the full list, newest first.")

d.para("Each row shows the company, what they sell, their phone and email (both clickable), "
       "any documents they uploaded, and whether payment has been received.")

# ------------------------------------------------------------------ section 5
d.h2("5. Ticking off a payment (do this regularly)")

d.step(1, "Open your bank statement and find the transfers in, using the reference "
          "“MelaStall – company name”.")
d.step(2, "On the Mela Stalls page, find that company in the list.")
d.step(3, "Click the green “Mark paid” button on the right of their row.")
d.step(4, "The badge changes to “received” and records who ticked it and when.")

d.box("If you tick the wrong one", [
    "Click “Mark unpaid” on that row. It goes straight back. Nothing is lost.",
], tone="info")

d.para("The stall holder ticks a box on the form saying they have paid. That is only their "
       "word for it. The column headed “says paid” is their claim; “received” is "
       "your confirmation. Only trust the second one.")

# ------------------------------------------------------------------ section 6
d.h2("6. Food hygiene certificates")

d.para("Where a stall holder uploaded a document, the file name appears in their row as a "
       "link. Click it and the certificate opens in a new tab. You can print it from there.")

d.para("These documents are private. Only somebody logged in to the website admin can open "
       "them. They are not on the public website and cannot be found by anyone else.")

d.box("Watch for this warning", [
    "If an orange bar appears near the top saying a food stall has no hygiene certificate, "
    "ring that stall holder. They must produce one before the day.",
], tone="warn")

# ------------------------------------------------------------------ section 7
d.h2("7. Getting the list into a spreadsheet")

d.para("Click “Export CSV” at the top right of the list. It downloads a file you can "
       "open in Excel, with every booking, all their contact details, payment status and "
       "the reference to look for. Useful for printing a list for the day itself.")

# ------------------------------------------------------------------ section 8
d.h2("8. Cancelling a booking")

d.para("Click the red “Cancel” button on their row and confirm. The booking stays in "
       "the list, greyed out, so there is always a record. It stops counting towards your "
       "totals. Nothing is deleted and no email is sent to the stall holder, so ring them "
       "yourself if they need to know.")

# ------------------------------------------------------------------ section 9
d.h2("9. Things to keep an eye on")

d.bullet("Possible duplicate: an orange tag when the same company and email books twice. "
         "Check whether it was a double submission before treating it as two stalls.")
d.bullet("Awaiting payment: anyone still showing this a few days before the event needs "
         "a phone call.")
d.bullet("Food stall with no certificate: chase it, they cannot trade without one.")

# ------------------------------------------------------------------ section 10
d.h2("10. Key dates and numbers")

d.kv([
    ("Bookings close", "Midnight on Wednesday 26 August 2026. The form closes itself – "
                       "after that nobody can book, and they see a message telling them to "
                       "ring you."),
    ("Event day", "Monday 31 August 2026, 12:00pm to 6:00pm"),
    ("Venue", "RCT Centre, Bridleway off Headstone Lane, Harrow, HA2 6NG"),
    ("Stall holders arrive", "From 10:30am"),
    ("Car park cleared", "11:30am – vehicles moved to the grass area"),
    ("Stall fee", "£75 per stall, 2m x 2m, no equipment provided"),
    ("Bank", "Lohana Community North London, account 21497995, sort code 40-23-13"),
    ("Reference", "MelaStall – company name"),
])

d.h2("11. If something looks wrong")

d.para("Ring Sunny. Nothing a stall holder does can break the website, and no booking is "
       "ever lost – even if the notification emails stop arriving, every booking is still "
       "listed on the Mela Stalls page.")

d.para("Organiser contacts: Madhu Popat 07500 701 318, Sheetal Barai 07412 101 501.",
       color=MUTED)

size, pages = d.build("/var/www/html/lcnl/docs/LCNL-Mela-Stall-Bookings-Guide.pdf")
print(f"wrote docs/LCNL-Mela-Stall-Bookings-Guide.pdf  {pages} pages  {size/1024:.1f} KB")
