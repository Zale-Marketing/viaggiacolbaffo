---
phase: 06-admin-panel
plan: 02
subsystem: admin-auth-dashboard
tags: [auth, session, dashboard, drag-and-drop, ajax, soft-delete, trash]
dependency_graph:
  requires: [admin-config.json-overlay, admin.css, load_trips, save_trips]
  provides: [admin-session-auth-pattern, admin-dashboard, admin-login]
  affects: [admin/login.php, admin/index.php]
tech_stack:
  added: []
  patterns: [session-auth-dual-format, html5-drag-api, ajax-json-response, soft-delete-trash]
key_files:
  created: [admin/login.php, admin/index.php]
  modified: []
decisions:
  - JSON-body detection added to AJAX dispatcher — reorder action sends Content-Type application/json; PHP detects this and pre-decodes body before the action switch
  - Publish toggle updates both the pill class AND the delete-button data-published attribute in a single DOM pass to keep modal wording in sync
  - Reload after soft-delete/restore/empty-trash (800ms delay) ensures stats bar and trash section reflect current state without manual refresh logic
metrics:
  duration: "3 minutes"
  completed: 2026-03-06
  tasks: 2
  files: 2
---

# Phase 6 Plan 02: Admin Login + Dashboard Summary

**One-liner:** admin/login.php with bcrypt+plaintext dual-format session auth; admin/index.php with stats bar, HTML5 drag-and-drop reorder, inline publish toggle, soft-delete modal, trash section — all AJAX actions return JSON and write via save_trips().

## Tasks Completed

| # | Task | Commit | Files |
|---|------|--------|-------|
| 1 | admin/login.php — session auth with dual password format support | 4cbb707 | admin/login.php |
| 2 | admin/index.php — trip dashboard with stats, table, DnD reorder, trash | 002d3bc | admin/index.php |

## What Was Built

### Task 1 — admin/login.php

Self-contained HTML page (no public header.php or footer.php). POST handler at top: `session_start()`, loads `config.php`, compares submitted password against `ADMIN_PASSWORD` using dual-format logic — bcrypt (`$2y$` prefix → `password_verify()`) or plaintext string comparison. On success: `session_regenerate_id(true)`, `$_SESSION['admin'] = true`, redirect to `/admin/`. On failure: sets `$error = 'Password errata.'` displayed below the form in a `.admin-alert-error` div. Loads admin.css, Inter font (Google Fonts CDN), Font Awesome 6 CDN. Uses `.admin-login` / `.admin-login__card` CSS classes already defined in admin.css.

### Task 2 — admin/index.php

Session-guarded dashboard. Full AJAX dispatcher handles 5 actions before any HTML output:

- **toggle_published**: flips `published` bool, saves, returns `{success, published}`.
- **soft_delete**: sets `deleted: true`, saves.
- **restore**: sets `deleted: false`, saves.
- **empty_trash**: hard-deletes all trips where `deleted === true` via `array_filter` + `array_values`.
- **reorder**: accepts JSON body with `{ action, slugs }` array, updates `position` index on each trip.

PHP data layer separates `$active_trips` (deleted !== true) from `$trash_trips`, sorts active by `position` (missing = 999), computes total/published/draft counts.

**Stats bar**: 3 cards (Viaggi Totali, Pubblicati, Bozze).

**Active trips table**: columns drag-handle | Title | Continent | Tags (gold chips) | Status pill (clickable toggle) + trip status pill | Actions (Modifica, Anteprima if token exists, Elimina). Rows have `draggable="true"` and `data-slug`.

**Drag-and-drop**: `dragstart` / `dragover` / `drop` events on `tbody` rows. Drop inserts before/after based on cursor Y vs row midpoint. Debounced (400ms) `saveReorder()` posts new slug array as JSON to `/admin/`.

**Publish toggle**: clicking the pill POSTs `toggle_published`, updates pill classes and delete-button `data-published` attribute in DOM.

**Delete modal**: if trip is published warns "È pubblicato"; if draft shows "Andrà nel Cestino." confirmation. Confirm triggers `soft_delete` AJAX, removes row from DOM, reloads after 800ms.

**Trash section**: shown only if `count($trash_trips) > 0`. Per-row Ripristina button + bulk Svuota Cestino (confirm prompt → `empty_trash` AJAX).

**Toast**: fixed-position div, auto-hides after 2.2s, turns red on error.

## Decisions Made

1. **JSON-body detection for reorder** — The reorder AJAX fetch sends `Content-Type: application/json` with the body, so `$_POST['action']` is empty. Added a pre-check: if Content-Type contains `application/json`, decode `php://input` and set `$ajax_action` from the decoded body. This keeps the single AJAX dispatcher pattern clean.

2. **Publish toggle syncs delete-button attribute** — After toggling, the `.btn-delete` `data-published` attribute is updated so the delete modal shows the correct warning message if the user deletes immediately after toggling — no page reload required.

3. **Reload strategy for state changes** — After soft-delete, restore, and empty-trash, the page reloads after 800ms (enough time for the toast to appear). This avoids hand-crafting DOM updates for the stats bar and trash section, which are server-rendered.

## Deviations from Plan

### Auto-fixed Issues

**1. [Rule 1 - Bug] Fixed AJAX reorder JSON-body detection**
- **Found during:** Task 2 implementation review
- **Issue:** `$ajax_action = $_POST['action']` is empty when request body is JSON (reorder sends `application/json`, not form-encoded). The reorder action would never match.
- **Fix:** Added Content-Type check before the AJAX switch: if `application/json`, decode `php://input` and extract `action` from the decoded body.
- **Files modified:** admin/index.php (lines 21-29)
- **Commit:** 002d3bc (included in Task 2 commit)

## Self-Check

- [x] `admin/login.php` exists: YES
- [x] `admin/login.php` contains session_start, password_verify, ADMIN_PASSWORD: YES (3 matches)
- [x] `admin/login.php` contains session_regenerate_id: YES
- [x] `admin/login.php` does NOT include public header.php: CONFIRMED
- [x] `admin/index.php` exists: YES
- [x] `admin/index.php` contains load_trips, dragstart, empty_trash: YES (6 matches for combined pattern)
- [x] `admin/index.php` contains session_start: YES
- [x] Commits 4cbb707 and 002d3bc exist in git log: CONFIRMED

## Self-Check: PASSED
