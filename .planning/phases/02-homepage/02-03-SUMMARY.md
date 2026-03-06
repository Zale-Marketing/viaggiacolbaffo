---
phase: 02-homepage
plan: "03"
subsystem: ui
tags: [php, homepage, hero, trips-carousel, destinations, footer]

# Dependency graph
requires:
  - phase: 02-homepage-02-01
    provides: CSS classes (.hero, .urgency-bar, .trips-carousel, .dest-grid, .why-grid, .founder-grid, .testimonials-grid, .b2b-banner, .site-footer) all defined in style.css
  - phase: 02-homepage-02-02
    provides: PHP dependencies verified (config.php, functions.php, trips.json, header.php, footer.php) — load_trips() and published filter logic confirmed correct
  - phase: 01-foundation
    provides: config.php (ROOT constant, WHATSAPP_NUMBER), functions.php (load_trips, save_trips), trips.json with 2 published trips, header.php, main.js
provides:
  - "Complete index.php — 8-section homepage assembled in a single file write"
  - "Production includes/footer.php — shared footer for all pages from Phase 2 onward"
  - "6-destination grid linking to destinazione.php?slug= for Phase 5"
  - "Active trips carousel reading published===true trips from trips.json"
affects:
  - phase-03-catalog
  - phase-04-trip-detail
  - phase-05-destinations
  - all-future-pages (footer.php is now shared)

# Tech tracking
tech-stack:
  added: []
  patterns:
    - PHP foreach loop rendering trips from load_trips() with published===true filter
    - $destinations PHP array defined inline before section — no external data source
    - Footer uses defined() checks for WHATSAPP_NUMBER and CONTACT_EMAIL constants — graceful fallback if undefined
    - date('Y') for auto-updating copyright year

key-files:
  created:
    - index.php
  modified:
    - includes/footer.php

key-decisions:
  - "destinazione.php?slug= used for destination card hrefs (direct PHP URL per CONTEXT.md locked decision — not /destinazioni/slug)"
  - "No logo inside hero section — header.php logo is sufficient (per locked decision)"
  - "Urgency bar is hardcoded static HTML — not data-bound to trips.json"
  - "Footer WHATSAPP_NUMBER uses str_replace([' ', '+'], ['', '']) to build wa.me URL — matches config.php constant format"
  - "date('Y') used for copyright year — auto-updates without operator maintenance"

patterns-established:
  - "Single-file write pattern: index.php written in one atomic Write operation, not assembled via appends"
  - "PHP bootstrap order: config.php -> functions.php -> set $page_title + $hero_page -> header.php"
  - "Footer defined() guards: all PHP constants checked with defined() before use, fallback strings provided"

requirements-completed: [HOME-01, HOME-02, HOME-03, HOME-04, HOME-05, HOME-06, HOME-07, HOME-08, HOME-09]

# Metrics
duration: 2min
completed: 2026-03-06
---

# Phase 2 Plan 03: Homepage Write Summary

**Complete index.php homepage (8 sections: hero, urgency bar, trips carousel, 6-destination grid, why-Baffo, founder story, testimonials, B2B banner) and production footer.php replacing placeholder — shared footer now ready for all future pages.**

## Performance

- **Duration:** 2 min
- **Started:** 2026-03-06T13:10:14Z
- **Completed:** 2026-03-06T13:10:37Z
- **Tasks:** 2
- **Files modified:** 2

## Accomplishments

- Created complete `index.php` in a single atomic write — all 8 homepage sections from PHP bootstrap to footer include, no partial-write risk
- Active trips carousel reads `published === true` trips from trips.json via `load_trips()`, renders with snap-scroll mobile / grid desktop pattern
- Replaced placeholder `includes/footer.php` with 3-column production footer (brand + nav + contacts), WhatsApp link built dynamically from `WHATSAPP_NUMBER` constant, IATA badge, navy bottom bar — now shared by all future pages

## Task Commits

Each task was committed atomically:

1. **Task 1: Create complete index.php — all 8 sections in a single file write** - `8e1286f` (feat)
2. **Task 2: Replace placeholder footer.php with production footer** - `ef826c2` (feat)

**Plan metadata:** (docs commit — see below)

## Files Created/Modified

- `index.php` — Complete homepage: PHP bootstrap, hero (tagline + 2 CTAs), urgency bar, active trips carousel, 6-destination grid, why-Baffo 4-block grid, founder section (portrait + 3 gold stats), 3 testimonial cards, B2B banner, footer include
- `includes/footer.php` — Production footer replacing placeholder: 3-column grid, dynamic WhatsApp/phone/email links, Instagram/Facebook social icons, IATA Accredited Agency badge, auto-year copyright

## Decisions Made

- `destinazione.php?slug=` used for destination hrefs (per locked CONTEXT.md decision — not URL-rewritten paths)
- No logo repeated inside hero — header.php provides the logo
- Urgency bar hardcoded: "West America Aprile 2026 — Ultimi 5 posti disponibili" (per requirements, not dynamic)
- Footer `defined()` guards added for WHATSAPP_NUMBER and CONTACT_EMAIL — graceful degradation if constants undefined in any future page context
- `date('Y')` in footer for auto-updating copyright year

## Deviations from Plan

None — plan executed exactly as written.

## Issues Encountered

None. PHP CLI unavailable in bash environment (documented in STATE.md from Plan 02), so verification used content inspection grep patterns — all section markers confirmed present.

## User Setup Required

None - no external service configuration required.

## Next Phase Readiness

- `index.php` complete and self-contained — serves the full homepage immediately upon FTP deploy
- `includes/footer.php` is now the production footer shared by all pages — Phase 3 (catalog), Phase 4 (trip detail), Phase 5 (destinations) can all include it directly
- Destination card links (`destinazione.php?slug=america` etc.) point to Phase 5 pages — links exist now, pages built later
- Trip card links (`/viaggio/slug`) point to Phase 4 pages — same pattern

---
*Phase: 02-homepage*
*Completed: 2026-03-06*
