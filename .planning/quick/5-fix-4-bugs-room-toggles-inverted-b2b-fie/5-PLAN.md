---
phase: quick-5
plan: 5
type: execute
wave: 1
depends_on: []
files_modified:
  - admin/edit-trip.php
  - viaggio.php
autonomous: true
requirements: [QUICK-5]
must_haves:
  truths:
    - "Room type config panels in edit-trip.php are hidden on load unless the room type is saved in form_config"
    - "Clicking an inactive room pill shows its config panel; clicking an active pill hides it"
    - "B2B form in viaggio.php has all required fields with correct IDs (nomeAgenzia, codiceAgenzia, emailAgenzia, telefonoAgenzia, nomeCliente)"
    - "Checking 'Invia preventivo anche al cliente' shows #emailClienteBox and makes emailCliente required; unchecking hides it and removes required"
    - "Checkbox labels in the quote form are visible (dark text, not white)"
    - "Counter + buttons are disabled when adulti + bambini would exceed CONFIG-derived max; X1-only groups show error when X1 is not in CONFIG.room_types"
  artifacts:
    - path: "admin/edit-trip.php"
      provides: "Room toggle JS with correct show/hide logic"
    - path: "viaggio.php"
      provides: "B2B fields, toggleClientEmail fix, checkbox label CSS, counter enforcement"
  key_links:
    - from: "admin/edit-trip.php JS toggleRoom()"
      to: "rp-X1/rp-X3/rp-X4/rp-X5 panels"
      via: "panel.style.display = 'block'/'none'"
    - from: "viaggio.php #inviaEmailCliente"
      to: "#emailClienteBox"
      via: "toggleClientEmail() onclick"
    - from: "viaggio.php counter buttons"
      to: "maxPersone derived from CONFIG.room_types"
      via: "disabled attribute on + buttons"
---

<objective>
Fix 4 bugs across admin/edit-trip.php and viaggio.php:
1. Room type toggles inverted (admin)
2. B2B form missing fields / broken email toggle (quote form)
3. Checkbox labels invisible (white text on white)
4. Counter does not enforce room_type max from CONFIG

Purpose: Quote form and admin form config panel must work correctly for agency users and operators.
Output: Two files patched — admin/edit-trip.php and viaggio.php.
</objective>

<execution_context>
@./.claude/get-shit-done/workflows/execute-plan.md
</execution_context>

<context>
@.planning/STATE.md
</context>

<tasks>

<task type="auto">
  <name>Task 1: Fix room type toggle logic in admin/edit-trip.php</name>
  <files>admin/edit-trip.php</files>
  <action>
Locate the `toggleRoom` JS function (around line 1611) and the `DOMContentLoaded` handler below it (around line 1628). Verify — and rewrite if inverted — so that:

1. The `toggleRoom(btn)` function:
   - Reads `willActivate = !btn.classList.contains('active')`
   - If activating: adds 'active' class to btn, pushes room to activeRoomTypes, sorts, sets `panel.style.display = 'block'` (skip if panel is null — X2 has no panel)
   - If deactivating: removes 'active' class, filters room out of activeRoomTypes, sets `panel.style.display = 'none'`

2. The `DOMContentLoaded` handler iterates `['X1','X3','X4','X5']`, for each:
   - Gets the pill btn via `document.querySelector('.room-pill[data-room="' + room + '"]')`
   - Gets panel via `document.getElementById('rp-' + room)`
   - `isActive = activeRoomTypes.includes(room)`
   - Sets `btn.classList.toggle('active', isActive)`
   - Sets `panel.style.display = isActive ? 'block' : 'none'`

The PHP inline styles on the `.room-panel` divs (lines 1071, 1079, 1087, 1095) already correctly set initial display state from `$fc_room_types` — leave them as-is. The JS DOMContentLoaded handler must NOT invert this logic. If the current code already matches the above spec exactly, no change needed. If the condition is inverted anywhere (e.g., `!isActive` used where `isActive` should be, or `'none'` and `'block'` swapped in the handler), fix it.

The `activeRoomTypes` JS variable (line 1609) is initialized from PHP via `json_encode($fc_room_types)` — correct, leave as-is.
  </action>
  <verify>
    Open admin/edit-trip.php in browser (or inspect the JS source): confirm that for a trip whose form_config has room_types=['X2','X3'], on page load rp-X3 is visible and rp-X1/rp-X4/rp-X5 are hidden, and the X3 pill has class 'active'. Inspect source to verify the DOMContentLoaded logic sets display to `isActive ? 'block' : 'none'` (not inverted).
  </verify>
  <done>toggleRoom and DOMContentLoaded handler both show panel when isActive=true and hide when isActive=false. PHP initial inline styles preserved unchanged.</done>
</task>

<task type="auto">
  <name>Task 2: Fix B2B fields, toggleClientEmail, checkbox CSS, counter limits in viaggio.php</name>
  <files>viaggio.php</files>
  <action>
Make 4 targeted fixes in viaggio.php:

--- FIX A: B2B fields + email client toggle ---

The current #b2b-fields div (around line 581) already has fields 1-7 with different IDs than specified. The bug report specifies required IDs. Apply these changes:

1. Field 1 — Nome Agenzia: change `id="f-nome-agenzia"` to `id="nomeAgenzia"` and add `required`
2. Field 2 — Codice Agenzia: change `id="f-agency-code"` to `id="codiceAgenzia"` and add `required`. The feedback div below can stay with its current id. Also ensure there is an `<span class="qf-error-text" id="codiceAgenzia-error"></span>` error span below the input (add it if absent).
3. Field 3 — Email Agenzia: change `id="f-email-agenzia"` to `id="emailAgenzia"` and add `required`
4. Field 4 — Telefono: change `id="f-telefono-b2b"` to `id="telefonoAgenzia"` and add `required`
5. Field 5 — Nome Cliente Finale: change `id="f-nome-cliente"` to `id="nomeCliente"` and add `required`
6. Checkbox #6 — change `id="cb-send-cliente"` to `id="inviaEmailCliente"` and update its `onclick` to `onclick="toggleClientEmail()"` (no argument — the function will read checkbox.checked itself)
7. The conditional email wrapper: change its `id="cliente-email-row"` to `id="emailClienteBox"` and the email input inside from `id="f-email-cliente"` to `id="emailCliente"`. Keep `style="display:none"` on the wrapper div.

IMPORTANT: Any other JS in the file that references the OLD ids (f-nome-agenzia, f-agency-code, f-email-agenzia, f-telefono-b2b, f-nome-cliente, cb-send-cliente, cliente-email-row, f-email-cliente) must be updated to the new ids. Search for all occurrences and update them.

--- FIX B: toggleClientEmail() function ---

Replace the existing `toggleClientEmail(show)` function body (around line 826) with:

```javascript
function toggleClientEmail() {
  var checkbox = document.getElementById('inviaEmailCliente');
  var box = document.getElementById('emailClienteBox');
  var emailInput = document.getElementById('emailCliente');
  if (!checkbox || !box || !emailInput) return;
  if (checkbox.checked) {
    box.style.display = 'block';
    emailInput.required = true;
  } else {
    box.style.display = 'none';
    emailInput.required = false;
  }
}
```

--- FIX C: Checkbox label visibility ---

In the `<style>` block for the quote form (the block containing `.qf-checkbox-group`, around line 521), add these rules after the existing `.qf-checkbox-group` rules:

```css
.qf-checkbox-group { background: #f8f9fa; }
.qf-checkbox-group label { color: #333 !important; }
.qf-checkbox-group label strong { color: #000744 !important; }
.qf-checkbox-group label small { color: #666 !important; }
```

The `.qf-checkbox-group` background rule may already be in the block — if it exists, update it rather than duplicate.

--- FIX D: Counter button disabled enforcement ---

In the counter event listeners (around lines 1019-1032), update the `+` button click handlers to also set `disabled` on the button when at max, and remove `disabled` when below max. Specifically:

Replace the adulti-inc listener with:
```javascript
document.getElementById('btn-adulti-inc').addEventListener('click', function() {
  if (adultCount + childCount < maxPersons) {
    setCount('adulti', adultCount + 1);
  }
  updateButtonStates();
});
```

Replace the adulti-dec listener with:
```javascript
document.getElementById('btn-adulti-dec').addEventListener('click', function() {
  if (adultCount > 1) setCount('adulti', adultCount - 1);
  updateButtonStates();
});
```

Replace the bambini-inc and bambini-dec listeners similarly:
```javascript
if (bInc) bInc.addEventListener('click', function() {
  if (adultCount + childCount < maxPersons) setCount('bambini', childCount + 1);
  updateButtonStates();
});
if (bDec) bDec.addEventListener('click', function() {
  if (childCount > 0) setCount('bambini', childCount - 1);
  updateButtonStates();
});
```

Add the `updateButtonStates()` function immediately before `updatePrice()` (i.e., in the same IIFE, before the counter listeners):
```javascript
function updateButtonStates() {
  var total = adultCount + childCount;
  var adultIncBtn = document.getElementById('btn-adulti-inc');
  var bambiniIncBtn = document.getElementById('btn-bambini-inc');
  if (adultIncBtn) adultIncBtn.disabled = (adultCount >= maxPersons) || (total >= maxPersons);
  if (bambiniIncBtn) bambiniIncBtn.disabled = (total >= maxPersons);
}
```

Also call `updateButtonStates()` at the end of the IIFE initialization (right after the existing `updatePrice()` call on line 1034), and call it at the end of the `updatePrice()` function itself (after the savings block) to keep button states synced.

Additionally in `updatePrice()`, when the X1-not-available condition fires (line 952), also hide the price-box:
```javascript
if (n === 1 && CONFIG.room_types.indexOf('X1') === -1) {
  if (groupErr) { groupErr.textContent = 'La camera singola non è disponibile per questo viaggio.'; groupErr.style.display = 'block'; }
  if (submitBtn) submitBtn.disabled = true;
  var pb = document.getElementById('price-box');
  if (pb) pb.style.display = 'none';
  return;
}
```

And restore price-box display at the top of the "normal" path in `updatePrice()` (right before `var pricing = calcPricing()`):
```javascript
var priceBox = document.getElementById('price-box');
if (priceBox) priceBox.style.display = 'block';
```
  </action>
  <verify>
    1. Inspect HTML source of viaggio.php?slug=... — confirm id="nomeAgenzia", id="codiceAgenzia", id="emailAgenzia", id="telefonoAgenzia", id="nomeCliente", id="inviaEmailCliente", id="emailClienteBox", id="emailCliente" all present.
    2. Confirm toggleClientEmail() reads checkbox.checked and sets emailCliente.required accordingly.
    3. Confirm .qf-checkbox-group label has color:#333 in the style block.
    4. Confirm updateButtonStates() function exists and is called from counter listeners and updatePrice().
  </verify>
  <done>
    B2B fields have correct IDs with required attributes. toggleClientEmail() shows/hides emailClienteBox and sets required. Checkbox labels visible with dark text. Counter + buttons disabled when at max capacity derived from CONFIG.room_types.
  </done>
</task>

</tasks>

<verification>
- admin/edit-trip.php: Room panels respect form_config.room_types on load; clicking pills correctly toggles active class and panel visibility.
- viaggio.php: All 8 B2B field IDs match spec. Checkbox for "Invia preventivo al cliente" shows/hides emailClienteBox with required enforcement. Checkbox labels readable. Counter buttons go disabled at CONFIG-derived max.
</verification>

<success_criteria>
- Room toggle logic in admin: isActive pill = panel visible, inactive pill = panel hidden, on load and on click.
- B2B form fields have IDs: nomeAgenzia, codiceAgenzia, emailAgenzia, telefonoAgenzia, nomeCliente, inviaEmailCliente, emailClienteBox, emailCliente.
- toggleClientEmail() no longer takes a boolean argument — reads checkbox.checked directly, sets required on emailCliente.
- Quote form checkbox text is dark (#333), not white.
- adulti/bambini + buttons disabled when total = maxPersone. X1-unavailable error shown and price-box hidden when solo group with no X1.
</success_criteria>

<output>
After completion, create `.planning/quick/5-fix-4-bugs-room-toggles-inverted-b2b-fie/5-SUMMARY.md` with what was changed and any decisions made.
</output>
