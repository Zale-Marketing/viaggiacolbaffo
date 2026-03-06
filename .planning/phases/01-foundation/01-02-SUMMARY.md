---
phase: 01-foundation
plan: 02
subsystem: data
tags: [json, php, data-store, trips, tags]

# Dependency graph
requires:
  - phase: 01-01
    provides: DATA_DIR constant from includes/config.php, data/ directory with .htaccess protection
provides:
  - data/trips.json as sole data store with full DATA-01 schema and 2 trips
  - data/tags.json with 22 slug/label entries for filtering
  - includes/functions.php with 6-function data access API used by all future phases
affects: [01-03, 02-homepage, 03-catalog, 04-trip-detail, 05-destinations, 06-admin]

# Tech tracking
tech-stack:
  added: []
  patterns:
    - "All PHP data access via functions.php — no direct file_get_contents calls in page files"
    - "DATA_DIR constant from config.php used for all file paths — never relative paths in functions.php"
    - "array_values() wraps array_filter() results — ensures 0-indexed JSON arrays downstream"
    - "flock() in save_trips for safe concurrent writes to trips.json"

key-files:
  created:
    - data/trips.json
    - data/tags.json
    - includes/functions.php
  modified: []

key-decisions:
  - "trips.json uses JSON_UNESCAPED_UNICODE so Italian characters are literal UTF-8, not \\u escape sequences — required for readable JSON editing"
  - "Japan trip form_config is empty object — sold-out trips have no active quote form, prevents Phase 4 form from rendering on sold-out pages"
  - "West America itinerary covers 7 representative days (not all 15) — Phase 4 renders itinerary as collapsible list, 7 entries provides realistic UI test data"
  - "save_trips uses array_values() — ensures re-indexed array after deletions, otherwise JSON encodes as object not array"

patterns-established:
  - "Data access pattern: all pages call load_trips() or get_trip_by_slug() from functions.php — never parse JSON directly"
  - "Tag filtering pattern: get_trips_by_tag('america') returns 0-indexed array of matching trips — Phase 3 catalog filters use this"
  - "form_config pattern: empty object {} signals sold-out/no-form, structured object with room_types/addons/fields signals active quote form"

requirements-completed: [DATA-01, DATA-02, DATA-03, DATA-04]

# Metrics
duration: 2min
completed: 2026-03-06
---

# Phase 1 Plan 02: Data Store Summary

**trips.json (2 trips, full DATA-01 schema) + tags.json (22 tags) + functions.php (6-function PHP data access API over DATA_DIR)**

## Performance

- **Duration:** 2 min
- **Started:** 2026-03-06T11:09:30Z
- **Completed:** 2026-03-06T11:11:50Z
- **Tasks:** 2
- **Files modified:** 3

## Accomplishments

- trips.json contains West America Aprile 2026 (status=ultimi-posti, price_from=3490) with all 19 DATA-01 fields including 7-day itinerary, form_config with room_types/addons, and full Italian copy — no schema additions needed in Phases 2–6
- Japan Classico 2025 (status=sold-out) provides test data for empty-state UI, status pills, and sold-out form suppression in Phases 3 and 4
- tags.json covers 6 continents, 6 themes, 3 occasion tags, 5 month tags, and 2 special tags — enables Phase 3 dual-row filter without additions
- functions.php exposes all 6 required functions via DATA_DIR constant, with array_values() ensuring 0-indexed arrays and flock() for write safety

## Task Commits

Each task was committed atomically:

1. **Task 1: trips.json and tags.json with full sample data** - `97a72c9` (feat)
2. **Task 2: functions.php data access API** - `aa54ae9` (feat)

**Plan metadata:** (docs commit — see final_commit step)

## Files Created/Modified

- `data/trips.json` - Full DATA-01 schema with 2 trips: West America (published, ultimi-posti) and Japan (published, sold-out)
- `data/tags.json` - 22 slug/label tag entries covering continents, themes, occasions, months, and specials
- `includes/functions.php` - 6 data access functions: load_trips, get_trip_by_slug, get_trips_by_continent, get_trips_by_tag, save_trips, load_tags

## Decisions Made

- Japan `form_config` is empty object `{}` — sold-out trips have no active quote form. Phase 4 will check for `form_config.fields` presence before rendering the form widget, ensuring sold-out pages don't show a non-functional booking form.
- West America itinerary uses 7 representative day entries (not all 15 days) — sufficient for realistic UI testing of the Phase 4 collapsible itinerary component without bloating the data file.
- `save_trips` wraps result in `array_values()` — ensures the JSON array stays 0-indexed after any deletions via the Phase 6 admin panel. Without this, PHP's `array_filter` preserves original keys, causing JSON to encode as an object `{}` instead of array `[]`.
- Italian characters stored as literal UTF-8 in JSON — readable by operators editing the file directly, avoids `\u00e8` style escapes that make raw JSON hard to read/maintain.

## Deviations from Plan

None - plan executed exactly as written.

## Issues Encountered

- `php -l` syntax verification skipped — PHP is not in the PATH on this machine (same as Plan 01-01). functions.php was verified by direct inspection: all 6 function signatures are syntactically correct, DATA_DIR used throughout, no relative paths, no define fallback. Node.js structural check confirmed all 6 functions present with correct naming.

## User Setup Required

None - no external service configuration required.

## Next Phase Readiness

- data/trips.json is ready — Phase 2 (homepage) can call `load_trips()` and render the first published trip immediately
- data/tags.json is ready — Phase 3 (catalog) can call `load_tags()` for dual-row filter
- functions.php is ready — all 6 functions available to any PHP file that `require_once`s config.php first
- form_config schema is complete — Phase 4 (trip detail + quote form) can parse room_types/addons/fields without schema changes
- No blockers for 01-03 (design system) or any Phase 2–6 plan

---
*Phase: 01-foundation*
*Completed: 2026-03-06*

## Self-Check: PASSED

All 4 files verified present on disk. Both task commits (97a72c9, aa54ae9) confirmed in git log.
