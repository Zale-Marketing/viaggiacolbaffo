---
phase: quick-7
plan: 01
type: execute
wave: 1
depends_on: []
files_modified:
  - viaggio.php
  - admin/edit-trip.php
autonomous: true
requirements: []
must_haves:
  truths:
    - "A trip with empty room_types shows the unconfigured message instead of a broken form"
    - "Clicking Salva Bozza while on the Form Config tab saves form_config before submitting"
    - "The Invia preventivo anche al cliente checkbox shows/hides email field correctly"
  artifacts:
    - path: viaggio.php
      provides: "Fixed room_types default, JS guard, global toggleClientEmail"
    - path: admin/edit-trip.php
      provides: "submitWithFormConfig() wires hidden action input + optional AJAX pre-save"
  key_links:
    - from: "viaggio.php CONFIG block"
      to: "quote-form-wrap"
      via: "JS guard after CONFIG definition"
      pattern: "room_types.*length.*0"
    - from: "admin/edit-trip.php sticky footer buttons"
      to: "edit-form"
      via: "submitWithFormConfig() + hidden #form-action-hidden"
      pattern: "submitWithFormConfig"
---

<objective>
Fix 3 bugs across viaggio.php and admin/edit-trip.php.

Purpose: Prevent broken quote form on trips without configured room types; prevent Salva Bozza from silently discarding Form Config changes; fix toggleClientEmail scope error on agency form.
Output: Both files patched, all three bugs resolved.
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
  <name>Task 1: Fix viaggio.php — room_types default + JS guard + toggleClientEmail scope</name>
  <files>viaggio.php</files>
  <action>
Three edits to viaggio.php:

EDIT A — Fix room_types default (line 433):
Change:
  room_types: <?= json_encode($fc['room_types'] ?? ['X1','X2','X3','X4']) ?>,
To:
  room_types: <?= json_encode($fc['room_types'] ?? []) ?>,

EDIT B — Add JS guard immediately after the closing `};` of the CONFIG block (after line 447, before the closing `</script>` tag at line 448):
After the line:  webhook_url: "<?= htmlspecialchars($fc['webhook_url'] ?? '') ?>"
and the closing `};` of CONFIG, add before `</script>`:
```
if (!CONFIG.room_types || CONFIG.room_types.length === 0) {
  document.getElementById('quote-form-wrap').innerHTML =
    '<div class="qf-error">Il form non è ancora configurato. Contatta Lorenzo direttamente su WhatsApp.</div>';
}
```

EDIT C — Move toggleClientEmail to global scope:
The function is currently defined at line 829 inside `<?php if ($has_form): ?>` inside the outer IIFE (which starts at line 739 with `(function () {`). This makes it unavailable to the `onclick="toggleClientEmail()"` attribute at line 621.

Remove the function definition from lines 829-837 (inside the IIFE under `<?php if ($has_form): ?>`):
```javascript
  function toggleClientEmail() {
    var checkbox = document.getElementById('inviaEmailCliente');
    var box = document.getElementById('emailClienteBox');
    var emailInput = document.getElementById('emailCliente');
    if (!checkbox || !box || !emailInput) return;
    box.style.display = checkbox.checked ? 'block' : 'none';
    emailInput.required = checkbox.checked;
    if (!checkbox.checked) emailInput.value = '';
  }
```

Add it in global scope BEFORE the outer IIFE opening `(function () {` at line 739:
```javascript
function toggleClientEmail() {
  var checkbox = document.getElementById('inviaEmailCliente');
  var box = document.getElementById('emailClienteBox');
  var emailInput = document.getElementById('emailCliente');
  if (!checkbox || !box || !emailInput) return;
  box.style.display = checkbox.checked ? 'block' : 'none';
  emailInput.required = checkbox.checked;
  if (!checkbox.checked) emailInput.value = '';
}
```
The `<script>` tag at line 738 opens the script block; place the function right after that tag, then the outer IIFE follows.
  </action>
  <verify>
Search viaggio.php for the correct strings:
- `grep "room_types.*?? \[\]" viaggio.php` returns a match
- `grep "room_types.*length.*=== 0" viaggio.php` returns a match
- `grep "function toggleClientEmail" viaggio.php` returns exactly 1 match
- That match is BEFORE `(function ()` (global scope), not inside the IIFE
  </verify>
  <done>
- room_types default is `[]` not `['X1','X2','X3','X4']`
- JS guard replaces quote-form-wrap contents when room_types is empty
- toggleClientEmail is defined once in global scope before the IIFE
  </done>
</task>

<task type="auto">
  <name>Task 2: Fix admin/edit-trip.php — Salva Bozza/Pubblica use submitWithFormConfig</name>
  <files>admin/edit-trip.php</files>
  <action>
Three edits to admin/edit-trip.php:

EDIT A — Replace the two submit buttons in the sticky footer (lines 1257-1262).
Current:
```html
            <button type="submit" name="action" value="draft" class="btn-draft">
                <i class="fa-regular fa-floppy-disk"></i> Salva Bozza
            </button>
            <button type="submit" name="action" value="publish" class="btn-publish">
                <i class="fa-solid fa-rocket"></i> Pubblica
            </button>
```
Replace with:
```html
            <button type="button" class="btn-draft" onclick="submitWithFormConfig('draft')">
                <i class="fa-regular fa-floppy-disk"></i> Salva Bozza
            </button>
            <button type="button" class="btn-publish" onclick="submitWithFormConfig('publish')">
                <i class="fa-solid fa-rocket"></i> Pubblica
            </button>
```

EDIT B — Add hidden input for action inside the `<form>` tag block. The form opens at line 783. Add immediately after `<input type="hidden" id="active_tab_field" name="active_tab" value="">` (line 784):
```html
    <input type="hidden" name="action" id="form-action-hidden" value="draft">
```

EDIT C — Add the submitWithFormConfig() JavaScript function. Place it in the `<script>` block at the bottom of the file, just before the closing `</script>` tag (after line 1737, before `</script>`):

```javascript
// ─────────────────────────────────────────────────────────────────────────────
// Submit with optional form config pre-save
// ─────────────────────────────────────────────────────────────────────────────
function submitWithFormConfig(action) {
    document.getElementById('form-action-hidden').value = action;
    var activeTab = 'info';
    try { activeTab = localStorage.getItem('edit_trip_tab') || 'info'; } catch(e) {}
    if (activeTab !== 'formconfig') {
        document.getElementById('edit-form').submit();
        return;
    }
    // Active tab is formconfig: AJAX save first, then submit
    var brackets = [];
    document.querySelectorAll('#fc-brackets-list .bracket-row').forEach(function(row) {
        var min  = parseInt(row.querySelector('.br-min').value);
        var max  = parseInt(row.querySelector('.br-max').value);
        var disc = parseInt(row.querySelector('.br-discount').value);
        if (!isNaN(min) && !isNaN(max) && !isNaN(disc)) brackets.push({min_age:min,max_age:max,discount:disc});
    });
    var val = function(id, fallback) { var el = document.getElementById(id); return el ? el.value : fallback; };
    var checked = function(id) { var el = document.getElementById(id); return el && el.checked ? '1' : '0'; };
    fetch(window.location.href, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            action:                         'save_form_config',
            slug:                           tripSlug,
            webhook_url:                    val('fc-webhook','').trim(),
            prezzo_base_persona:            val('fc-prezzo-base',0),
            room_types:                     JSON.stringify(activeRoomTypes),
            supplemento_singola:            val('fc-suppl-singola',0),
            sconto_terzo_letto:             val('fc-sconto-3',0),
            sconto_quarto_letto:            val('fc-sconto-4',0),
            sconto_quinto_letto:            val('fc-sconto-5',0),
            child_discounts_enabled:        checked('fc-child-enabled'),
            child_discount_brackets:        JSON.stringify(brackets),
            insurance_enabled:              checked('fc-insurance-enabled'),
            percentuale_assicurazione:      val('fc-assicurazione',5),
            competitor_enabled:             checked('fc-competitor-enabled'),
            prezzo_concorrenza_persona:     val('fc-concorrenza-pp',0),
            prezzo_concorrenza_letti_extra: val('fc-concorrenza-extra',0),
            agency_code_hash:               val('fc-agency-hash','').trim(),
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            document.getElementById('edit-form').submit();
        } else {
            alert('Errore nel salvataggio del Form Config. Riprova.');
        }
    })
    .catch(function() {
        alert('Errore di rete durante il salvataggio del Form Config.');
    });
}
```
  </action>
  <verify>
Search admin/edit-trip.php for correct strings:
- `grep "submitWithFormConfig" admin/edit-trip.php` returns 3 matches (2 onclick attrs + 1 function definition)
- `grep 'name="action"' admin/edit-trip.php` returns exactly 1 match (the hidden input, NOT on a button)
- `grep 'form-action-hidden' admin/edit-trip.php` returns 2 matches (hidden input + JS function reference)
- `grep 'type="submit"' admin/edit-trip.php` returns 0 matches (no more submit buttons)
  </verify>
  <done>
- Salva Bozza and Pubblica are type="button" with onclick="submitWithFormConfig(...)"
- Hidden #form-action-hidden carries the action value
- When active tab is formconfig, AJAX saves form_config first then submits edit-form
- When active tab is anything else, edit-form submits directly
  </done>
</task>

</tasks>

<verification>
After both tasks:
1. `grep "room_types.*?? \[\]" viaggio.php` matches
2. `grep "room_types.*length.*=== 0" viaggio.php` matches
3. `grep "function toggleClientEmail" viaggio.php` returns 1 match, located before `(function ()` (global scope)
4. `grep 'type="submit"' admin/edit-trip.php` returns 0 matches
5. `grep "submitWithFormConfig" admin/edit-trip.php` returns 3 matches
6. `grep 'name="action".*id="form-action-hidden"' admin/edit-trip.php` returns 1 match
</verification>

<success_criteria>
- viaggio.php: empty room_types shows unconfigured message, not broken JS
- viaggio.php: toggleClientEmail works when checkbox clicked (global scope)
- admin/edit-trip.php: Salva Bozza from Form Config tab persists form_config before page save
- admin/edit-trip.php: Salva Bozza from any other tab submits directly without extra AJAX
</success_criteria>

<output>
After completion, create `.planning/quick/7-fix-3-bugs-room-types-default-empty-subm/7-SUMMARY.md` following the summary template.
</output>
