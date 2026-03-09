---
phase: quick-9
plan: 01
type: execute
wave: 1
depends_on: []
files_modified:
  - viaggio.php
  - admin/edit-trip.php
autonomous: true
requirements: [QUICK-9]
must_haves:
  truths:
    - B2B/B2C toggle is centered horizontally in the form body
    - B2B fields layout: Nome Agenzia full-width, Codice+Email 50/50, Telefono+NomeCliente 50/50, checkbox+email client+guarantee below
    - Adulti and Bambini counters appear side by side in a 2-column grid
    - Child age inputs each have a label above them and are wider (130px)
    - Insurance checkbox appears BEFORE the price box
    - Admin toggle switch uses #000744 instead of var(--primary)
    - Admin bracket-row inputs and form-grid-2 have proper CSS styling
  artifacts:
    - path: viaggio.php
      provides: Quote form UI fixes (FIX 1-5)
    - path: admin/edit-trip.php
      provides: Admin CSS fixes (FIX 7-8)
  key_links:
    - from: viaggio.php insurance checkbox
      to: viaggio.php price box
      via: DOM order — insurance must come before price-box div
---

<objective>
Fix 7 UI and logic issues in viaggio.php (quote form) and admin/edit-trip.php (admin panel CSS).

Purpose: Improve form layout, UX, and visual consistency.
Output: viaggio.php with centered toggle, correct B2B layout, side-by-side counters, labeled child age inputs, insurance before price box. admin/edit-trip.php with correct toggle color and bracket input styles.
</objective>

<context>
@viaggio.php
@admin/edit-trip.php
</context>

<tasks>

<task type="auto">
  <name>Task 1: Fix viaggio.php quote form UI (FIX 1-5)</name>
  <files>viaggio.php</files>
  <action>
Apply these exact changes to viaggio.php in order:

**FIX 1 — Center the B2B/B2C toggle (lines 578-582):**

FIND (exact):
```
      <!-- B2B/B2C toggle -->
      <div class="qf-toggle-wrap">
        <button class="qf-toggle-btn active" type="button" data-type="agenzia" id="btn-agenzia">Agenzia</button>
        <button class="qf-toggle-btn" type="button" data-type="privato" id="btn-privato">Privato</button>
      </div>
```

REPLACE WITH:
```
      <!-- B2B/B2C toggle -->
      <div style="display:flex;justify-content:center;margin-bottom:24px;">
      <div class="qf-toggle-wrap" style="margin-bottom:0;">
        <button class="qf-toggle-btn active" type="button" data-type="agenzia" id="btn-agenzia">Agenzia</button>
        <button class="qf-toggle-btn" type="button" data-type="privato" id="btn-privato">Privato</button>
      </div>
      </div>
```

---

**FIX 2 — Replace entire #b2b-fields div content (lines 590-641):**

FIND (exact, from `<div id="b2b-fields">` through its closing `</div>` at line 641):
```
        <!-- B2B Fields -->
        <div id="b2b-fields">
          <div class="qf-section-title">Dati Agenzia</div>

          <!-- 1. Nome Agenzia -->
          <div class="qf-field">
            <label class="qf-label" for="nomeAgenzia">Nome Agenzia *</label>
            <input class="qf-input" type="text" id="nomeAgenzia" name="nome_agenzia" required>
          </div>

          <!-- 2. Codice Agenzia with SHA-256 validation -->
          <div class="qf-field">
            <label class="qf-label" for="codiceAgenzia">Codice Agenzia *</label>
            <input class="qf-input" type="password" id="codiceAgenzia" name="agency_code" autocomplete="off" placeholder="Inserisci il codice agenzia" required>
            <span class="qf-error-text" id="codiceAgenzia-error"></span>
            <div id="agency-code-feedback" style="font-size:13px;margin-top:4px;"></div>
          </div>

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

          <!-- 5. Nome Cliente Finale -->
          <div class="qf-field">
            <label class="qf-label" for="nomeCliente">Nome Cliente Finale *</label>
            <input class="qf-input" type="text" id="nomeCliente" name="nome_cliente_finale" required>
          </div>

          <!-- 6. Checkbox + conditional email -->
          <div class="qf-checkbox-group">
            <label>
              <input type="checkbox" id="inviaEmailCliente" name="invia_al_cliente" value="1" onclick="toggleClientEmail()">
              Invia preventivo anche al cliente
            </label>
          </div>
          <div id="emailClienteBox" class="qf-field" style="display:none;">
            <label class="qf-label" for="emailCliente">Email Cliente *</label>
            <input class="qf-input" type="email" id="emailCliente" name="email_cliente">
          </div>

          <!-- 7. Agency guarantee message -->
          <div style="padding:15px;background:#f8f9fa;border-left:4px solid #000744;border-radius:4px;font-size:13px;color:#555;margin-top:20px;">
            🛡️ <strong>Garanzia per le Agenzie:</strong> Non contatteremo mai direttamente il vostro cliente. Qualora in futuro il cliente decidesse di prenotare con noi senza passare dalla vostra agenzia, vi riconosceremo comunque la vostra commissione.
          </div>
        </div>
```

REPLACE WITH:
```
        <!-- B2B Fields -->
        <div id="b2b-fields">
          <div class="qf-section-title">Dati Agenzia</div>

          <!-- 1. Nome Agenzia — full width -->
          <div class="qf-field">
            <label class="qf-label" for="nomeAgenzia">Nome Agenzia *</label>
            <input class="qf-input" type="text" id="nomeAgenzia" name="nome_agenzia" required>
          </div>

          <!-- 2+3. Codice Agenzia + Email Agenzia (50/50) -->
          <div class="qf-grid">
            <div class="qf-field">
              <label class="qf-label" for="codiceAgenzia">Codice Agenzia *</label>
              <input class="qf-input" type="password" id="codiceAgenzia" name="agency_code" autocomplete="off" placeholder="Inserisci il codice agenzia" required>
              <span class="qf-error-text" id="codiceAgenzia-error"></span>
              <div id="agency-code-feedback" style="font-size:13px;margin-top:4px;"></div>
            </div>
            <div class="qf-field">
              <label class="qf-label" for="emailAgenzia">Email Agenzia *</label>
              <input class="qf-input" type="email" id="emailAgenzia" name="email_agenzia" required>
            </div>
          </div>

          <!-- 4+5. Telefono + Nome Cliente Finale (50/50) -->
          <div class="qf-grid" style="margin-top:16px;">
            <div class="qf-field">
              <label class="qf-label" for="telefonoAgenzia">Telefono *</label>
              <input class="qf-input" type="tel" id="telefonoAgenzia" name="telefono" required>
            </div>
            <div class="qf-field">
              <label class="qf-label" for="nomeCliente">Nome Cliente Finale *</label>
              <input class="qf-input" type="text" id="nomeCliente" name="nome_cliente_finale" required>
            </div>
          </div>

          <!-- 6. Checkbox + conditional email -->
          <div style="margin-top:16px;">
            <div class="qf-checkbox-group">
              <label>
                <input type="checkbox" id="inviaEmailCliente" name="invia_al_cliente" value="1" onclick="toggleClientEmail()">
                Invia preventivo anche al cliente
              </label>
            </div>
            <div id="emailClienteBox" class="qf-field" style="display:none;">
              <label class="qf-label" for="emailCliente">Email Cliente *</label>
              <input class="qf-input" type="email" id="emailCliente" name="email_cliente">
            </div>
          </div>

          <!-- 7. Agency guarantee message -->
          <div style="padding:15px;background:#f8f9fa;border-left:4px solid #000744;border-radius:4px;font-size:13px;color:#555;margin-top:16px;">
            🛡️ <strong>Garanzia per le Agenzie:</strong> Non contatteremo mai direttamente il vostro cliente. Qualora in futuro il cliente decidesse di prenotare con noi senza passare dalla vostra agenzia, vi riconosceremo comunque la vostra commissione.
          </div>
        </div>
```

---

**FIX 3 — Replace Composizione Gruppo section HTML (lines 668-691):**

FIND (exact):
```
        <!-- Group Composition -->
        <div class="qf-section" style="margin-top:20px;">
          <div class="qf-section-title">Composizione Gruppo</div>
          <div class="qf-field">
            <label class="qf-label">Adulti</label>
            <div class="qf-counter-wrap">
              <button class="qf-counter-btn" type="button" id="btn-adulti-dec">−</button>
              <span class="qf-counter-val" id="adulti-val">2</span>
              <button class="qf-counter-btn" type="button" id="btn-adulti-inc">+</button>
            </div>
            <input type="hidden" name="adulti" id="adulti-hidden" value="2">
          </div>
          <div class="qf-field" id="bambini-row" style="display:block;">
            <label class="qf-label">Bambini</label>
            <div class="qf-counter-wrap">
              <button class="qf-counter-btn" type="button" id="btn-bambini-dec">−</button>
              <span class="qf-counter-val" id="bambini-val">0</span>
              <button class="qf-counter-btn" type="button" id="btn-bambini-inc">+</button>
            </div>
            <input type="hidden" name="bambini" id="bambini-hidden" value="0">
            <div id="child-ages" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;"></div>
          </div>
          <div id="group-error" style="display:none;color:#cc0031;font-size:14px;margin-top:8px;font-weight:600;"></div>
        </div>
```

REPLACE WITH:
```
        <!-- Group Composition -->
        <div class="qf-section" style="margin-top:20px;">
          <div class="qf-section-title">Composizione Gruppo</div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
            <div class="qf-field">
              <label class="qf-label">Adulti</label>
              <div class="qf-counter-wrap">
                <button class="qf-counter-btn" type="button" id="btn-adulti-dec">−</button>
                <span class="qf-counter-val" id="adulti-val">2</span>
                <button class="qf-counter-btn" type="button" id="btn-adulti-inc">+</button>
              </div>
              <input type="hidden" name="adulti" id="adulti-hidden" value="2">
            </div>
            <div class="qf-field" id="bambini-row" style="display:block;">
              <label class="qf-label">Bambini</label>
              <div class="qf-counter-wrap">
                <button class="qf-counter-btn" type="button" id="btn-bambini-dec">−</button>
                <span class="qf-counter-val" id="bambini-val">0</span>
                <button class="qf-counter-btn" type="button" id="btn-bambini-inc">+</button>
              </div>
              <input type="hidden" name="bambini" id="bambini-hidden" value="0">
            </div>
          </div>
          <div id="child-ages" style="display:flex;flex-wrap:wrap;gap:10px;margin-top:16px;"></div>
          <div id="group-error" style="display:none;color:#cc0031;font-size:14px;margin-top:8px;font-weight:600;"></div>
        </div>
```

---

**FIX 4 — Replace rebuildChildAges() and update getChildAges() selector (lines 873-900):**

FIND (exact):
```
    // -- Child ages array --
    function getChildAges() {
      var ages = [];
      document.querySelectorAll('.qf-child-age-input').forEach(function(inp) {
        ages.push(inp.value !== '' ? parseInt(inp.value) : null);
      });
      return ages;
    }

    // -- Rebuild child age inputs --
    function rebuildChildAges() {
      if (!CONFIG.child_discounts_enabled) return;
      var container = document.getElementById('child-ages');
      if (!container) return;
      var existing = container.querySelectorAll('.qf-child-age-input');
      var oldVals = [];
      existing.forEach(function(inp){ oldVals.push(inp.value); });
      container.innerHTML = '';
      for (var i = 0; i < childCount; i++) {
        var inp = document.createElement('input');
        inp.type = 'number'; inp.min = 0; inp.max = 17;
        inp.className = 'qf-child-age-input';
        inp.placeholder = 'Età bambino ' + (i+1);
        inp.name = 'eta_bambini[]';
        if (oldVals[i] !== undefined) inp.value = oldVals[i];
        inp.addEventListener('input', updatePrice);
        container.appendChild(inp);
      }
    }
```

REPLACE WITH:
```
    // -- Child ages array --
    function getChildAges() {
      var ages = [];
      document.querySelectorAll('#child-ages .qf-child-age-input').forEach(function(inp) {
        ages.push(inp.value !== '' ? parseInt(inp.value) : null);
      });
      return ages;
    }

    // -- Rebuild child age inputs --
    function rebuildChildAges() {
      if (!CONFIG.child_discounts_enabled) return;
      var container = document.getElementById('child-ages');
      if (!container) return;
      var existing = container.querySelectorAll('.qf-child-age-input');
      var oldVals = [];
      existing.forEach(function(inp){ oldVals.push(inp.value); });
      container.innerHTML = '';
      for (var i = 0; i < childCount; i++) {
        var wrap = document.createElement('div');
        wrap.className = 'qf-child-age-wrap';
        wrap.style.cssText = 'display:flex;flex-direction:column;gap:4px;';
        var lbl = document.createElement('label');
        lbl.className = 'qf-label';
        lbl.textContent = 'Età bambino ' + (i+1) + ' *';
        var inp = document.createElement('input');
        inp.type = 'number'; inp.min = 0; inp.max = 17;
        inp.className = 'qf-input qf-child-age-input';
        inp.style.cssText = 'width:130px;padding:10px 14px;';
        inp.placeholder = 'Anni (0-17)';
        inp.name = 'eta_bambini[]';
        inp.required = true;
        if (oldVals[i] !== undefined) inp.value = oldVals[i];
        inp.addEventListener('input', updatePrice);
        wrap.appendChild(lbl);
        wrap.appendChild(inp);
        container.appendChild(wrap);
      }
    }
```

---

**FIX 5 — Move insurance block BEFORE price box (lines 693-714):**

The current order is:
1. `<!-- Price Box -->` div (lines 693-701)
2. `<!-- Insurance -->` PHP block (lines 703-714)

Swap them so insurance comes first.

FIND (exact):
```
        <!-- Price Box -->
        <div class="qf-price-box" id="price-box">
          <div id="price-lines"></div>
          <div class="qf-total-line">
            <span>TOTALE FINALE</span>
            <span id="pe-total">€0</span>
          </div>
          <div id="pe-savings" style="display:none;"></div>
        </div>

        <!-- Insurance -->
        <?php if (!empty($fc['insurance_enabled'])): ?>
        <div class="qf-checkbox-group">
          <label>
            <input type="checkbox" id="cb-assicurazione" name="assicurazione" value="1" onchange="updatePrice()">
            <span>
              <strong>Aggiungi Assicurazione Viaggio (+<?= (int)($fc['percentuale_assicurazione'] ?? 5) ?>%)</strong><br>
              <small>Protezione completa per il tuo viaggio</small>
            </span>
          </label>
        </div>
        <?php endif; ?>
```

REPLACE WITH:
```
        <!-- Insurance -->
        <?php if (!empty($fc['insurance_enabled'])): ?>
        <div class="qf-checkbox-group">
          <label>
            <input type="checkbox" id="cb-assicurazione" name="assicurazione" value="1" onchange="updatePrice()">
            <span>
              <strong>Aggiungi Assicurazione Viaggio (+<?= (int)($fc['percentuale_assicurazione'] ?? 5) ?>%)</strong><br>
              <small>Protezione completa per il tuo viaggio</small>
            </span>
          </label>
        </div>
        <?php endif; ?>

        <!-- Price Box -->
        <div class="qf-price-box" id="price-box">
          <div id="price-lines"></div>
          <div class="qf-total-line">
            <span>TOTALE FINALE</span>
            <span id="pe-total">€0</span>
          </div>
          <div id="pe-savings" style="display:none;"></div>
        </div>
```

**FIX 6 — Verify insurance fields in payload:**

The payload at lines 1186-1189 already contains `assicurazione_percentuale`, `costo_assicurazione`, and `assicurazione_inclusa`. No change needed.
  </action>
  <verify>
    Open viaggio.php for a trip with insurance_enabled=true and child_discounts_enabled=true.
    - Toggle is centered
    - B2B fields: NomeAgenzia full-width, then Codice+Email side by side, then Telefono+NomeCliente side by side
    - Adulti and Bambini counters appear in a 2-column grid side by side
    - Adding bambini shows labeled inputs with wider boxes
    - Insurance checkbox appears above (before) the price box
  </verify>
  <done>All 5 structural changes applied without syntax errors. PHP tags intact. JavaScript functions updated.</done>
</task>

<task type="auto">
  <name>Task 2: Fix admin/edit-trip.php CSS (FIX 7-8)</name>
  <files>admin/edit-trip.php</files>
  <action>
Apply these exact changes to admin/edit-trip.php:

**FIX 7 — Replace var(--primary) with #000744 in toggle CSS (line 1242):**

FIND (exact):
```
        .toggle-switch input:checked + .toggle-slider { background:var(--primary); }
```

REPLACE WITH:
```
        .toggle-switch input:checked + .toggle-slider { background:#000744; }
```

---

**FIX 8 — Add bracket-row input and form-grid-2 styles immediately after FIX 7 line (before the closing `</style>` tag at line 1244):**

FIND (exact):
```
        .toggle-switch input:checked + .toggle-slider { background:#000744; }
        .toggle-switch input:checked + .toggle-slider:before { transform:translateX(20px); }
        </style>
```

REPLACE WITH:
```
        .toggle-switch input:checked + .toggle-slider { background:#000744; }
        .toggle-switch input:checked + .toggle-slider:before { transform:translateX(20px); }
        .bracket-row input[type="number"] { padding: 7px 10px; border: 1px solid var(--border); border-radius: var(--radius); font-size: 13px; font-family: inherit; color: var(--text); background: var(--white); }
        .bracket-row input[type="number"]:focus { outline: none; border-color: var(--gold); box-shadow: 0 0 0 3px rgba(201,168,76,.15); }
        .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        </style>
```

Note: FIX 7 must be done first (changes `var(--primary)` to `#000744`), then FIX 8 finds the already-modified line. Do both in a single file write to avoid double-search confusion — apply FIX 7 and FIX 8 together as a single replacement block as shown above.
  </action>
  <verify>
    Visit admin panel, open any trip for editing, go to Form Config tab.
    - Toggle switches (child discounts, insurance, competitor) show #000744 blue when active (not the previous primary color).
    - Bracket-row number inputs have proper padding and border styling.
  </verify>
  <done>Line 1242 updated to #000744. Three new CSS rules added before closing style tag.</done>
</task>

</tasks>

<verification>
After both tasks complete:
1. `php -l viaggio.php` — no syntax errors
2. `php -l admin/edit-trip.php` — no syntax errors
3. Load viaggio.php in browser for a configured trip and verify all 5 layout fixes are visible
4. Load admin/edit-trip.php, Form Config tab — verify toggle color and bracket input styles
</verification>

<success_criteria>
- viaggio.php: toggle centered, B2B grid layout correct, counters side-by-side, child ages labeled+wider, insurance above price box
- admin/edit-trip.php: toggle active color is #000744, bracket inputs have padding/border, form-grid-2 utility class present
- No PHP syntax errors in either file
</success_criteria>

<output>
After completion, create `.planning/quick/9-fix-ui-counters-side-by-side-child-ages-/9-SUMMARY.md` with what was changed.
</output>
