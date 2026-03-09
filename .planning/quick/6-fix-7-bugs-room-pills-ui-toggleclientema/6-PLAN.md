---
phase: quick-6
plan: 6
type: execute
wave: 1
depends_on: []
files_modified:
  - admin/edit-trip.php
  - viaggio.php
autonomous: true
must_haves:
  truths:
    - "Room pills in admin start all OFF on new trips, show saved state on existing trips"
    - "Saving Form Config redirects back to the formconfig tab confirming the save"
    - "toggleClientEmail works when called from onclick attribute in viaggio.php"
    - "Agency code validation fires only on form submit, not on keystroke"
    - "adultCount starts at min(2, maxPersons) so 1-person trips default to 1 adult"
    - "Pricing label correctly shows '1 adulto + 1 bambino' for mixed occupancy"
    - "Child counter row is hidden when child_discounts_enabled is false"
  artifacts:
    - path: admin/edit-trip.php
      provides: "Fixed room pills, save-redirect, CSS"
    - path: viaggio.php
      provides: "Fixed toggleClientEmail scope, agency validation, adultCount init, pricing labels, child counter init"
---

<objective>
Fix 7 bugs across admin/edit-trip.php and viaggio.php. No new features — targeted surgical edits only.

Purpose: These bugs make the quote form and admin form-config UI unreliable in everyday use.
Output: Both files corrected, all 7 bugs resolved.
</objective>

<execution_context>
@./.claude/get-shit-done/workflows/execute-plan.md
@./.claude/get-shit-done/templates/summary.md
</execution_context>

<context>
@.planning/STATE.md
</context>

<tasks>

<task type="auto">
  <name>Task 1: Fix admin/edit-trip.php — room pills default, DOMContentLoaded array, CSS, save redirect</name>
  <files>admin/edit-trip.php</files>
  <action>
Four changes in admin/edit-trip.php:

CHANGE A — PHP default for $fc_room_types (line ~1036):
Find:
  $fc_room_types = $fc['room_types'] ?? ['X1','X2','X3','X4'];
Replace with:
  $fc_room_types = $fc['room_types'] ?? [];

CHANGE B — JS activeRoomTypes const (line ~1609):
Find:
  let activeRoomTypes = <?= json_encode($fc_room_types ?? ['X1','X2','X3','X4']) ?>;
Replace with:
  let activeRoomTypes = <?= json_encode($fc_room_types ?? []) ?>;

CHANGE C — DOMContentLoaded loop array (line ~1629):
Find:
  ['X1','X3','X4','X5'].forEach(function(room) {
Replace with:
  ['X1','X2','X3','X4','X5'].forEach(function(room) {
(X2 has no panel element but its pill must get the active class from activeRoomTypes — the existing `if (!panel) return;` guard handles the missing panel safely. Remove that `if (!panel) return;` guard so the pill still gets toggled even without a panel. Revised loop body:)
  ['X1','X2','X3','X4','X5'].forEach(function(room) {
      const btn   = document.querySelector('.room-pill[data-room="' + room + '"]');
      const panel = document.getElementById('rp-' + room);
      const isActive = activeRoomTypes.includes(room);
      if (btn) { btn.classList.toggle('active', isActive); }
      if (panel) { panel.style.display = isActive ? 'block' : 'none'; }
  });

CHANGE D — .room-pill CSS (lines ~1217-1230):
Find the existing .room-pill and .room-pill.active rules:
  .room-pill {
    padding: 8px 18px;
    border: 2px solid var(--border);
    border-radius: 20px;
    background: white;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s;
  }
  .room-pill.active {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
  }
Replace with explicit values (removes reliance on CSS variables that may inherit incorrect colors):
  .room-pill {
    padding: 8px 18px;
    border: 2px solid #ccc;
    border-radius: 20px;
    background: white;
    color: #666;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s;
  }
  .room-pill.active {
    background: #000744;
    border-color: #000744;
    color: white;
  }

CHANGE E — saveFormConfig() success handler (lines ~1712-1716):
Find:
  .then(data => {
      if (data.success) {
          const msg = document.getElementById('save-fc-msg');
          msg.style.display = 'inline';
          setTimeout(() => msg.style.display = 'none', 2000);
      }
  });
Replace with:
  .then(data => {
      if (data.success) {
          window.location.href = window.location.pathname + '?slug=' + tripSlug + '&saved=1&tab=formconfig';
      }
  });
  </action>
  <verify>
    Open admin/edit-trip.php?new in browser:
    - All 5 room pills appear with white background (none active)
    Save a form config with X2 and X3 active, then reload:
    - X2 and X3 pills show dark navy background, others white
    - After clicking "Salva Configurazione Form", page reloads on formconfig tab
  </verify>
  <done>
    New trips show 0 active pills. Existing trips show saved pill state. X2 pill toggles visually. Save redirects to formconfig tab.
  </done>
</task>

<task type="auto">
  <name>Task 2: Fix viaggio.php — toggleClientEmail scope, agency validation, adultCount init, pricing labels, child counter</name>
  <files>viaggio.php</files>
  <action>
Five changes in viaggio.php. All changes are inside the `<?php if ($has_form): ?>` JS block.

CHANGE A — toggleClientEmail scope (lines ~829-841):
The function is already declared OUTSIDE the IIFE (it appears before `(function() {` at line ~842). Verify this is the case. If it is outside, no change needed for scope. However, the function body uses if/else instead of the concise form from the bug spec. Update the function to use the concise form AND ensure it is outside the IIFE:

Find:
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
Replace with (same logic, concise form, confirms it stays OUTSIDE the IIFE):
  function toggleClientEmail() {
    var checkbox = document.getElementById('inviaEmailCliente');
    var box = document.getElementById('emailClienteBox');
    var emailInput = document.getElementById('emailCliente');
    if (!checkbox || !box || !emailInput) return;
    box.style.display = checkbox.checked ? 'block' : 'none';
    emailInput.required = checkbox.checked;
    if (!checkbox.checked) emailInput.value = '';
  }
Confirm this definition is BEFORE the `(function() {` line. If there is a second definition inside the IIFE, remove the duplicate.

CHANGE B — Remove live agency code validation (lines ~1099-1100):
Find:
  var agencyInput = document.getElementById('codiceAgenzia');
  if (agencyInput) agencyInput.addEventListener('input', function(){ validateAgencyCode(agencyInput.value); });
Replace with (remove the addEventListener line entirely, keep the variable declaration only if it is used later; if not used elsewhere, remove both lines):
  // Agency code validation runs only on submit

CHANGE C — adultCount init (line ~843):
Find:
  var adultCount = 2;
Replace with:
  var adultCount = Math.min(2, maxPersons);
Note: maxPersons is declared on the next line. Move the maxPersons declaration ABOVE adultCount so Math.min(2, maxPersons) is valid:
  var maxPersons = Math.max.apply(null, CONFIG.room_types.map(function(r){ return parseInt(r.replace('X','')); }));
  var adultCount = Math.min(2, maxPersons);
  var childCount = 0;
Then add init sync immediately after these declarations (before isAgency):
  document.getElementById('adulti-val').textContent = adultCount;
  document.getElementById('adulti-hidden').value = adultCount;

CHANGE D — Pricing label for camera doppia (lines ~910-916 in calcPricing):
Find the `else` branch that currently reads:
  lines.push({label: adultCount + ' Adult' + (adultCount > 1 ? 'i' : 'o') + ' × ' + fmt(pb), value: pb * 2});
  subtotale += pb * 2;
  if (n >= 3) {
    var p3 = pb - CONFIG.sconto_terzo_letto;
    lines.push({label: '➕ 3° Letto (Adulto/Bambino)', value: p3});
    subtotale += p3;
  }
Replace with:
  var primiDue;
  if (adultCount >= 2) {
    primiDue = '2 adulti';
  } else {
    primiDue = '1 adulto + 1 bambino';
  }
  lines.push({label: 'Camera doppia (' + primiDue + ') × ' + fmt(pb), value: pb * 2});
  subtotale += pb * 2;
  if (n >= 3) {
    var p3 = pb - CONFIG.sconto_terzo_letto;
    var tipo3 = (adultCount >= 3) ? 'adulto' : 'bambino';
    lines.push({label: '➕ 3° letto (' + tipo3 + ')', value: p3});
    subtotale += p3;
  }

CHANGE E — Child counter init (inside the IIFE, after childCount declaration):
Find:
  var childCount = 0;
After this line (following the adultCount/maxPersons reorder from CHANGE C), add:
  if (!CONFIG.child_discounts_enabled) { childCount = 0; }
(This line is a no-op when child_discounts are enabled, and ensures childCount is 0 when they're not — the bambini-row is already hidden via PHP inline style when child_discounts_enabled is false, so this aligns the JS state.)
  </action>
  <verify>
    In viaggio.php quote form (trip with maxPersons=1):
    - Page loads with adulti counter showing 1 (not 2)
    In viaggio.php quote form (1 adulto + 1 bambino):
    - Price breakdown shows "Camera doppia (1 adulto + 1 bambino)"
    In viaggio.php quote form (agency B2B, codiceAgenzia field):
    - Typing in the agency code field does NOT trigger any validation or UI change
    - Submitting the form DOES trigger agency code validation
    In viaggio.php (trip without child_discounts_enabled):
    - Bambini counter row is hidden, childCount is 0 in JS
    Click "Invia preventivo anche al cliente" checkbox:
    - emailClienteBox appears/disappears correctly (toggleClientEmail scope works)
  </verify>
  <done>
    adultCount defaults to min(2, maxPersons). Pricing labels show correct occupancy description. Agency code field silent on input. Child counter hidden and zero when disabled. toggleClientEmail works from onclick attribute.
  </done>
</task>

</tasks>

<verification>
After both tasks complete:
- admin/edit-trip.php: room pills start white on new trips, saved state restored on reload, save redirects
- viaggio.php: all 5 JS fixes in place, no regressions in pricing or form submission
</verification>

<success_criteria>
All 7 bugs resolved as specified. No new JS errors introduced. Both files pass a manual smoke-test of the quote form and admin form-config tab.
</success_criteria>

<output>
After completion, create `.planning/quick/6-fix-7-bugs-room-pills-ui-toggleclientema/6-SUMMARY.md`
</output>
