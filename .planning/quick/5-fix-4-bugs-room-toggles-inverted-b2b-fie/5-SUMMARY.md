---
phase: quick-5
plan: 5
subsystem: viaggio.php, admin/edit-trip.php
tags: [bug-fix, b2b-form, css, counter, quote-form]
key-files:
  modified:
    - viaggio.php
decisions:
  - "admin/edit-trip.php toggleRoom and DOMContentLoaded were already correct — no changes made"
  - "B2B field IDs renamed to camelCase spec; old IDs replaced in all 3 JS reference sites (input listener, submit validator, payload builder)"
  - "updateButtonStates() placed before updatePrice() and called from 4 sites: 2 counter listeners, updatePrice() end, IIFE init"
metrics:
  duration: 8min
  completed: 2026-03-09
  tasks_completed: 2
  files_modified: 1
---

# Quick Task 5: Fix 4 Bugs — Room Toggles, B2B Fields, Checkbox CSS, Counter Limits

**One-liner:** Fixed B2B field IDs to spec, toggleClientEmail to use checkbox.checked, checkbox labels dark with CSS !important, and counter + buttons disabled at CONFIG-derived maxPersons with X1 unavailability hiding price-box.

## What Was Done

### Task 1: admin/edit-trip.php — Room Toggle Logic

After reading the current code, `toggleRoom()` and the `DOMContentLoaded` handler already exactly matched the spec:
- `toggleRoom()` correctly uses `willActivate = !btn.classList.contains('active')`, adds/removes class, shows/hides panel.
- `DOMContentLoaded` correctly uses `isActive ? 'block' : 'none'`.
- PHP inline `style="display:..."` on room panels already set correctly from `$fc_room_types`.

**No changes were required.** The bug reported was already fixed in a previous commit (b4ab6fd).

### Task 2: viaggio.php — 4 Targeted Fixes

**Fix A: B2B field IDs + required attributes**

All 5 B2B form inputs renamed to spec IDs with `required` added:
- `f-nome-agenzia` → `nomeAgenzia`
- `f-agency-code` → `codiceAgenzia`
- `f-email-agenzia` → `emailAgenzia`
- `f-telefono-b2b` → `telefonoAgenzia`
- `f-nome-cliente` → `nomeCliente`

Checkbox renamed `cb-send-cliente` → `inviaEmailCliente` with `onclick="toggleClientEmail()"` (no argument).
Wrapper div renamed `cliente-email-row` → `emailClienteBox`.
Email input renamed `f-email-cliente` → `emailCliente`.
Added `<span class="qf-error-text" id="codiceAgenzia-error"></span>` below agency code input.

All 3 JS sites that referenced old IDs updated:
1. Submit handler validation block
2. Payload builder
3. Agency code input event listener (`getElementById('codiceAgenzia')`)

**Fix B: toggleClientEmail() rewrite**

Replaced `toggleClientEmail(show)` (took boolean arg) with no-arg version that reads `checkbox.checked` directly and sets `emailInput.required = true/false` accordingly.

**Fix C: Checkbox label CSS**

Added color rules to existing `.qf-checkbox-group` CSS block:
```css
.qf-checkbox-group label { ... color: #333 !important; }
.qf-checkbox-group label strong { color: #000744 !important; }
.qf-checkbox-group label small { color: #666 !important; }
```

**Fix D: Counter button disabled enforcement + X1 unavailability**

Added `updateButtonStates()` function (placed before `updatePrice()`):
- Disables `btn-adulti-inc` when `adultCount >= maxPersons` or total at max
- Disables `btn-bambini-inc` when total at max

Called from:
1. `btn-adulti-inc` listener (after setCount)
2. `btn-adulti-dec` listener (after setCount)
3. `btn-bambini-inc` listener (after setCount)
4. `btn-bambini-dec` listener (after setCount)
5. End of `updatePrice()` (after savings block)
6. IIFE init (after `updatePrice()`)

X1 unavailability: when `n === 1 && CONFIG.room_types.indexOf('X1') === -1`, now also hides `#price-box`. Normal path restores `#price-box` display before `calcPricing()`.

## Commits

| Task | Commit | Description |
|------|--------|-------------|
| Task 1 | (no commit) | admin/edit-trip.php already correct, no changes |
| Task 2 | 769b304 | fix(quick-5): B2B field IDs, toggleClientEmail, checkbox CSS, counter limits |

## Deviations from Plan

**None for Task 2.**

**Task 1 deviation:** admin/edit-trip.php did not require any changes. The toggleRoom and DOMContentLoaded logic were already correct (fixed in commit b4ab6fd). This is documented as a finding, not a failure — the plan's "if already matches spec, no change needed" clause applied.

## Self-Check

- viaggio.php modified: confirmed (git shows 1 file changed, 65 insertions, 32 deletions)
- Commit 769b304: present in git log
- No old IDs remain: grep for all 8 old ID patterns returns no matches
