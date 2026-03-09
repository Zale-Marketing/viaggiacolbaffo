---
phase: quick-7
plan: 01
subsystem: viaggio.php, admin/edit-trip.php
tags: [bugfix, quote-form, admin, room-types, form-config]
dependency_graph:
  requires: []
  provides: [room_types_empty_guard, toggleClientEmail_global_scope, submitWithFormConfig_form_save]
  affects: [viaggio.php, admin/edit-trip.php]
tech_stack:
  added: []
  patterns: [JS guard before render, global function scope, AJAX pre-save before form submit]
key_files:
  modified:
    - viaggio.php
    - admin/edit-trip.php
decisions:
  - room_types default changed to [] so empty config is detectable at runtime by the JS guard
  - submitWithFormConfig reads localStorage edit_trip_tab to decide AJAX vs direct submit path
  - toggleClientEmail placed before outer IIFE so onclick HTML attribute can reach it
metrics:
  duration: 8min
  completed: 2026-03-09T10:29:04Z
  tasks_completed: 2
  files_modified: 2
---

# Quick Task 7: Fix 3 Bugs — room_types default, Salva Bozza Form Config, toggleClientEmail scope

**One-liner:** Three surgical fixes: empty room_types guard with user-facing message, AJAX pre-save of form_config on Salva Bozza when form config tab is active, and toggleClientEmail hoisted to global scope.

## Tasks Completed

| Task | Description | Commit |
|------|-------------|--------|
| 1 | viaggio.php: room_types default [], JS guard, toggleClientEmail global scope | 1e914c7 |
| 2 | admin/edit-trip.php: Salva Bozza/Pubblica use submitWithFormConfig() | 69cb663 |

## Changes Made

### Task 1 — viaggio.php (3 edits)

**Edit A — room_types default**
Changed the fallback from `['X1','X2','X3','X4']` to `[]` so trips without configured room types produce an empty array detectable by the JS guard.

**Edit B — JS guard**
Added immediately after the CONFIG `};` block:
```javascript
if (!CONFIG.room_types || CONFIG.room_types.length === 0) {
  document.getElementById('quote-form-wrap').innerHTML =
    '<div class="qf-error">Il form non è ancora configurato. Contatta Lorenzo direttamente su WhatsApp.</div>';
}
```
This replaces the quote form with a clear message rather than crashing on an empty array.

**Edit C — toggleClientEmail global scope**
Removed `toggleClientEmail` from inside the IIFE (where `onclick="toggleClientEmail()"` on line 621 could not reach it) and placed it in global scope immediately after the `<script>` tag, before the outer IIFE at line 752.

### Task 2 — admin/edit-trip.php (3 edits)

**Edit A — Submit buttons**
Changed both sticky-footer buttons from `type="submit" name="action" value="..."` to `type="button" onclick="submitWithFormConfig('...')"` so the JS function controls submission.

**Edit B — Hidden action input**
Added `<input type="hidden" name="action" id="form-action-hidden" value="draft">` immediately after the `active_tab_field` hidden input, giving the form a proper action field that `submitWithFormConfig` sets before submitting.

**Edit C — submitWithFormConfig() function**
Added function before closing `</script>`. Logic:
- Sets `form-action-hidden` value to the requested action
- If `localStorage.edit_trip_tab !== 'formconfig'` — submits `edit-form` directly
- If active tab is `formconfig` — POSTs `save_form_config` via AJAX first, then submits `edit-form` on success, or shows an alert on failure

## Deviations from Plan

None — plan executed exactly as written.

## Verification Results

1. `grep "room_types.*?? \[\]" viaggio.php` — match found (line 433)
2. `grep "room_types.*length.*=== 0" viaggio.php` — match found (line 448)
3. `grep "function toggleClientEmail" viaggio.php` — exactly 1 match at line 743 (before IIFE at line 752)
4. `grep 'type="submit"' admin/edit-trip.php` — 0 matches
5. `grep "submitWithFormConfig" admin/edit-trip.php` — 3 matches (2 onclick, 1 function def)
6. `grep 'name="action".*id="form-action-hidden"' admin/edit-trip.php` — 1 match

## Self-Check: PASSED
