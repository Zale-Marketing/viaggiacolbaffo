---
phase: quick-10
plan: 01
subsystem: viaggio.php
tags: [bugfix, quote-form, insurance, child-ages, validation, b2c]
dependency_graph:
  requires: []
  provides: [insurance-checkbox-pricing, child-age-labels, bambini-hint, b2c-telefono-required]
  affects: [viaggio.php]
tech_stack:
  added: []
  patterns: [vanilla-js-IIFE, CONFIG-driven-labels, event-listener-over-inline-handler]
key_files:
  created: []
  modified: [viaggio.php]
decisions:
  - "window.updatePrice assigned inside IIFE after function definition — exposes to global scope for external callers without polluting global on load"
  - "Insurance change handled via addEventListener('change') not inline onchange — consistent with existing JS event pattern in file"
  - "maxBracket computed from CONFIG.child_discount_brackets every time rebuildChildAges runs — stays in sync if admin changes brackets"
  - "tel2 check placed after nome/cognome/email block not replacing it — plan constraint honoured"
metrics:
  duration: 72s
  completed: 2026-03-09
  tasks_completed: 2
  files_modified: 1
---

# Quick Task 10: Fix viaggio.php updatePrice Global Scope — Summary

**One-liner:** Four targeted fixes — insurance checkbox wired via event listener (not broken inline handler), child age labels read maxBracket from CONFIG brackets, Bambini counter shows age hint, B2C telefono validated before submit.

## Tasks Completed

| Task | Name | Commit | Lines affected |
|------|------|--------|---------------|
| 1 | Fix updatePrice global scope + insurance event listener + Bambini label | 7189028 | 703, 686, 1057-1058, 1095-1099 |
| 2 | Child age labels (dynamic maxBracket) + B2C telefono required + validation | 5fa4d2c | 666-667, 902-911, 1163-1182 |

## Changes Made

### FIX 1 — updatePrice global scope (line 1058)

Added `window.updatePrice = updatePrice;` immediately after the closing brace of the `updatePrice` function definition, inside the quote-form IIFE. This exposes the function for any external callers (e.g., admin test buttons).

### FIX 2 — Insurance checkbox event listener (lines 703, 1098-1099)

- Removed `onchange="updatePrice()"` from the `#cb-assicurazione` input (broken because `updatePrice` was not in global scope at parse time).
- Added `var cbAss2 = document.getElementById('cb-assicurazione'); if (cbAss2) cbAss2.addEventListener('change', updatePrice);` after `updatePrice(); updateButtonStates();` init calls, inside the IIFE.

### FIX 3 — Bambini counter age hint (line 686)

Updated the Bambini label to:
```html
<label class="qf-label">Bambini <small style="font-weight:400;color:#666;">(0–17 anni)</small></label>
```

### FIX 4 — Child age label dynamic max age (lines 902-911)

In `rebuildChildAges()`, replaced hardcoded `'Età bambino N *'` with:
- `maxBracket` computed from `CONFIG.child_discount_brackets` (falls back to 17 if empty)
- Label: `'Età bambino N * (0–{maxBracket} anni per sconto)'`
- Placeholder: `'0–{maxBracket} anni'`

### FIX 5 — B2C telefono required (lines 666-667)

- Label updated to `Telefono *`
- Input gets `required` attribute

### FIX 6 — B2C telefono validation in submit handler (lines 1173-1182)

Added `var tel2` read and validation block AFTER the nome/cognome/email check (not replacing it):
```javascript
var tel2 = (document.getElementById('f-telefono')||{}).value||'';
if (!tel2.trim()) {
  errorDiv.textContent = 'Inserisci il numero di telefono.';
  errorDiv.style.display = 'block';
  return;
}
```

## Verified Already Correct — No Changes Needed

**FIX 5 (insurance summary lines ~1016-1032):** Confirmed present:
- `var insurance = insChecked ? Math.round(subtotale * CONFIG.percentuale_assicurazione) : 0`
- `var totale = subtotale + insurance`
- Subtotal row with border-top divider
- Conditional insurance row when `insChecked`
- TOTALE uses `fmt(totale)`

**FIX 6 (webhook payload ~1190-1221):** Confirmed all required fields present:
- Common fields: `tipo_cliente`, `nome_viaggio`, `numero_adulti`, `numero_bambini`, `eta_bambini`, `composizione_camera`, `prezzo_base_pp`, `supplemento_singola`, `sconto_letti_aggiuntivi`, `sconto_bambini`, `subtotale`, `assicurazione_percentuale`, `costo_assicurazione`, `totale_finale`, `assicurazione_inclusa`, `note`, `data_preventivo`
- Agency branch: `nome_agenzia`, `email_agenzia`, `telefono`, `nome_cliente_finale`, `invia_al_cliente`, `email_cliente`
- Privato branch: `nome`, `cognome`, `email`, `telefono` (line 1221)

No edits required for either.

## Deviations from Plan

**1. [Rule 2 - Missing Critical Functionality] Added Telefono * label update**

- **Found during:** Task 2
- **Issue:** Plan specified adding `required` to the input but the label still read `Telefono` (no asterisk), which would mislead users about the field being required
- **Fix:** Updated label from `Telefono` to `Telefono *` — cosmetic correctness, not a structural change
- **Files modified:** viaggio.php line 666

## Self-Check

Checking key claims...

## Self-Check: PASSED

- viaggio.php: FOUND
- SUMMARY.md: FOUND
- commit 7189028: FOUND
- commit 5fa4d2c: FOUND
