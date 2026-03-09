---
phase: quick-10
plan: 01
type: execute
wave: 1
depends_on: []
files_modified: [viaggio.php]
autonomous: true
requirements: [QUICK-10]
must_haves:
  truths:
    - "Insurance checkbox triggers price recalculation when toggled"
    - "Child age labels show the max age from CONFIG brackets (e.g. 0-17)"
    - "Bambini counter label shows age range hint"
    - "B2C telefono field is required and validated before submit"
  artifacts:
    - path: "viaggio.php"
      provides: "All 4 bug fixes applied"
  key_links:
    - from: "#cb-assicurazione checkbox"
      to: "updatePrice()"
      via: "addEventListener inside IIFE"
    - from: "B2C submit handler"
      to: "#f-telefono"
      via: "validation check before fetch"
---

<objective>
Fix 4 bugs in viaggio.php: updatePrice not in global scope (insurance checkbox broken), child age label text missing max age from CONFIG, Bambini label missing age hint, B2C telefono not required/validated.

Purpose: Quote form works correctly — insurance pricing triggers on checkbox, age labels match CONFIG data, B2C form enforces phone.
Output: viaggio.php with all 4 bugs fixed.
</objective>

<execution_context>
@./.claude/get-shit-done/workflows/execute-plan.md
</execution_context>

<context>
@.planning/STATE.md
</context>

<tasks>

<task type="auto">
  <name>Task 1: Fix updatePrice global scope + insurance event listener + Bambini label</name>
  <files>viaggio.php</files>
  <action>
Make three targeted edits to viaggio.php:

EDIT A — Expose updatePrice to global scope.
Find the line (inside the inner quote-form IIFE, after the closing brace of updatePrice):

```
    updatePrice();
    updateButtonStates();
```

These two calls appear at around line 1095. BEFORE them (immediately after the closing brace of the updatePrice function definition, around line 1057), add:

```javascript
    window.updatePrice = updatePrice;
```

EDIT B — Remove inline onchange from insurance checkbox.
Find (around line 703):
```
            <input type="checkbox" id="cb-assicurazione" name="assicurazione" value="1" onchange="updatePrice()">
```
Replace with:
```
            <input type="checkbox" id="cb-assicurazione" name="assicurazione" value="1">
```

Then, AFTER the two lines `updatePrice(); updateButtonStates();` that initialize the form (around line 1095-1096), add the event listener block:

```javascript
    var cbAss2 = document.getElementById('cb-assicurazione');
    if (cbAss2) cbAss2.addEventListener('change', updatePrice);
```

EDIT C — Bambini counter label age hint.
Find (around line 686):
```html
              <label class="qf-label">Bambini</label>
```
Replace with:
```html
              <label class="qf-label">Bambini <small style="font-weight:400;color:#666;">(0–17 anni)</small></label>
```
  </action>
  <verify>grep -n "window.updatePrice" /c/Users/Zanni/viaggiacolbaffo/viaggio.php && grep -n "cbAss2" /c/Users/Zanni/viaggiacolbaffo/viaggio.php && grep -n "0–17 anni" /c/Users/Zanni/viaggiacolbaffo/viaggio.php</verify>
  <done>All three grep patterns return matches. Insurance checkbox has no onchange attribute (grep for "onchange=\"updatePrice\"" returns nothing).</done>
</task>

<task type="auto">
  <name>Task 2: Fix child age labels (dynamic maxBracket) + B2C telefono required + validation</name>
  <files>viaggio.php</files>
  <action>
Make two targeted edits to viaggio.php:

EDIT A — Child age label dynamic max age.
In rebuildChildAges(), find the line (around line 902):
```javascript
        lbl.textContent = 'Età bambino ' + (i+1) + ' *';
```
Replace it with:
```javascript
        var maxBracket = 17;
        if (CONFIG.child_discount_brackets && CONFIG.child_discount_brackets.length > 0) {
          maxBracket = Math.max.apply(null, CONFIG.child_discount_brackets.map(function(b){ return b.max_age; }));
        }
        lbl.textContent = 'Età bambino ' + (i+1) + ' * (0–' + maxBracket + ' anni per sconto)';
```

Also update the placeholder on the input (around line 907):
```javascript
        inp.placeholder = 'Anni (0-17)';
```
Replace with:
```javascript
        inp.placeholder = '0–' + maxBracket + ' anni';
```

Note: maxBracket is computed in the block above, so it is in scope when setting placeholder. Both lines are inside the same for-loop iteration.

EDIT B — B2C telefono required attribute.
Find (around line 667):
```html
              <input class="qf-input" type="tel" id="f-telefono" name="telefono">
```
Replace with:
```html
              <input class="qf-input" type="tel" id="f-telefono" name="telefono" required>
```

EDIT C — B2C submit validation: add telefono check.
In the submit handler B2C branch (around line 1162-1170), find:
```javascript
        var nome    = (document.getElementById('f-nome')||{}).value||'';
        var cognome = (document.getElementById('f-cognome')||{}).value||'';
        var email   = (document.getElementById('f-email')||{}).value||'';
        if (!nome.trim() || !cognome.trim() || !email.trim()) {
          errorDiv.textContent = 'Compila Nome, Cognome ed Email.';
          errorDiv.style.display = 'block';
          return;
        }
```
Replace with:
```javascript
        var nome    = (document.getElementById('f-nome')||{}).value||'';
        var cognome = (document.getElementById('f-cognome')||{}).value||'';
        var email   = (document.getElementById('f-email')||{}).value||'';
        var tel2    = (document.getElementById('f-telefono')||{}).value||'';
        if (!nome.trim() || !cognome.trim() || !email.trim()) {
          errorDiv.textContent = 'Compila Nome, Cognome ed Email.';
          errorDiv.style.display = 'block';
          return;
        }
        if (!tel2.trim()) {
          errorDiv.textContent = 'Inserisci il numero di telefono.';
          errorDiv.style.display = 'block';
          return;
        }
```

Note: payload.telefono for B2C is already present at line 1221 — no change needed there.

VERIFY FIX 5 (insurance lines — already correct, no change needed):
Lines ~1016-1032 already have: `var insurance = insChecked ? Math.round(subtotale * CONFIG.percentuale_assicurazione) : 0`, `var totale = subtotale + insurance`, subtotale row with border-top, conditional insurance row, and TOTALE uses fmt(totale). No edits required.

VERIFY FIX 6 (webhook payload — already complete, no change needed):
Payload already contains all required fields: tipo_cliente, nome_viaggio, numero_adulti, numero_bambini, eta_bambini, composizione_camera, prezzo_base_pp, supplemento_singola, sconto_letti_aggiuntivi, sconto_bambini, subtotale, assicurazione_percentuale, costo_assicurazione, totale_finale, assicurazione_inclusa, note, data_preventivo. Agency branch adds nome_agenzia, email_agenzia, telefono, nome_cliente_finale, invia_al_cliente, email_cliente. Privato branch adds nome, cognome, email, telefono. No edits required.
  </action>
  <verify>grep -n "maxBracket" /c/Users/Zanni/viaggiacolbaffo/viaggio.php && grep -n "f-telefono.*required" /c/Users/Zanni/viaggiacolbaffo/viaggio.php && grep -n "tel2" /c/Users/Zanni/viaggiacolbaffo/viaggio.php</verify>
  <done>maxBracket computed in rebuildChildAges, f-telefono has required attribute, tel2 variable present in B2C submit branch.</done>
</task>

</tasks>

<verification>
After both tasks:
- grep -c "window.updatePrice" viaggio.php returns 1
- grep -c "onchange=\"updatePrice" viaggio.php returns 0 (inline handler removed)
- grep -c "cbAss2" viaggio.php returns 1
- grep -c "maxBracket" viaggio.php returns at least 2 (declaration + use in placeholder)
- grep -c "0–17 anni" viaggio.php returns 1 (Bambini label)
- grep -c "required" viaggio.php includes f-telefono line
- grep -c "tel2" viaggio.php returns 1 (B2C validation)
</verification>

<success_criteria>
- Insurance checkbox fires updatePrice via event listener (not broken onchange)
- Child age labels read max bracket from CONFIG, not hardcoded
- Bambini counter label shows "(0–17 anni)" hint
- B2C form blocks submit when telefono is empty, shows error message
- FIX 5 (insurance summary) and FIX 6 (payload) confirmed already correct — no regression
</success_criteria>

<output>
After completion, create `.planning/quick/10-fix-viaggio-php-updateprice-global-scope/10-SUMMARY.md` with what was changed, line numbers affected, and confirmation that FIX 5 and FIX 6 were verified correct with no changes needed.
</output>
