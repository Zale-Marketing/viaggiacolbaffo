---
quick_task: 8
completed: "2026-03-09T10:47:15Z"
files_modified:
  - admin/edit-trip.php
  - viaggio.php
commits:
  - hash: 0c4d9ea
    message: "fix(quick-8): Bug 5A — Section F agency plain text input + hidden hash"
  - hash: f3e0946
    message: "fix(quick-8): Bugs 2-4 and 5B in viaggio.php"
---

# Quick Task 8: Fix 5 Bugs — Room Pills, B2B Grid, Bambini, WhatsApp, Agency Code

**One-liner:** Section F agency input switched to plain-text + hidden hash, B2B email/tel in 50/50 grid, bambini counter always visible, WhatsApp CTA text forced dark, agency code validated submit-time via crypto.subtle with re-dispatch.

## Bugs Fixed

### Bug 1 — Room pills DOMContentLoaded (admin/edit-trip.php)
**Status:** Already correct — no change needed.

The DOMContentLoaded handler at line 1630 already matched the exact spec (`classList.toggle('active', isActive)` + panel display toggle). The PHP `$fc_room_types = $fc['room_types'] ?? []` default is also correct. No modification required.

### Bug 5A — Section F agency inputs (admin/edit-trip.php)
**Change:** Replaced the two visible `form-group` divs (plain text + SHA-256 hash text) with:
- One visible `<input type="text" id="fc-agency-plain">` pre-filled with `'8823'` when saved hash matches the default or is empty
- One `<input type="hidden" id="fc-agency-hash">` carrying the persisted hash

The SHA-256 IIFE (reading `#fc-agency-plain`, writing to `#fc-agency-hash`) continues to work unchanged. The `saveFormConfig()` `val('fc-agency-hash','')` call continues to work because the hidden input still holds the hash.

### Bug 2 — B2B email + telefono grid layout (viaggio.php)
**Change:** Wrapped the two separate full-width `qf-field` divs (`emailAgenzia`, `telefonoAgenzia`) inside `<div class="qf-grid">`. The existing `.qf-grid` CSS already provides `grid-template-columns: 1fr 1fr` (50/50 on desktop, 1fr on mobile).

### Bug 3 — Bambini counter always visible (viaggio.php)
Three-part fix:
1. Changed `bambini-row` inline style from PHP conditional to `display:block`
2. Removed `if (!CONFIG.child_discounts_enabled) { childCount = 0; }` reset line
3. Added `if (!CONFIG.child_discounts_enabled) return;` early return at top of `rebuildChildAges()` so age inputs only render when enabled

### Bug 4 — WhatsApp CTA text illegible (viaggio.php)
**Change:** Added three CSS rules before `</style>`:
```css
.whatsapp-cta { color: #333 !important; }
.whatsapp-cta p { color: #333 !important; }
.whatsapp-cta a { color: #cc0031 !important; font-weight: 600; }
```

### Bug 5B — Agency code submit-time validation (viaggio.php)
Four-part fix:
1. Hoisted `var submitBtn = document.getElementById('qf-submit-btn')` to just after `errorDiv.style.display = 'none'` (before the `if (isAgency)` block) so it is in scope when the crypto.subtle promise resolves
2. Removed the duplicate `var submitBtn` declaration at its original location
3. Replaced the `if (!agencyUnlocked)` single-line error with a full crypto.subtle block: hashes the entered code, compares to `CONFIG.agency_code_hash`, sets `agencyUnlocked = true` and re-dispatches `submit` on match, or shows "Codice agenzia non valido." and re-enables button on mismatch
4. Deleted the now-dead `validateAgencyCode()` function, `bufToHex()` helper, and trailing comment

## Deviations from Plan

None — plan executed exactly as written.

## Self-Check

### Files exist
- admin/edit-trip.php: modified
- viaggio.php: modified

### Commits exist
- 0c4d9ea: fix(quick-8): Bug 5A — Section F agency plain text input + hidden hash
- f3e0946: fix(quick-8): Bugs 2-4 and 5B in viaggio.php

## Self-Check: PASSED
