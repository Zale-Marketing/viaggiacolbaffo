---
phase: quick-6
plan: 6
subsystem: admin/quote-form
tags: [bugfix, room-pills, form-config, pricing, agency-validation]
dependency_graph:
  requires: []
  provides: [room-pills-correct-state, formconfig-save-redirect, toggleClientEmail-global-scope, agency-code-submit-only, adultCount-min-init, pricing-labels-occupancy, child-counter-hidden]
  affects: [admin/edit-trip.php, viaggio.php]
tech_stack:
  added: []
  patterns: [surgical-patch, JS-scope-fix, PHP-default-change]
key_files:
  modified:
    - admin/edit-trip.php
    - viaggio.php
decisions:
  - "Room pills default to [] (empty) so new trips show all pills inactive, consistent with no room types having been configured yet"
  - "DOMContentLoaded loop now covers X1-X5 and uses if(panel) guard instead of early return, so X2 pill gets toggled even without a panel element"
  - "saveFormConfig redirects to ?slug=...&saved=1&tab=formconfig instead of showing inline success message — ensures saved state is confirmed by fresh server render"
  - "adultCount init uses Math.min(2, maxPersons) so 1-person trips default to 1 adult not 2"
  - "Agency code input listener removed — validation only fires on form submit, not on every keystroke"
metrics:
  duration: 10min
  completed: 2026-03-09
  tasks_completed: 2
  files_modified: 2
---

# Quick Task 6: Fix 7 Bugs — Room Pills, UI Toggles, Client Email Summary

**One-liner:** Seven surgical fixes across admin room-pill state management and viaggio.php quote form logic (adultCount init, pricing labels, agency validation, toggleClientEmail scope).

## Tasks Completed

| Task | Name | Commit | Files |
|------|------|--------|-------|
| 1 | Fix admin/edit-trip.php — room pills default, DOMContentLoaded array, CSS, save redirect | ca5ead1 | admin/edit-trip.php |
| 2 | Fix viaggio.php — toggleClientEmail scope, agency validation, adultCount init, pricing labels, child counter | 4756396 | viaggio.php |

## Changes Made

### Task 1 — admin/edit-trip.php (5 changes)

**Change A:** `$fc_room_types` default changed from `['X1','X2','X3','X4']` to `[]` — new trips now start with all pills inactive.

**Change B:** JS `activeRoomTypes` fallback changed from `['X1','X2','X3','X4']` to `[]` — consistent with PHP default.

**Change C:** `DOMContentLoaded` loop array changed from `['X1','X3','X4','X5']` to `['X1','X2','X3','X4','X5']`. Removed early `if (!panel) return;` guard; replaced with `if (panel)` conditional so X2 pill gets its active class toggled even though it has no panel element.

**Change D:** `.room-pill` CSS updated to explicit values — `border: 2px solid #ccc`, `color: #666` (inactive), `.room-pill.active` uses `background: #000744; border-color: #000744; color: white` instead of CSS variables that may inherit wrong values.

**Change E:** `saveFormConfig()` success handler now does `window.location.href = window.location.pathname + '?slug=' + tripSlug + '&saved=1&tab=formconfig'` instead of showing a fading inline message.

### Task 2 — viaggio.php (5 changes)

**Change A:** `toggleClientEmail()` updated to concise form (`box.style.display = checkbox.checked ? 'block' : 'none'`), adds `emailInput.value = ''` on uncheck. Function confirmed outside IIFE — accessible from `onclick` attribute.

**Change B:** Removed `agencyInput.addEventListener('input', ...)` line. Agency code validation now only fires on form submit. Replaced with comment `// Agency code validation runs only on submit`.

**Change C:** Reordered declarations: `maxPersons` first, then `adultCount = Math.min(2, maxPersons)`, then `childCount = 0`. Added DOM sync (`adulti-val` textContent and `adulti-hidden` value) immediately after.

**Change D:** `else` branch in `calcPricing()` now computes `primiDue` ('2 adulti' when adultCount >= 2, else '1 adulto + 1 bambino') and uses it in the label: `'Camera doppia (' + primiDue + ') × ' + fmt(pb)`. Third bed label also context-aware: 'adulto' or 'bambino'.

**Change E:** After `var childCount = 0;` added `if (!CONFIG.child_discounts_enabled) { childCount = 0; }` — aligns JS state with bambini-row being hidden via PHP when child discounts are disabled.

## Deviations from Plan

None — plan executed exactly as written.

## Self-Check

Files exist:
- admin/edit-trip.php: modified (not new)
- viaggio.php: modified (not new)
- .planning/quick/6-fix-7-bugs-room-pills-ui-toggleclientema/6-SUMMARY.md: this file

Commits exist:
- ca5ead1: Task 1 — admin/edit-trip.php
- 4756396: Task 2 — viaggio.php

## Self-Check: PASSED
