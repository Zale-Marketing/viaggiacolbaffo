---
phase: quick-9
plan: 01
subsystem: quote-form, admin-panel
tags: [ui, layout, css, quote-form, admin]
dependency_graph:
  requires: []
  provides: [viaggio.php quote form layout fixes, admin toggle color, admin bracket input styles]
  affects: [viaggio.php, admin/edit-trip.php]
tech_stack:
  added: []
  patterns: [CSS grid 2-column layout, JS DOM builder with label wraps]
key_files:
  created: []
  modified:
    - viaggio.php
    - admin/edit-trip.php
decisions:
  - child-ages div moved outside bambini-row so it spans full width below both counter columns
  - codiceAgenzia kept as type="password" in both old and new layout
  - bambini-row retains display:block (no PHP gate) as required by plan
metrics:
  duration: ~5min
  completed: 2026-03-09
---

# Quick Task 9: Fix UI — Counters Side-by-Side, Child Ages Summary

**One-liner:** Seven UI fixes to quote form and admin panel — centered toggle, B2B field 50/50 grids, side-by-side counters, labeled child age inputs, insurance before price box, admin toggle color #000744, bracket input CSS.

## Tasks Completed

| Task | Name | Commit | Files |
|------|------|--------|-------|
| 1 | Fix viaggio.php quote form UI (FIX 1-5) | a98ac79 | viaggio.php |
| 2 | Fix admin/edit-trip.php CSS (FIX 7-8) | 30ebebc | admin/edit-trip.php |

## Changes Applied

### viaggio.php (FIX 1-5)

**FIX 1 — Centered B2B/B2C toggle**
Added outer `<div style="display:flex;justify-content:center;margin-bottom:24px;">` wrapper around `qf-toggle-wrap`. Toggle now horizontally centered in the form body.

**FIX 2 — B2B fields grid layout**
Restructured entire `#b2b-fields` content:
- Nome Agenzia: full-width (unchanged position)
- Codice Agenzia + Email Agenzia: 50/50 grid (was: Codice alone, then Email+Telefono together)
- Telefono + Nome Cliente Finale: 50/50 grid (was: Telefono in grid with Email, NomeCliente was standalone)
- Checkbox + emailClienteBox wrapped in a margin div
- Guarantee message margin-top reduced from 20px to 16px for tighter spacing

**FIX 3 — Side-by-side Adulti/Bambini counters**
Replaced two stacked `qf-field` divs with a `display:grid;grid-template-columns:1fr 1fr;gap:20px` wrapper. The `#child-ages` div moved outside `bambini-row` to span the full width below both counters. `bambini-row` retains `display:block` unconditionally.

**FIX 4 — Labeled child age inputs with rebuildChildAges()**
`rebuildChildAges()` now builds a `qf-child-age-wrap` div per child containing:
- A `<label class="qf-label">` showing "Eta bambino N *"
- An `<input>` with classes `qf-input qf-child-age-input`, `width:130px`, `placeholder="Anni (0-17)"`, `required=true`

`getChildAges()` selector updated from `.qf-child-age-input` to `#child-ages .qf-child-age-input` to avoid any potential selector collision.

**FIX 5 — Insurance before price box**
Swapped DOM order: `<!-- Insurance -->` PHP block now appears before `<!-- Price Box -->` div. Only one insurance block exists in the file.

### admin/edit-trip.php (FIX 7-8)

**FIX 7 — Toggle active color**
`.toggle-switch input:checked + .toggle-slider` changed from `background:var(--primary)` to `background:#000744`.

**FIX 8 — Bracket input and grid CSS**
Three new rules added before closing `</style>`:
- `.bracket-row input[type="number"]`: padding, border, border-radius, font-size, color, background
- `.bracket-row input[type="number"]:focus`: outline:none, border-color:var(--gold), box-shadow focus ring
- `.form-grid-2`: display:grid, two equal columns, 16px gap

## Verification

- `php -l viaggio.php`: No syntax errors
- `php -l admin/edit-trip.php`: No syntax errors

## Deviations from Plan

None — plan executed exactly as written.

## Self-Check: PASSED

- viaggio.php modified: confirmed (a98ac79)
- admin/edit-trip.php modified: confirmed (30ebebc)
- Both files pass PHP lint
- No extra insurance block introduced
- bambini-row has display:block (no PHP gate)
- codiceAgenzia type="password" preserved
