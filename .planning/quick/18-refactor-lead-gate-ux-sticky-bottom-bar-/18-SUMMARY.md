---
phase: quick-18
plan: 18
subsystem: viaggio.php lead gate
tags: [lead-gate, ux, mobile, sticky-bar, bottom-sheet, intersection-observer]
dependency_graph:
  requires: [quick-17]
  provides: [gate-bar, gate-overlay, gate-sheet, IntersectionObserver sentinel logic]
  affects: [viaggio.php, assets/css/style.css]
tech_stack:
  added: []
  patterns: [IntersectionObserver, bottom-sheet, sticky-bar, CSS transform slide-up]
key_files:
  modified:
    - viaggio.php
    - assets/css/style.css
decisions:
  - "HTML gate divs inserted before footer.php include (before </body>) — footer include at line 889 renders </body>, so new divs placed just above it"
  - "sentinel is last .timeline-item:not(.gated-content .timeline-item) — watches end of last free day, shows bar when user scrolls past it"
  - "Privacy checkbox validation added to gate form (required); marketing checkbox opt-in included in webhook payload"
  - "gated-content--hidden updated to blur(7px)/opacity 0.3 (removed position:relative from Quick-17 which is not needed for the filter approach)"
metrics:
  duration: "~8 minutes"
  completed: "2026-03-09"
  tasks_completed: 3
  files_modified: 2
---

# Quick-18: Refactor Lead Gate UX — Sticky Bottom Bar + Bottom Sheet

**One-liner:** Replaced full-page `#lead-gate` overlay with IntersectionObserver-triggered sticky bar + slide-up bottom sheet pattern, preserving localStorage unlock and webhook POST.

## Tasks Completed

| # | Task | Commit | Files |
|---|------|--------|-------|
| 1 | Replace HTML — remove old gate div, add bar + sheet before footer | 6e0a6e8 | viaggio.php |
| 2 | Replace gate JS with IntersectionObserver sentinel + openSheet/closeSheet | 8774511 | viaggio.php |
| 3 | Replace lead gate CSS block in style.css | fa7aeb8 | assets/css/style.css |

## What Was Built

**viaggio.php HTML changes:**
- Deleted entire `<!-- LEAD GATE OVERLAY -->` div (id=`lead-gate`, ~41 lines)
- Inserted three new elements just before footer include:
  - `#gate-bar .gate-bar` — sticky bottom bar with lock icon, text, and "Sblocca ora" button
  - `#gate-overlay .gate-overlay` — full-screen backdrop for sheet
  - `#gate-sheet .gate-sheet` — slide-up bottom sheet with drag handle, form (nome/cognome/email/telefono), privacy + marketing checkboxes, error div, submit button

**viaggio.php JS changes:**
- Replaced old overlay-reference JS with new:
  - `IntersectionObserver` watches `sentinel` (last `.timeline-item` not inside `.gated-content`) — shows bar when user scrolls past it, hides bar + closes sheet when user scrolls back
  - `openSheet()` / `closeSheet()` toggle `gate-sheet--open` + `gate-overlay--visible` classes and lock/unlock `body.overflow`
  - Bar click → `openSheet()`; overlay click → `closeSheet()`
  - Privacy checkbox validation (required); marketing checkbox in payload (optional)
  - Webhook POST still fails silently (catch calls `doUnlock`)
  - localStorage key pattern preserved: `vcb_unlocked_{slug}`

**assets/css/style.css changes:**
- Removed all `.lead-gate` / `.lead-gate__*` / `.lead-gate--unlocked` rules (~69 lines)
- Added gate-bar (fixed, z-index 300, translateY(100%) hidden, slides up via `.gate-bar--visible`)
- Added gate-overlay (fixed inset, z-index 310, opacity 0 → 1 via `.gate-overlay--visible`, backdrop-filter blur)
- Added gate-sheet (fixed bottom, z-index 320, translateY(100%) → 0 via `.gate-sheet--open`, max-height 92vh, scroll)
- Updated `.gated-content--hidden`: blur(7px), opacity 0.3 (removed `position:relative` from Quick-17)
- Mobile `@media (max-width: 600px)`: hides bar text span, shows `::after` fallback text, single-column form rows

## Deviations from Plan

None — plan executed exactly as written.

## Self-Check

### Files exist:
- viaggio.php — present, modified
- assets/css/style.css — present, modified
- .planning/quick/18-refactor-lead-gate-ux-sticky-bottom-bar-/18-SUMMARY.md — this file

### Commits exist:
- 6e0a6e8 — feat(quick-18): replace old #lead-gate overlay HTML
- 8774511 — feat(quick-18): replace lead gate JS
- fa7aeb8 — feat(quick-18): replace old lead-gate CSS

## Self-Check: PASSED
