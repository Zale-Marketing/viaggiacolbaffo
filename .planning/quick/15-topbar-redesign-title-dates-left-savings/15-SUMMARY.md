---
phase: quick-15
plan: 15
subsystem: trip-detail-topbar
tags: [topbar, css-grid, status-pill, responsive, viaggio-php]
dependency_graph:
  requires: []
  provides: [3-column-topbar, status-pill-variants, savings-badge-icon]
  affects: [viaggio.php, assets/css/style.css]
tech_stack:
  added: []
  patterns: [CSS grid 3-column layout, status pill variants, piggy-bank innerHTML badge]
key_files:
  created: []
  modified:
    - viaggio.php
    - assets/css/style.css
decisions:
  - CSS grid (1fr auto 1fr) chosen over flexbox for stable 3-column alignment regardless of content width
  - Mobile 768px collapses to 2-col (hides center), 480px hides CTA — progressive reduction
  - topbar_icons PHP array renders inline icon symbol before $status_label for zero-JS status display
metrics:
  duration: 5min
  completed: 2026-03-09
  tasks_completed: 2
  files_modified: 2
---

# Quick Task 15: Topbar Redesign — Title + Dates Left, Savings + Status Center Summary

**One-liner:** 3-column grid topbar with stacked title/dates left, status pill + savings badge center, CTA right — replaces flat flex row.

## What Was Built

Replaced the 2-section (left/right) flex topbar with a 3-section CSS grid layout:

- **Left:** Trip title (bold, truncated) + dates row with calendar icon, stacked vertically
- **Center:** Savings badge (piggy-bank icon + bold euro amount) + status pill with icon and label
- **Right:** "Richiedi Preventivo" CTA button

Status pills are colour-coded:
- `confermata` — green
- `ultimi-posti` — yellow/amber
- `sold-out` — red
- `programmata` — grey/dim

Mobile responsive:
- 768px and below: center column hidden, dates hidden, collapses to 2-col grid
- 480px and below: CTA hidden

## Tasks Completed

| Task | Name | Commit | Files |
|------|------|--------|-------|
| 1 | Replace topbar HTML in viaggio.php | 2268fe3 | viaggio.php |
| 2 | Replace topbar CSS block in style.css | 6d84341 | assets/css/style.css |

## Deviations from Plan

None — plan executed exactly as written.

## Self-Check: PASSED

- viaggio.php: contains trip-topbar__center, trip-topbar__dates, trip-topbar__status, topbar_icons, piggy-bank
- assets/css/style.css: contains grid-template-columns: 1fr auto 1fr, trip-topbar__center, trip-topbar__dates, trip-topbar__status--confermata, trip-topbar__status--sold-out
- Commits 2268fe3 and 6d84341 exist in git log
