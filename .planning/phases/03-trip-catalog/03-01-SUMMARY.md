---
phase: 03-trip-catalog
plan: 01
subsystem: ui

tags: [php, vanilla-js, css, filter-engine, sticky-bar, url-deep-linking]

# Dependency graph
requires:
  - phase: 01-foundation
    provides: trips.json data store, load_trips()/load_tags() functions, trip card CSS classes, config.php constants
  - phase: 02-homepage
    provides: header.php hero_page pattern, footer.php, style.css design system, trip card permanent CSS

provides:
  - viaggi.php — complete catalog page with PHP data layer, filter bar, trip grid, count display, empty state
  - Phase 3 CSS section in style.css — catalog-hero, filter-bar, filter-pill, catalog-count, catalog-empty
  - Inline IIFE JS filter engine with continent single-select, tag AND multi-select, URL deep-linking

affects: [04-trip-detail, 05-destination-pages]

# Tech tracking
tech-stack:
  added: []
  patterns:
    - "Dual-row sticky filter bar: Row 1 single-select continents, Row 2 multi-select AND tags"
    - "Inline IIFE filter engine: data attributes on wrappers, no server round-trip"
    - "URL deep-linking via history.replaceState + URLSearchParams (PHP pre-apply on load)"
    - "Phase 3 CSS section appended to style.css with /* === PHASE 3: TRIP CATALOG === */ marker"

key-files:
  created:
    - viaggi.php
  modified:
    - assets/css/style.css

key-decisions:
  - "Filter bar sticky at top:80px to clear ~82px site header; comment notes UAT adjustment point (84px)"
  - "Active filter pill uses #000744 navy literal — var(--gold) maps to red urgency and must not be used for decorative accents"
  - "PHP pre-apply only single $_GET['tag'] (last value); multi-tag deep-link deferred to future phase"
  - "Inline IIFE at page bottom (no DOMContentLoaded wrapper needed); TALLY_CATALOG_URL guarded with PHP conditional"

patterns-established:
  - "Pattern: Trip card wrappers carry data-continent and data-tags attributes; JS reads these, never card internals"
  - "Pattern: Grid/empty-state panel swap via direct style.display toggle; fade transition on count number only"
  - "Pattern: PHP echoes init state into JS IIFE variable declarations ($init_continent, $init_tag)"

requirements-completed: [CATALOG-01, CATALOG-02, CATALOG-03, CATALOG-04, CATALOG-05, CATALOG-06]

# Metrics
duration: 1min
completed: 2026-03-06
---

# Phase 3 Plan 01: Trip Catalog Summary

**Sticky dual-row filter catalog page (viaggi.php) with inline IIFE JS engine: continent single-select + theme AND multi-select, URL deep-linking, empty state with Tally guard**

## Performance

- **Duration:** ~1 min
- **Started:** 2026-03-06T15:04:31Z
- **Completed:** 2026-03-06T15:06:15Z
- **Tasks:** 2
- **Files modified:** 2

## Accomplishments

- Appended Phase 3 CSS section (173 lines) to style.css: catalog-hero, filter-bar, filter-pill active/inactive states, catalog-count fade, catalog-empty with navy accent
- Created viaggi.php (236 lines): PHP bootstrap, 38vh hero, sticky dual-row filter bar with PHP-rendered continent/tag pills, trip grid loop with data attributes, count display, empty state with TALLY guard
- Inline IIFE filter engine: applyFilters(), syncPillsContinent(), syncPillsTags(), history.replaceState URL deep-linking, PHP URL pre-apply via $init_continent/$init_tag

## Task Commits

Each task was committed atomically:

1. **Task 1: Append Phase 3 CSS to style.css** - `5faee29` (feat)
2. **Task 2: Build viaggi.php catalog page** - `bacc572` (feat)

## Files Created/Modified

- `assets/css/style.css` - Phase 3 CSS section appended (catalog-hero, filter-bar, filter-pill, catalog-count, catalog-empty)
- `viaggi.php` - Complete catalog page: PHP data layer, hero, filter bar, trip grid with data attributes, count, empty state, inline JS filter engine

## Decisions Made

- Filter bar `top: 80px` to clear sticky site header (~82px); CSS comment notes UAT adjustment if overlap occurs
- Active filter pill uses `#000744` literal (not `var(--gold)` which maps to red `#CC0031` urgency)
- Single `$_GET['tag']` pre-apply only — PHP doesn't support repeated keys without `tag[]` notation; JS handles multi-tag state client-side after page load
- TALLY_CATALOG_URL guarded with `if(TALLY_CATALOG_URL)` — empty string is falsy in PHP, prevents broken iframe `src=""`
- Inline IIFE positioned after footer.php require so DOM elements exist when script runs (no DOMContentLoaded needed)

## Deviations from Plan

None - plan executed exactly as written.

## Issues Encountered

None.

## User Setup Required

None - no external service configuration required. TALLY_CATALOG_URL is empty string in config.php; when a Tally form is created, add the URL to config.php.

## Next Phase Readiness

- viaggi.php fully functional; trip cards link to `/viaggio/{slug}` (Phase 4 trip detail pages)
- Phase 4 can add tag pill links on trip detail pages pointing to `viaggi.php?tag={slug}` (URL deep-link ready)
- Phase 5 destination pages can link to `viaggi.php?continent={slug}` (URL deep-link ready)
- TALLY_CATALOG_URL constant placeholder ready — no code changes needed when Tally form is built

---
*Phase: 03-trip-catalog*
*Completed: 2026-03-06*
