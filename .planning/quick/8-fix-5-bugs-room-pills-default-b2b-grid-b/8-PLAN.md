---
quick_task: 8
type: execute
wave: 1
depends_on: []
files_modified:
  - admin/edit-trip.php
  - viaggio.php
autonomous: true
must_haves:
  truths:
    - "Room pills in admin Form Config tab show correct active state from saved data on page load (empty data = all inactive)"
    - "B2B emailAgenzia and telefonoAgenzia fields display side-by-side at 50% width each"
    - "Bambini counter is always visible regardless of child_discounts_enabled; child age inputs only render when enabled"
    - "WhatsApp CTA text and link are legible (dark text, red link)"
    - "Agency code validated on submit via crypto.subtle; admin Section F shows plain text input only (hash is hidden)"
  artifacts:
    - path: admin/edit-trip.php
      provides: "Fixed room pills init, fixed Section F agency plain/hidden inputs"
    - path: viaggio.php
      provides: "Fixed B2B grid, bambini row visibility, WhatsApp colors, submit-time agency code validation"
  key_links:
    - from: "admin/edit-trip.php DOMContentLoaded"
      to: "room-pill buttons and rp-* panels"
      via: "activeRoomTypes array from PHP json_encode"
    - from: "viaggio.php submit handler"
      to: "crypto.subtle.digest"
      via: "agencyCodeVal → hex comparison vs CONFIG.agency_code_hash"
---

<objective>
Fix 5 bugs across admin/edit-trip.php and viaggio.php as specified.

Purpose: Correct broken UX for room pill defaults, B2B layout, bambini counter, WhatsApp legibility, and agency code flow.
Output: Both files patched; all 5 bugs resolved.
</objective>

<context>
@.planning/STATE.md
</context>

<tasks>

<task type="auto">
  <name>Task 1: Fix admin/edit-trip.php — Bug 1 (room pills) + Bug 5A (Section F agency inputs)</name>
  <files>admin/edit-trip.php</files>
  <action>
**BUG 1 — Room pills all active by default**

The PHP variable `$fc_room_types` at line 1037 already reads `$fc['room_types'] ?? []` — this is CORRECT. Do NOT change it.

The DOMContentLoaded at line 1630 already has the correct loop. Verify it matches exactly:
```js
document.addEventListener('DOMContentLoaded', function() {
    ['X1','X2','X3','X4','X5'].forEach(function(room) {
        const btn   = document.querySelector('.room-pill[data-room="' + room + '"]');
        const panel = document.getElementById('rp-' + room);
        const isActive = activeRoomTypes.includes(room);
        if (btn) { btn.classList.toggle('active', isActive); }
        if (panel) { panel.style.display = isActive ? 'block' : 'none'; }
    });
```
If it matches, no change needed for Bug 1. If it differs, apply the exact code above.

**BUG 5A — Section F: show plain text input, make hash input type="hidden"**

Replace lines 1188–1203 (the SECTION F card) with:

```html
          <!-- SECTION F: Agency Code -->
          <div class="card" style="margin-bottom:1.5rem;">
            <h3 style="margin-bottom:0.75rem;">F — Codice Agenzia</h3>
            <div class="form-group">
              <label for="fc-agency-plain">Codice agenzia (testo)</label>
              <input type="text" id="fc-agency-plain" placeholder="es. 8823" autocomplete="off"
                     value="<?php
                       $saved_hash = $fc['agency_code_hash'] ?? '';
                       echo ($saved_hash === 'af97d1baebca1eaae1ce418c082402e60c2529ef719983ad7c8dda6ea1f8e8ee' || $saved_hash === '') ? '8823' : '';
                     ?>">
              <small>L'hash viene calcolato automaticamente al salvataggio</small>
            </div>
            <input type="hidden" id="fc-agency-hash"
                   value="<?= htmlspecialchars($fc['agency_code_hash'] ?? 'af97d1baebca1eaae1ce418c082402e60c2529ef719983ad7c8dda6ea1f8e8ee') ?>">
          </div>
```

The SHA-256 IIFE at lines 1659–1673 reads from `#fc-agency-plain` and writes to `#fc-agency-hash` — this continues to work unchanged because both element IDs are preserved.

The `saveFormConfig()` calls use `val('fc-agency-hash','').trim()` — this continues to work because the hidden input still holds the hash value.
  </action>
  <verify>
    Open admin/edit-trip.php for an existing trip that has no room_types saved. Confirm all 5 room pills appear inactive. Enter a code in the plain text field and confirm the hidden hash field updates (inspect element). Save and reload — hash persists.
  </verify>
  <done>Room pills reflect saved data (empty = all off). Section F shows one visible plain input + one hidden hash input. SHA-256 auto-calculation and save still work.</done>
</task>

<task type="auto">
  <name>Task 2: Fix viaggio.php — Bugs 2, 3, 4, 5B</name>
  <files>viaggio.php</files>
  <action>
**BUG 2 — B2B fields grid layout**

Lines 604–614 currently have emailAgenzia and telefonoAgenzia as separate full-width `qf-field` divs. Wrap them in a `<div class="qf-grid">` so they display 50/50. Replace:

```html
          <!-- 3. Email Agenzia -->
          <div class="qf-field">
            <label class="qf-label" for="emailAgenzia">Email Agenzia *</label>
            <input class="qf-input" type="email" id="emailAgenzia" name="email_agenzia" required>
          </div>

          <!-- 4. Telefono -->
          <div class="qf-field">
            <label class="qf-label" for="telefonoAgenzia">Telefono *</label>
            <input class="qf-input" type="tel" id="telefonoAgenzia" name="telefono" required>
          </div>
```

With:

```html
          <!-- 3+4. Email Agenzia + Telefono (grid 50/50) -->
          <div class="qf-grid">
            <div class="qf-field">
              <label class="qf-label" for="emailAgenzia">Email Agenzia *</label>
              <input class="qf-input" type="email" id="emailAgenzia" name="email_agenzia" required>
            </div>
            <div class="qf-field">
              <label class="qf-label" for="telefonoAgenzia">Telefono *</label>
              <input class="qf-input" type="tel" id="telefonoAgenzia" name="telefono" required>
            </div>
          </div>
```

**BUG 3 — Bambini counter always visible**

At line 677, change the `bambini-row` div's inline style from the PHP conditional to always `display:block`:

Replace:
```html
          <div class="qf-field" id="bambini-row" style="display:<?php echo !empty($fc['child_discounts_enabled']) ? 'block' : 'none'; ?>;">
```
With:
```html
          <div class="qf-field" id="bambini-row" style="display:block;">
```

At line 685, the `child-ages` div has no gate — leave it as-is:
```html
            <div id="child-ages" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;"></div>
```

In `rebuildChildAges()` at line 880, add an early return at the top (after the function declaration line, before the container lookup):

Replace:
```js
    function rebuildChildAges() {
      var container = document.getElementById('child-ages');
```
With:
```js
    function rebuildChildAges() {
      if (!CONFIG.child_discounts_enabled) return;
      var container = document.getElementById('child-ages');
```

At line 846, remove the childCount reset block:

Remove this line entirely:
```js
    if (!CONFIG.child_discounts_enabled) { childCount = 0; }
```

**BUG 4 — WhatsApp text white/illegible**

Add CSS rules before the closing `</style>` tag at line 563 (i.e., after line 562 `}`):

Replace:
```css
}
</style>
```
With:
```css
}
.whatsapp-cta { color: #333 !important; }
.whatsapp-cta p { color: #333 !important; }
.whatsapp-cta a { color: #cc0031 !important; font-weight: 600; }
</style>
```

**BUG 5B — Agency code: submit-time crypto.subtle validation, remove input/blur listeners**

The current `validateAgencyCode()` function (lines 1084–1107) runs on `input`/`blur`. The spec requires validation to happen only on submit, inline via `crypto.subtle`.

Step 1: Remove the `validateAgencyCode` function and the comment "Agency code validation runs only on submit" (lines 1084–1109). Delete lines 1084–1109 entirely.

Step 2: In the submit handler, the current check at line 1142 is:
```js
        if (!agencyUnlocked) { errorDiv.textContent = 'Inserisci un codice agenzia valido.'; errorDiv.style.display = 'block'; return; }
```

Replace the entire B2B validation block inside the submit handler. Find lines 1136–1150 (the `if (isAgency) { ... }` validation block) and replace with:

```js
      // Validate required fields by mode
      if (isAgency) {
        var nomeAg   = (document.getElementById('nomeAgenzia')||{}).value||'';
        var emailAg  = (document.getElementById('emailAgenzia')||{}).value||'';
        var telAg    = (document.getElementById('telefonoAgenzia')||{}).value||'';
        var nomeCliente = (document.getElementById('nomeCliente')||{}).value||'';
        var agencyCodeVal = (document.getElementById('codiceAgenzia')||{}).value||'';
        if (!nomeAg.trim()) { errorDiv.textContent = 'Inserisci il nome agenzia.'; errorDiv.style.display = 'block'; return; }
        if (!agencyUnlocked) {
          if (agencyCodeVal.trim()) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Verifica…';
            crypto.subtle.digest('SHA-256', new TextEncoder().encode(agencyCodeVal))
              .then(function(hashBuf) {
                var hex = Array.from(new Uint8Array(hashBuf)).map(function(b){ return b.toString(16).padStart(2,'0'); }).join('');
                if (hex === CONFIG.agency_code_hash) {
                  agencyUnlocked = true;
                  document.getElementById('quote-form').dispatchEvent(new Event('submit'));
                } else {
                  errorDiv.textContent = 'Codice agenzia non valido.';
                  errorDiv.style.display = 'block';
                  submitBtn.disabled = false;
                  submitBtn.textContent = 'Invia Richiesta di Preventivo';
                }
              });
            return;
          }
          errorDiv.textContent = 'Inserisci il codice agenzia.';
          errorDiv.style.display = 'block';
          return;
        }
        if (!emailAg.trim()) { errorDiv.textContent = 'Inserisci l\'email agenzia.'; errorDiv.style.display = 'block'; return; }
        if (!telAg.trim()) { errorDiv.textContent = 'Inserisci il telefono.'; errorDiv.style.display = 'block'; return; }
        if (!nomeCliente.trim()) { errorDiv.textContent = 'Inserisci il nome del cliente finale.'; errorDiv.style.display = 'block'; return; }
        var sendCl = document.getElementById('inviaEmailCliente');
        if (sendCl && sendCl.checked) {
          var emailCl = (document.getElementById('emailCliente')||{}).value||'';
          if (!emailCl.trim()) { errorDiv.textContent = 'Inserisci l\'email del cliente.'; errorDiv.style.display = 'block'; return; }
        }
      } else {
```

Note: The `submitBtn` variable is declared later in the original code (line 1168). Move the `var submitBtn` declaration to just before the `if (isAgency)` block so it is in scope when needed. Find:

```js
      var submitBtn = document.getElementById('qf-submit-btn');
      submitBtn.disabled = true;
      submitBtn.textContent = 'Invio in corso…';
```

And move/hoist the declaration `var submitBtn = document.getElementById('qf-submit-btn');` to immediately after `errorDiv.style.display = 'none';` (line 1133), so it reads:

```js
      var errorDiv = document.getElementById('form-error-msg');
      errorDiv.style.display = 'none';
      var submitBtn = document.getElementById('qf-submit-btn');
```

Then remove the duplicate `var submitBtn` line from its original position (keep only `submitBtn.disabled = true;` and `submitBtn.textContent = 'Invio in corso…';` there).

Also remove any remaining `input` or `blur` event listeners attached to `codiceAgenzia` if any exist outside the now-deleted `validateAgencyCode` block. Search for `codiceAgenzia` listeners — based on the grep, none exist outside the deleted block, so no further removal needed.
  </action>
  <verify>
    1. View viaggio.php for a trip with a form. Confirm emailAgenzia + telefonoAgenzia display side by side.
    2. Confirm bambini counter row is always visible; age inputs only appear when child_discounts_enabled=true in the trip config.
    3. Confirm WhatsApp CTA text is dark (#333) and the link is red (#cc0031).
    4. Enter a wrong agency code and submit — confirm inline error "Codice agenzia non valido." appears without page reload. Enter correct code and submit — confirm form submits.
  </verify>
  <done>All 4 viaggio.php bugs fixed: B2B fields in 50/50 grid, bambini counter always visible, WhatsApp text legible, agency code validated on submit via crypto.subtle with proper re-dispatch.</done>
</task>

</tasks>

<verification>
- admin/edit-trip.php: Section F has one visible text input (fc-agency-plain) and one hidden input (fc-agency-hash). SHA-256 auto-calc still updates hidden field on keystroke. Save persists hash.
- viaggio.php: B2B email+telefono fields side-by-side. Bambini counter visible regardless of config. WhatsApp text dark. Agency code: entering nothing shows "Inserisci il codice agenzia.", entering wrong code shows "Codice agenzia non valido.", entering correct code re-dispatches submit.
</verification>

<success_criteria>
All 5 bugs resolved with no regressions to existing functionality (room pill toggle, SHA-256 save, B2C form fields, pricing calculator, form submission webhook).
</success_criteria>

<output>
No SUMMARY file needed for quick tasks.
</output>
