---
phase: quick-11
plan: 01
subsystem: trip-cards, admin-tags
tags: [bug-fix, php, null-safety, json, admin]
dependency_graph:
  requires: []
  provides: [null-safe-date-rendering, complete-month-tags, correct-chip-id-routing]
  affects: [index.php, viaggi.php, data/tags.json, admin/tags.php]
tech_stack:
  added: []
  patterns: [null-coalescing, empty-check, str_replace-slug]
key_files:
  created: []
  modified:
    - index.php
    - viaggi.php
    - data/tags.json
    - admin/tags.php
decisions:
  - "Null-coalesce $trip['date_start'] and $trip['date_end'] to empty string before empty() check — consistent with existing fmt_date() pattern in viaggio.php"
  - "Reorder mese tags chronologically (jan-dec) in tags.json for operator readability"
  - "Use str_replace(' ', '-', $cat_key) in PHP and .replace(/ /g, '-') in JS — symmetric transformation ensures getElementById always finds the correct chip container"
metrics:
  duration: "5 minutes"
  completed: "2026-03-09"
  tasks_completed: 2
  files_modified: 4
---

# Quick Task 11: Fix 3 Issues — Date Null Checks, Missing Months, Admin Tag Routing

**One-liner:** Null-safe date rendering in trip cards (show "Date da definire"), 12 complete month tags in tags.json, and hyphenated chip container IDs in admin/tags.php so new tags route to the correct group section.

## Tasks Completed

| Task | Name | Commit | Files |
|------|------|--------|-------|
| 1 | Fix date null-safety in index.php and viaggi.php | fe9faa8 | index.php, viaggi.php |
| 2 | Add missing months to data/tags.json and fix admin/tags.php group select | acdbec5 | data/tags.json, admin/tags.php |

## What Was Done

### Task 1: Date Null-Safety

Both `index.php` and `viaggi.php` were calling `strtotime($trip['date_start'])` and `strtotime($trip['date_end'])` directly without null checks, triggering PHP "Undefined index" warnings for trips that have no dates set.

Fixed in `index.php` (lines 67-70): replaced inline `<?= date(...) ?>` calls with a null-coalesce + empty check block that outputs "Date da definire" when either date is absent.

Fixed in `viaggi.php` (line 217): replaced `(int) date('n', strtotime($trip['date_start']))` with a `$ds_raw` null-coalesce and a ternary that returns 0 when the date is empty. Also updated `data-date` attribute to use `$ds_raw`. Replaced the trip card dates block (lines 251-254) with the same null-safe pattern used in index.php.

### Task 2: Complete Month Tags + Admin Chip IDs

`data/tags.json` had only 5 of 12 months (aprile, maggio, giugno, settembre, ottobre). Added the 7 missing months (gennaio, febbraio, marzo, luglio, agosto, novembre, dicembre) in chronological order. All non-mese tags preserved unchanged.

`admin/tags.php` had a bug where chip container IDs like `id="chips-tipo viaggio"` contained spaces — invalid HTML IDs and unfindable by `getElementById`. Fixed by applying `str_replace(' ', '-', $cat_key)` in both PHP rendering loops (standard categories at line 196, non-standard loop at line 230). Fixed the JS add-tag callback to apply `.replace(/ /g, '-')` to the category string before the `getElementById('chips-' + cat)` lookup, making it symmetric with the PHP.

## Deviations from Plan

None — plan executed exactly as written.

## Verification

1. `grep -c "strtotime(\$trip\[" index.php viaggi.php` → 0 in both files
2. `grep -c '"category": "mese"' data/tags.json` → 12
3. `grep -c 'str_replace.*cat_key' admin/tags.php` → 2 (both PHP loops)
4. `grep -c "replace.*/ /g.*'-'" admin/tags.php` → 1 (JS callback)

## Self-Check: PASSED

- index.php modified: FOUND
- viaggi.php modified: FOUND
- data/tags.json: 12 mese entries confirmed
- admin/tags.php: str_replace in 2 PHP loops + JS replace confirmed
- Commit fe9faa8: FOUND
- Commit acdbec5: FOUND
