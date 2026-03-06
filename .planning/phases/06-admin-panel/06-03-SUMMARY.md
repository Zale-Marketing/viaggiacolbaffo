---
phase: 06-admin-panel
plan: 03
subsystem: admin-edit-trip
tags: [admin, edit-form, tabbed-ui, drag-and-drop, itinerary, tags, media-preview]
dependency_graph:
  requires: [06-01]
  provides: [admin/edit-trip.php, trip-edit-form-all-fields]
  affects: [data/trips.json]
tech_stack:
  added: []
  patterns: [multi-tab-form, drag-and-drop-reorder, live-preview-js, char-counter, slug-lock-pattern, flock-write-pattern]
key_files:
  created: [admin/edit-trip.php]
  modified: []
decisions:
  - slug_locked derived from trip published flag at PHP render time; JS receives slugLocked constant via json_encode
  - Itinerary builder combines drag-and-drop (HTML5 dragstart/drop) AND arrow buttons as fallback in same implementation
  - Gallery textarea drives preview grid via JS split on newline; remove button syncs back to textarea
  - Form Config tab renders as placeholder only — full implementation deferred to Plan 06
  - form_config preserved from existing trip data during save — NOT overwritten by this form
  - preview_token generated with bin2hex(random_bytes(16)) on first save when absent
metrics:
  duration: "4 minutes"
  completed: 2026-03-06
  tasks: 2
  files: 1
---

# Phase 6 Plan 03: Trip Edit Form Summary

**One-liner:** Complete multi-tab trip edit form (admin/edit-trip.php) with POST handler saving all fields to trips.json, slug locking, hero/gallery live preview, 160-char countdown, drag-and-drop itinerary builder, tag chip selector, and sticky save footer.

## Tasks Completed

| # | Task | Commit | Files |
|---|------|--------|-------|
| 1 | Scaffold edit-trip.php — POST handler, PHP data layer, tab navigation, Info Base + Media + Contenuto tabs | d6463ce | admin/edit-trip.php |
| 2 | Itinerario builder with drag-and-drop + arrow buttons (included in same file) | d6463ce | admin/edit-trip.php |

## What Was Built

### admin/edit-trip.php — 1321 lines

A self-contained PHP + inline JS admin page covering the full trip editing workflow.

**PHP section:**
- Session auth guard redirects unauthenticated users to `/admin/login.php`
- Mode detection: `?new=1` initialises an empty trip template; `?slug=xxx` loads from `load_trips()` via `get_trip_by_slug()`; missing slug redirects to `/admin/`
- `$slug_locked = !$is_new && ($trip['published'] ?? false)` — prevents slug mutation after first publish
- `php_generate_slug()` mirrors the JS `generateSlug()` function: strtolower → iconv TRANSLIT → preg_replace non-alphanumeric → trim → collapse hyphens
- POST handler reads all form fields, builds `$trip_data` array with all schema fields, calls `save_trips()` and redirects with `?saved=1` (PRG pattern)
- Gallery, included, excluded: explode on newline, filter empty, array_values
- Itinerary: `itinerary_title[]` + `itinerary_desc[]` arrays assembled with `day` = `index+1` during save
- Tags: from `tags_json` hidden input (JSON array of slugs updated by JS on every chip toggle)
- `form_config` preserved from existing trip — not touched here (Plan 06)
- `preview_token` generated on first save via `bin2hex(random_bytes(16))`

**5-tab navigation:**
- Info Base | Media | Contenuto | Itinerario | Form Config
- localStorage key `edit_trip_tab` restores last active tab on reload
- Tab switching: hide all panels, show target, update active class

**Info Base tab:**
- Title input → `blur` triggers slug auto-generation (JS `generateSlug`) when slug is empty and not locked
- Slug input: readonly + lock badge when `$slug_locked`; slug-preview span shows live URL path
- Continent select (6 options), Status select (4 options)
- Start date + end date → JS auto-calculates duration: `Math.round((end-start)/86400000)+1 + " giorni"` → fills hidden duration input
- price_from (integer), commission_rate (float, step 0.5, admin-only label)
- Preview token box with truncated display and "Rigenera" button (calls `/admin/ajax.php`)
- Tags: all tags from `load_tags()` grouped by category as toggleable gold pill chips; custom tag entry creates new pill; `tags_json` hidden input keeps JSON synced

**Media tab:**
- Hero image URL input → `oninput` event sets `img#hero-preview` src; show/hide based on non-empty value
- Gallery URLs textarea (one per line) → `oninput` splits on `\n`, renders 4-column thumbnail grid; each thumb has remove button that syncs back to textarea

**Contenuto tab:**
- Short description textarea (maxlength=160) with live counter: "X / 160 caratteri rimanenti", turns red when < 20 remaining
- Full description textarea (8 rows, no limit)
- Included / Excluded: two side-by-side textareas (one item per line)

**Itinerario tab:**
- PHP renders existing `$trip['itinerary']` rows; each row is a `draggable="true"` div with drag handle, day number circle, title input, description textarea, up/down arrow buttons, trash button
- `initDrag()` binds dragstart/dragend/dragover/drop on all rows; `renumberItinerary()` updates day number badges
- `moveRow(btn, dir)` handles arrow button reordering; `removeRow(btn)` removes row
- `addItineraryRow()` appends blank row and calls `initDrag()` to bind drag events
- Form Config tab: placeholder with robot icon ("Coming soon")

**Sticky save footer:**
- Fixed bottom bar always visible above all content
- Left: trip title preview (or "Nuovo viaggio" for new)
- Right: Anteprima (opens preview URL in new tab, disabled if no token/slug), Salva Bozza (submit action=draft), Pubblica (submit action=publish, gold style)

## Decisions Made

1. **slug_locked computation** — `!$is_new && ($trip['published'] ?? false)` is computed once in PHP and passed to JS via `json_encode`. Once a trip is published, the slug input becomes `readonly` on every subsequent load (even if re-saved as draft) — this matches the Research Pitfall 5 rule.

2. **Itinerary builder in single commit** — Both Task 1 and Task 2 modify the same file (`admin/edit-trip.php`). The complete file including the itinerary tab and all JS was written atomically. The single commit `d6463ce` covers both tasks.

3. **form_config not overwritten** — The POST handler preserves `$trip['form_config'] ?? (object)[]` from the loaded trip data. The Form Config tab (Plan 06) will add its own form fields that write to this key.

4. **PRG (Post/Redirect/Get) pattern** — After a successful save, the handler issues `Location: /admin/edit-trip.php?slug=xxx&saved=1`. This prevents double-POST on browser refresh.

5. **Gallery preview driven by textarea** — Rather than maintaining a separate hidden array input, the gallery textarea is the single source of truth. JS parses it on every `oninput` event. The remove button on each thumbnail splices the URL out of the textarea value and removes the DOM element.

## Deviations from Plan

None — plan executed exactly as written.

## Self-Check

- [x] `admin/edit-trip.php` exists: YES
- [x] Contains `session_start`: YES
- [x] Contains `save_trips`: YES (2 matches)
- [x] Contains `slugLocked`: YES
- [x] Contains `hero_image`: YES
- [x] Contains `short_description`: YES
- [x] Contains `dragstart`: YES
- [x] Contains `renumberItinerary`: YES
- [x] Contains `addItineraryRow`: YES
- [x] Contains `itinerary_title`: YES
- [x] Contains `preview_token`: YES
- [x] Commit d6463ce exists in git log: YES

## Self-Check: PASSED
