---
phase: 06-admin-panel
plan: 06
subsystem: admin-edit-trip
tags: [admin, form-config, ai-generator, anthropic, ajax, preview-token, webhook]

requires:
  - phase: 06-03
    provides: admin/edit-trip.php with Form Config tab placeholder and sticky footer
  - phase: 06-05
    provides: api/generate-form.php using Anthropic claude-sonnet-4-6

provides:
  - Form Config tab fully implemented in admin/edit-trip.php
  - save_form_config AJAX handler writes form_config + webhook_url to trips.json
  - regenerate_token AJAX handler replaces /admin/ajax.php call
  - openPreview() JS function wired to sticky footer Anteprima button

affects: [api/generate-form.php, data/trips.json, viaggio.php]

tech-stack:
  added: []
  patterns: [ajax-post-same-page, json-body-preview, form-config-save-pattern]

key-files:
  created: []
  modified: [admin/edit-trip.php]

key-decisions:
  - "save_form_config merges webhook_url inside form_config object before writing to trips.json — matches existing schema where webhook_url lives inside form_config"
  - "regenerate_token AJAX posts to same page (window.location.href) instead of /admin/ajax.php — self-contained handler, no separate endpoint needed"
  - "previewToken declared as let (not const) to allow update by regenerateToken() without page reload"
  - "openPreview() checks both tripSlug and previewToken before opening — prevents broken preview for new unsaved trips"
  - "AI-generated JSON preview strips webhook_url before displaying — operator sees only AI-generated fields, webhook URL managed separately in dedicated input"

patterns-established:
  - "AJAX post to same page pattern: POST with action= field, PHP exits early with json_encode() before HTML output"
  - "Form Config save pattern: fetch JSON textarea value, validate JSON.parse(), post slug+form_config+webhook_url"

requirements-completed: [ADMIN-09, ADMIN-10]

duration: 2min
completed: 2026-03-06
---

# Phase 6 Plan 06: Form Config Tab + Save Actions Summary

**Form Config tab in edit-trip.php: full AI form generator UI with Anthropic-powered generation, editable JSON preview, webhook_url field, save-to-trips.json AJAX handler, and Anteprima/token-regen wired to preview token**

## Performance

- **Duration:** 2 min
- **Started:** 2026-03-06T21:33:56Z
- **Completed:** 2026-03-06T21:35:32Z
- **Tasks:** 1
- **Files modified:** 1

## Accomplishments
- Replaced Form Config tab placeholder (robot icon "coming soon") with full working implementation
- Added `save_form_config` PHP AJAX handler — merges webhook_url into form_config, writes to trips.json
- Added `regenerate_token` PHP AJAX handler — replaces stale `/admin/ajax.php` call with self-contained handler
- Added `generateAI()`, `saveFormConfig()`, `loadCurrentConfig()`, `openPreview()`, `regenerateToken()` JS functions
- Wired sticky footer Anteprima button to `openPreview()` (JS-driven, uses runtime `previewToken` variable)
- Token display updated with `id="token-display"` so `regenerateToken()` can update without page reload

## Task Commits

1. **Task 1: Complete Form Config tab — AI generator UI + webhook_url field + form_config save** - `5170540` (feat)

**Plan metadata:** (to be added after final commit)

## Files Created/Modified
- `admin/edit-trip.php` — Form Config tab fully implemented; two new PHP AJAX handlers; five new JS functions; sticky footer Anteprima button updated

## Decisions Made

1. **save_form_config merges webhook_url inside form_config** — matches the existing trips.json schema where webhook_url is a key inside form_config, not a top-level trip field.

2. **regenerate_token posts to same page** — Plan 03's `regenToken()` called `/admin/ajax.php` which was not built in this phase series. Instead, the handler lives directly in edit-trip.php, keeping all admin logic self-contained. `regenToken()` now delegates to `regenerateToken()` for backward compatibility.

3. **previewToken as `let` not `const`** — Required to allow `regenerateToken()` to update the JS variable after AJAX success, so subsequent `openPreview()` calls use the new token without a page reload.

4. **AI JSON preview strips webhook_url** — When displaying the AI-generated form_config, webhook_url is deleted from the preview object. The operator sets webhook_url separately in the dedicated input field above — this prevents accidental overwrite of the webhook URL on save.

## Deviations from Plan

None — plan executed exactly as written.

## Issues Encountered

None.

## User Setup Required

None — no external service configuration required beyond what Plan 05 established (ANTHROPIC_API_KEY in admin-config.json).

## Next Phase Readiness

- Phase 6 admin panel is now complete — all 6 plans (01-06) executed
- Full trip CRUD workflow operational: create, edit all fields including AI form generation, publish/draft, preview
- trips.json form_config properly managed through the UI
- Token regeneration fully self-contained in edit-trip.php

---
*Phase: 06-admin-panel*
*Completed: 2026-03-06*

## Self-Check: PASSED

- [x] `admin/edit-trip.php` exists: YES
- [x] Contains `save_form_config` PHP handler: YES (line 79)
- [x] Contains `regenerate_token` PHP handler: YES (line 96)
- [x] Contains `generateAI()` JS function: YES (line 1390)
- [x] Contains `saveFormConfig()` JS function: YES (line 1422)
- [x] Contains `openPreview()` JS function: YES (line 1382)
- [x] Contains `previewToken` let declaration: YES (line 1095)
- [x] Contains `tripSlug` const: YES (line 1096)
- [x] Form Config tab HTML (not placeholder): YES
- [x] Commit 5170540 exists: YES
