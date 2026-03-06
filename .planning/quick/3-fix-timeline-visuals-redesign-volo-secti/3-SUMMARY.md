---
quick: 3
subsystem: viaggio-php
tags: [timeline, volo, hotel, css, ui]
dependency_graph:
  requires: [quick-2]
  provides: [timeline-visual-polish, volo-redesign, hotel-section]
  affects: [viaggio.php, assets/css/style.css, data/trips.json]
tech_stack:
  added: []
  patterns: [css-grid-two-column, badge-overlay, collapsible-section]
key_files:
  created: []
  modified:
    - assets/css/style.css
    - viaggio.php
    - data/trips.json
decisions:
  - ".volo-details-grid used instead of .volo-details to avoid collision with JS-controlled id=volo-details element"
  - "Volo toggle rewritten to use style.display instead of classList.toggle('open') — new HTML uses display:none inline default"
  - "hotel key appended after volo key in trips.json — JSON validated via Node.js"
  - "hotel-section inserted between itinerary and cosa-include — matches tab nav order"
metrics:
  duration: ~8min
  completed: 2026-03-06
  tasks: 3
  files: 3
---

# Quick Task 3: Fix Timeline Visuals + Redesign Volo Section + Hotel Tab Summary

**One-liner:** Red gradient timeline line + ring-shadow dots + dark cards, redesigned two-column volo card grid, new Alloggi tab with 3 hotel cards from trips.json.

## What Was Done

### Task 1 — style.css: Timeline + Volo + Hotel CSS (commit 8699252)

**Timeline fixes:**
- `.timeline::before` — replaced solid `#000744` line with red gradient (`linear-gradient(to bottom, transparent, #CC0031 10%, #CC0031 90%, transparent)`) at 3px width, opacity 0.8
- `.timeline-dot` — changed background from `#000744` to `#CC0031`, replaced `rgba(255,255,255,0.2)` border with solid white `#FFFFFF`, added ring shadow `0 0 0 3px #CC0031` + glow
- `.timeline-card` — darkened background to `#0d1332`, added `border: 1px solid rgba(255,255,255,0.12)`, added `border-left: 3px solid #CC0031`, updated shadow
- `.timeline-card:hover` — added hover state with navy left border and lift effect

**Volo section redesign (full replacement of all old .volo-* rules):**
- `.volo-section` — clean dark bg `#080d24` with 60px padding
- `.volo-header-btn` — navy pill button (replaces ghost `.volo-toggle`)
- `.volo-cards-grid` — 2-col grid, max-width 900px (replaces `.volo-cards`)
- `.volo-card` — gradient navy background, 16px radius (replaces plain dark card)
- `.volo-card::before` — decorative airplane watermark (opacity 0.06)
- `.volo-card-type`, `.volo-route`, `.volo-route-arrow`, `.volo-details-grid`, `.volo-detail-label`, `.volo-detail-value`, `.volo-airline`, `.volo-scalo` — complete new component set
- Responsive: stacks to 1-col at 768px

**Hotel section (new):**
- `.hotel-section`, `.hotel-grid` (3-col responsive), `.hotel-card`
- `.hotel-badge-city` (top-left navy pill), `.hotel-badge-notti` (top-right red pill)
- `.hotel-stars` (gold), `.hotel-name`, `.hotel-desc`, `.hotel-address`
- `.hotel-colazione-yes` (green pill), `.hotel-colazione-no` (grey pill)
- Responsive: 2-col at 900px, 1-col at 600px

### Task 2 — viaggio.php: Volo HTML + Hotel Section + Tab Nav (commit 910391d)

**Volo section rewrite:**
- Button changed from `.volo-toggle` to `.volo-header-btn` with `id="volo-chevron"` on icon
- `#volo-details` now uses `style="display:none;"` inline default (not CSS class)
- Inner structure uses new CSS classes: `.volo-cards-grid`, `.volo-card`, `.volo-card-type`, `.volo-airline`, `.volo-route`, `.volo-details-grid`, `.volo-detail-label`, `.volo-detail-value`, `.volo-scalo`

**Tab nav reorder:**
- Added `Alloggi` tab button (conditional: `<?php if (!empty($trip['hotel'])): ?>`)
- Tab order: Itinerario | Alloggi | Cosa Include | Galleria | Richiedi Preventivo

**Hotel/Alloggi section inserted:**
- Placed between itinerary `</section>` and `COSA INCLUDE` comment
- Conditional on `$trip['hotel']` being non-empty
- `id="alloggi"` for tab scroll handler compatibility
- Uses `hotel-grid` with `foreach $trip['hotel']` — renders badges, stars, name, desc, address, colazione pill

**Volo JS toggle updated:**
- Uses `style.display !== 'none'` check instead of `classList.contains('open')`
- Sets `style.display = isOpen ? 'none' : 'block'`
- Rotates `#volo-chevron` via `style.transform`

### Task 3 — data/trips.json: Hotel Array (commit b961c05)

Added `"hotel"` key to `west-america-aprile-2026` with 3 entries:

| City | Hotel | Stars | Notti | Colazione |
|------|-------|-------|-------|-----------|
| Los Angeles | Hotel Santa Monica Beach | 4 | 3 | Yes |
| Las Vegas | The LINQ Hotel | 4 | 3 | No |
| San Francisco | Union Square Boutique Hotel | 4 | 4 | Yes |

JSON validated: `JSON valid` (Node.js parse check).

## Deviations from Plan

None — plan executed exactly as written.

## Self-Check

- [x] `assets/css/style.css` — updated (commit 8699252)
- [x] `viaggio.php` — updated (commit 910391d)
- [x] `data/trips.json` — updated (commit b961c05)
- [x] All 3 commits exist in git log
- [x] JSON valid (Node.js validated)
- [x] `.volo-details-grid` class used (no collision with `id="volo-details"`)

## Self-Check: PASSED
