---
phase: quick-12
plan: 01
subsystem: admin, viaggio
tags: [bug-fix, admin, date-keys, itinerary, tabs, validation]
key-files:
  modified:
    - admin/edit-trip.php
    - viaggio.php
decisions:
  - "The new Accompagnatore/Volo/Hotel tab panels are inserted inside edit-container (before the <style> block) so they remain inside the form and get submitted with Salva Bozza / Pubblica"
  - "addHotelRow() uses hotel_colazione_new[] name for dynamically added rows to avoid colliding with existing hotel_colazione_N[] indexed names"
metrics:
  duration: ~8min
  completed: 2026-03-09
---

# Quick Task 12: Fix 8 bugs — date key mismatch, preserve accompagnatore/volo/hotel, itinerary fields, tabs, child age validation, NaN filter, bambini label

Fixed 8 bugs across admin/edit-trip.php (5 bugs) and viaggio.php (3 bugs): date_start/date_end key mismatches, fields wiped on save, missing itinerary sub-fields, missing admin tabs, child age validation before webhook call, NaN in eta_bambini payload, and hardcoded bambini age label.

## Tasks Completed

| Task | Name | Commit | Files |
|------|------|--------|-------|
| 1 | Fix admin/edit-trip.php — date keys, preserve fields, hotel key, itinerary fields, tabs | d590e41 | admin/edit-trip.php |
| 2 | Fix viaggio.php — child age validation, NaN filter, dynamic bambini label | d590e41 | viaggio.php |

## Changes Made

### admin/edit-trip.php

**BUG 1 — Date key mismatch (CRITICAL)**
- POST handler: `start_date`/`end_date` keys renamed to `date_start`/`date_end` in `$trip_data`
- HTML inputs: value attributes now read `$trip['date_start']` and `$trip['date_end']` (name= attributes unchanged)

**BUG 2 — Preserve accompagnatore/volo/hotel on save**
- Added `accompagnatore`, `volo`, `hotel` to `$trip_data` array (initially preserved from existing trip, then overwritten by BUG 5 Step F with new POST-parsed variables)

**BUG 3 — hotel vs hotels key in new trip defaults**
- Changed `'hotels' => []` to `'hotel' => []` in the `$is_new` default array

**BUG 4 — Itinerary loses location/date/image_url on save**
- POST handler: reads `itinerary_location[]`, `itinerary_date[]`, `itinerary_image[]` arrays and saves them per day
- HTML foreach: added 3 new inputs (location, date, image URL) between title and textarea
- `addItineraryRow()` JS: same 3 inputs added to the dynamic row template

**BUG 5 — Admin tabs Accompagnatore, Volo, Hotel missing**
- Tab nav: added 3 new `<button class="tab-btn">` buttons
- `validTabs` JS array: extended with `'accompagnatore'`, `'volo'`, `'hotel'`
- Added 3 full tab panels (Accompagnatore with nome/titolo/bio/foto/whatsapp/instagram, Volo with incluso checkbox + andata/ritorno detail grid, Hotel with dynamic rows)
- Added `addHotelRow()` JS function before `openPreview()`
- Added POST handlers for all three: `$new_accompagnatore`, `$new_volo`, `$new_hotel`
- `$trip_data` updated to use the new variables instead of preserving old values

### viaggio.php

**BUG 6 — Child age validation missing on submit**
- Inserted validation block before `if (!CONFIG.webhook_url)` check: blocks submit if `child_discounts_enabled && childCount > 0` but any age input is empty

**BUG 7 — NaN in eta_bambini payload**
- Added `&& !isNaN(a)` to the `.filter()` on `childAges`

**BUG 8 — Bambini label hardcoded**
- HTML: added `id="bambini-label"` to label and `id="bambini-label-age"` to `<small>`, changed static text to `(0–17 anni)`
- JS init: after `updateButtonStates()`, reads `CONFIG.child_discount_brackets` to compute real max age and updates the label span text

## Deviations from Plan

None — plan executed exactly as written. The task details spec and plan were fully aligned; all 8 bugs fixed per specification.

## Notes

- `git push` blocked by GitHub Push Protection due to a pre-existing Anthropic API key in commit `c14a9c1` (in `data/admin-config.json`). This is not related to this task's changes. The local commit `d590e41` is complete and correct.

## Self-Check

- [x] admin/edit-trip.php: BUG1 `date_start`/`date_end` in POST handler — DONE
- [x] admin/edit-trip.php: BUG1 HTML inputs read correct keys — DONE
- [x] admin/edit-trip.php: BUG2/F `$trip_data` uses `$new_accompagnatore`/`$new_volo`/`$new_hotel` — DONE
- [x] admin/edit-trip.php: BUG3 `hotel` key in new trip defaults — DONE
- [x] admin/edit-trip.php: BUG4 itinerary POST handler extended — DONE
- [x] admin/edit-trip.php: BUG4 itinerary HTML foreach has 3 new inputs — DONE
- [x] admin/edit-trip.php: BUG4 addItineraryRow() template has 3 new inputs — DONE
- [x] admin/edit-trip.php: BUG5 3 tab buttons added to nav — DONE
- [x] admin/edit-trip.php: BUG5 validTabs extended — DONE
- [x] admin/edit-trip.php: BUG5 3 tab panels added — DONE
- [x] admin/edit-trip.php: BUG5 addHotelRow() JS added — DONE
- [x] admin/edit-trip.php: BUG5 POST handler for accompagnatore/volo/hotel — DONE
- [x] viaggio.php: BUG6 child age validation before webhook check — DONE
- [x] viaggio.php: BUG7 NaN filter in eta_bambini — DONE
- [x] viaggio.php: BUG8 bambini label HTML id added, static text updated — DONE
- [x] viaggio.php: BUG8 dynamic label init JS block added — DONE
- [x] Commit d590e41 exists — DONE

## Self-Check: PASSED
