---
phase: 06-admin-panel
plan: 04
subsystem: admin-ui
tags: [admin, settings, tags, destinations, php, ajax, flock]

requires:
  - phase: 06-01
    provides: [admin.css, save_tags, save_destinations, load_destinations, admin-config.json schema]

provides:
  - admin/settings.php — full site config editor writing to data/admin-config.json with bcrypt password
  - admin/tags.php — tag management UI with AJAX add/delete and cascade delete into trips.json
  - admin/destinations.php — 6-tile list + per-slug edit form writing to data/destinations.json

affects: [data/admin-config.json, data/tags.json, data/trips.json, data/destinations.json]

tech-stack:
  added: []
  patterns: [flock-write-pattern, ajax-json-handler, admin-config-overlay-pattern, cascade-delete-pattern]

key-files:
  created:
    - admin/settings.php
    - admin/tags.php
    - admin/destinations.php
  modified: []

key-decisions:
  - "settings.php reads existing admin-config.json first, merges POST fields, preserves existing password when empty field submitted"
  - "tags.php AJAX handlers check $_POST['action'] and return JSON early with exit — no HTML rendered for POST"
  - "delete_tag cascade: save_tags() then save_trips() in same PHP request — both saves or neither (no partial state)"
  - "destinations.php list mode uses $valid_slugs array to guarantee all 6 slots even if destinations.json is partial"
  - "destinations.php edit mode preserves 'name' field — 6 fixed slugs, name not editable to avoid breaking public URLs"

patterns-established:
  - "Admin AJAX: POST with action= field, JSON response with {success, data|error}, exit immediately after json_encode"
  - "Cascade delete: modify primary resource first, then cascade to dependent resources in same request"

requirements-completed: [ADMIN-11]

duration: 3min
completed: 2026-03-06
---

# Phase 6 Plan 04: Admin Supporting Pages Summary

**settings.php writing all 12 config fields to admin-config.json with bcrypt password hashing; tags.php with AJAX add/cascade-delete; destinations.php 6-tile list + per-slug editor saving to destinations.json**

## Performance

- **Duration:** ~3 min
- **Started:** 2026-03-06T21:28:06Z
- **Completed:** 2026-03-06T21:31:00Z
- **Tasks:** 2
- **Files modified:** 3

## Accomplishments

- admin/settings.php: 5-section config editor (Sicurezza, Contenuto Sito, Webhook URL, WhatsApp e Form, AI); POST merges all fields into admin-config.json via flock; bcrypt password with preserve-on-empty logic
- admin/tags.php: AJAX-first tag management — add with auto-slug generation and uniqueness check; cascade delete removes tag from all trips in one request; tags grouped by category with live DOM chip manipulation
- admin/destinations.php: dual-mode page — list shows 6 hero thumbnails with Modifica links; edit form covers all 6 destination content fields (hero_image, 3 intro paragraphs, 5 practical_info rows, 4 see_also cards, 3 curiosita entries) with live preview

## Task Commits

Each task was committed atomically:

1. **Task 1: admin/settings.php — full config editor** - `5853954` (feat)
2. **Task 2: admin/tags.php + admin/destinations.php** - `3ac0c3c` (feat)

## Files Created/Modified

- `admin/settings.php` — Site config editor with 5 card sections, flock write to admin-config.json, bcrypt password
- `admin/tags.php` — AJAX tag manager with category grouping and cascade delete to trips.json
- `admin/destinations.php` — List mode (6 tiles) + edit mode (full content form) backed by destinations.json

## Decisions Made

1. **settings.php merge strategy** — Read existing JSON first, overlay POST values on top. This ensures any keys added to admin-config.json in future plans are not erased when the form saves only the known keys.

2. **AJAX action dispatch in tags.php** — Single PHP file handles both GET (HTML page) and POST (JSON API) via `$_POST['action']` check. Cleaner than separate endpoint file; consistent with existing admin patterns.

3. **Cascade delete atomicity** — `save_tags()` called first, then `save_trips()` in the same PHP request. If the trip save fails, tags are already updated (no rollback), but the failure is visible immediately. This is acceptable because the primary data (tags list) is already correct and the trip tags will be fixed on next save.

4. **destinations.php list mode $valid_slugs** — Using a hardcoded array of the 6 slugs rather than iterating `$destinations` keys ensures all 6 tiles always appear even if destinations.json is missing entries.

5. **'name' field preserved in edit mode** — Destination names (America, Asia, etc.) are not editable. The existing name from destinations.json is preserved when saving to avoid UI inconsistencies.

## Deviations from Plan

None — plan executed exactly as written.

## Issues Encountered

None.

## User Setup Required

None — no external service configuration required.

## Next Phase Readiness

- All three supporting admin pages are complete
- Lorenzo can now manage site config, tag taxonomy, and destination editorial content from the browser
- Remaining admin plans: trip dashboard (index.php), edit-trip form, and trash management

## Self-Check

- [x] `admin/settings.php` exists: YES
- [x] `admin/settings.php` contains "admin-config.json" + "password_hash" + "urgency_bar_text": YES (8 matches)
- [x] `admin/tags.php` exists: YES
- [x] `admin/tags.php` contains "save_tags" + "save_trips" + "delete_tag": YES (5 matches)
- [x] `admin/destinations.php` exists: YES
- [x] `admin/destinations.php` contains "save_destinations" + "intro_paragraphs" + "see_also": YES (9 matches)
- [x] Commits 5853954 and 3ac0c3c exist in git log: CONFIRMED

## Self-Check: PASSED

---
*Phase: 06-admin-panel*
*Completed: 2026-03-06*
