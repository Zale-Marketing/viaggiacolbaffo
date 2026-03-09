---
phase: quick-12
plan: 01
type: execute
wave: 1
depends_on: []
files_modified:
  - admin/edit-trip.php
  - viaggio.php
autonomous: true
requirements: [QUICK-12]

must_haves:
  truths:
    - "Admin saves date_start/date_end with correct JSON keys and HTML values load from correct keys"
    - "Saving a trip preserves accompagnatore, volo, hotel fields (not wiped)"
    - "New trip defaults use 'hotel' key (not 'hotels')"
    - "Itinerary rows save and restore location, date, image_url fields"
    - "Admin tabs include Accompagnatore, Volo, Hotel with working panels"
    - "viaggio.php submit validates child ages when child_discounts_enabled before webhook check"
    - "eta_bambini payload filters out null AND NaN values"
    - "Bambini label shows dynamic max age from CONFIG brackets, not hardcoded '0–7 anni'"
  artifacts:
    - path: admin/edit-trip.php
      provides: "Fixed POST handler, HTML inputs, tab nav, tab panels, itinerary fields, addItineraryRow JS"
    - path: viaggio.php
      provides: "Fixed bambini label id, dynamic label init, child age validation, NaN filter"
  key_links:
    - from: "admin/edit-trip.php POST $trip_data"
      to: "trips.json"
      via: "save_trips()"
      pattern: "date_start.*date_end.*accompagnatore.*volo.*hotel"
    - from: "viaggio.php bambini-label-age span"
      to: "CONFIG.child_discount_brackets"
      via: "JS init after updatePrice()/updateButtonStates()"
      pattern: "bambini-label-age"
---

<objective>
Fix 8 bugs across admin/edit-trip.php and viaggio.php: date key mismatches, fields wiped on save, missing itinerary fields, missing admin tabs, and three viaggio.php issues (child age validation, NaN filter, dynamic bambini label).

Purpose: Admin can correctly save and load all trip data; quote form validates child ages and shows correct age range.
Output: Corrected admin/edit-trip.php and viaggio.php.
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
  <name>Task 1: Fix admin/edit-trip.php — date keys, preserve fields, hotel key, itinerary fields, tabs</name>
  <files>admin/edit-trip.php</files>
  <action>
Make ALL of the following changes in admin/edit-trip.php:

**BUG 1 — Date key mismatch in $trip_data (lines ~200-201):**
Change:
  'start_date' => $_POST['start_date'] ?? '',
  'end_date'   => $_POST['end_date']   ?? '',
To:
  'date_start' => $_POST['start_date'] ?? '',
  'date_end'   => $_POST['end_date']   ?? '',

NOTE: The HTML inputs keep `id="start_date"` and `name="start_date"` (the form field names stay the same). Only the array keys in $trip_data change. Also fix the HTML value attributes (lines ~855-859):
  value="<?= htmlspecialchars($trip['start_date'] ?? '') ?>"  → value="<?= htmlspecialchars($trip['date_start'] ?? '') ?>"
  value="<?= htmlspecialchars($trip['end_date']   ?? '') ?>"  → value="<?= htmlspecialchars($trip['date_end']   ?? '') ?>"

**BUG 2 — accompagnatore/volo/hotel wiped on save:**
After the 'form_config' line in $trip_data (~line 212), add:
  'accompagnatore' => $trip['accompagnatore'] ?? null,
  'volo'           => $trip['volo'] ?? null,
  'hotel'          => $trip['hotel'] ?? [],

**BUG 3 — hotel vs hotels key mismatch in new trip default (~line 46):**
Change:
  'hotels' => [],
To:
  'hotel'  => [],

**BUG 4 — Itinerary loses location, image_url, date on save:**

In the POST handler itinerary section (around line 158-172), read the extra POST arrays and save them:
```php
$itin_titles    = $_POST['itinerary_title']     ?? [];
$itin_descs     = $_POST['itinerary_desc']       ?? [];
$itin_locations = $_POST['itinerary_location']   ?? [];
$itin_dates     = $_POST['itinerary_date']       ?? [];
$itin_images    = $_POST['itinerary_image']      ?? [];
$itinerary = [];
foreach ($itin_titles as $i => $title_val) {
    $title_val    = trim($title_val);
    $desc_val     = trim($itin_descs[$i] ?? '');
    $location_val = trim($itin_locations[$i] ?? '');
    $date_val     = trim($itin_dates[$i] ?? '');
    $image_val    = trim($itin_images[$i] ?? '');
    if ($title_val !== '' || $desc_val !== '') {
        $itinerary[] = [
            'day'         => $i + 1,
            'title'       => $title_val,
            'description' => $desc_val,
            'location'    => $location_val,
            'date'        => $date_val,
            'image_url'   => $image_val,
        ];
    }
}
```

In the PHP itinerary HTML foreach (the existing rows, around lines 1006-1023), add three inputs INSIDE .itinerary-fields div, after the textarea:
```html
<input type="text" name="itinerary_location[]"
    value="<?= htmlspecialchars($day['location'] ?? '') ?>"
    placeholder="Località (es. Kyoto)">
<input type="date" name="itinerary_date[]"
    value="<?= htmlspecialchars($day['date'] ?? '') ?>">
<input type="url" name="itinerary_image[]"
    value="<?= htmlspecialchars($day['image_url'] ?? '') ?>"
    placeholder="URL immagine giorno">
```

In addItineraryRow() JS (~line 1543), add the same three inputs to the .itinerary-fields div template:
```javascript
<input type="text" name="itinerary_location[]" placeholder="Località (es. Kyoto)">
<input type="date" name="itinerary_date[]">
<input type="url" name="itinerary_image[]" placeholder="URL immagine giorno">
```
Place them after the `<textarea name="itinerary_desc[]"...></textarea>` in the template string.

**BUG 5 — Admin tabs Accompagnatore, Volo, Hotel missing:**

In the tab nav (around line 803-809), add three new tab buttons AFTER the formconfig button:
```html
<button type="button" class="tab-btn" data-tab="accompagnatore">Accompagnatore</button>
<button type="button" class="tab-btn" data-tab="volo">Volo</button>
<button type="button" class="tab-btn" data-tab="hotel">Hotel</button>
```

Update the validTabs JS array (~line 1301) from:
  const validTabs = ['info', 'media', 'content', 'itinerario', 'formconfig'];
To:
  const validTabs = ['info', 'media', 'content', 'itinerario', 'formconfig', 'accompagnatore', 'volo', 'hotel'];

After `</div><!-- /tab-formconfig -->` and BEFORE `</div><!-- /edit-container -->`, add three new tab panels:

```html
<!-- TAB: Accompagnatore -->
<div class="tab-panel" id="tab-accompagnatore">
    <h3 class="section-title">Accompagnatore</h3>
    <?php $acc = $trip['accompagnatore'] ?? null; ?>
    <div class="form-grid" style="margin-bottom:16px;">
        <div class="form-group">
            <label>Nome</label>
            <input type="text" name="acc_nome" value="<?= htmlspecialchars($acc['nome'] ?? '') ?>" placeholder="Es. Lorenzo">
        </div>
        <div class="form-group">
            <label>Ruolo</label>
            <input type="text" name="acc_ruolo" value="<?= htmlspecialchars($acc['ruolo'] ?? '') ?>" placeholder="Es. Tour Leader">
        </div>
    </div>
    <div class="form-group" style="margin-bottom:16px;">
        <label>Bio</label>
        <textarea name="acc_bio" rows="4" placeholder="Breve biografia..."><?= htmlspecialchars($acc['bio'] ?? '') ?></textarea>
    </div>
    <div class="form-group" style="margin-bottom:16px;">
        <label>URL foto</label>
        <input type="url" name="acc_foto" value="<?= htmlspecialchars($acc['foto'] ?? '') ?>" placeholder="https://...">
    </div>
    <p class="field-hint">Questi dati sono salvati con il pulsante "Salva Bozza" / "Pubblica" in basso.</p>
</div><!-- /tab-accompagnatore -->

<!-- TAB: Volo -->
<div class="tab-panel" id="tab-volo">
    <h3 class="section-title">Dettagli Volo</h3>
    <?php $volo = $trip['volo'] ?? null; ?>
    <div class="form-grid" style="margin-bottom:16px;">
        <div class="form-group">
            <label>Compagnia</label>
            <input type="text" name="volo_compagnia" value="<?= htmlspecialchars($volo['compagnia'] ?? '') ?>" placeholder="Es. Qatar Airways">
        </div>
        <div class="form-group">
            <label>Numero volo</label>
            <input type="text" name="volo_numero" value="<?= htmlspecialchars($volo['numero'] ?? '') ?>" placeholder="Es. QR123">
        </div>
    </div>
    <div class="form-grid" style="margin-bottom:16px;">
        <div class="form-group">
            <label>Partenza (aeroporto)</label>
            <input type="text" name="volo_partenza" value="<?= htmlspecialchars($volo['partenza'] ?? '') ?>" placeholder="Es. MXP">
        </div>
        <div class="form-group">
            <label>Arrivo (aeroporto)</label>
            <input type="text" name="volo_arrivo" value="<?= htmlspecialchars($volo['arrivo'] ?? '') ?>" placeholder="Es. NRT">
        </div>
    </div>
    <div class="form-group" style="margin-bottom:16px;">
        <label>Note volo</label>
        <textarea name="volo_note" rows="3" placeholder="Es. Scalo a Doha 2h"><?= htmlspecialchars($volo['note'] ?? '') ?></textarea>
    </div>
    <p class="field-hint">Questi dati sono salvati con il pulsante "Salva Bozza" / "Pubblica" in basso.</p>
</div><!-- /tab-volo -->

<!-- TAB: Hotel -->
<div class="tab-panel" id="tab-hotel">
    <h3 class="section-title">Alloggi</h3>
    <div id="hotel-rows">
        <?php foreach ($trip['hotel'] ?? [] as $hi => $h): ?>
        <div class="itinerary-row">
            <span class="drag-handle"><i class="fa-solid fa-grip-vertical"></i></span>
            <span class="day-num"><?= $hi + 1 ?></span>
            <div class="itinerary-fields">
                <input type="text" name="hotel_nome[]" value="<?= htmlspecialchars($h['nome'] ?? '') ?>" placeholder="Nome hotel">
                <input type="text" name="hotel_localita[]" value="<?= htmlspecialchars($h['localita'] ?? '') ?>" placeholder="Località">
                <input type="url" name="hotel_immagine[]" value="<?= htmlspecialchars($h['immagine'] ?? '') ?>" placeholder="URL immagine">
                <input type="text" name="hotel_stelle[]" value="<?= htmlspecialchars($h['stelle'] ?? '') ?>" placeholder="Stelle (es. 4★)">
                <textarea name="hotel_descrizione[]" rows="2" placeholder="Descrizione breve"><?= htmlspecialchars($h['descrizione'] ?? '') ?></textarea>
            </div>
            <div class="itinerary-actions">
                <button type="button" class="btn-icon btn-danger-icon" onclick="removeRow(this)" title="Elimina"><i class="fa-solid fa-trash"></i></button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="btn-small" onclick="addHotelRow()" style="margin-top:12px; padding:8px 16px;">
        <i class="fa-solid fa-plus"></i> Aggiungi Hotel
    </button>
    <p class="field-hint" style="margin-top:12px;">Questi dati sono salvati con il pulsante "Salva Bozza" / "Pubblica" in basso.</p>
</div><!-- /tab-hotel -->
```

Also read the hotel fields in the POST handler. After the itinerary block (before building $trip_data), add:
```php
// Hotel
$hotel_nomi     = $_POST['hotel_nome']        ?? [];
$hotel_localita = $_POST['hotel_localita']    ?? [];
$hotel_immagini = $_POST['hotel_immagine']    ?? [];
$hotel_stelle   = $_POST['hotel_stelle']      ?? [];
$hotel_descs    = $_POST['hotel_descrizione'] ?? [];
$hotel_arr      = [];
foreach ($hotel_nomi as $hi => $hn) {
    $hn = trim($hn);
    if ($hn !== '') {
        $hotel_arr[] = [
            'nome'        => $hn,
            'localita'    => trim($hotel_localita[$hi] ?? ''),
            'immagine'    => trim($hotel_immagini[$hi] ?? ''),
            'stelle'      => trim($hotel_stelle[$hi]   ?? ''),
            'descrizione' => trim($hotel_descs[$hi]    ?? ''),
        ];
    }
}
```

And in $trip_data, change `'hotel' => $trip['hotel'] ?? []` (the preserved field from BUG 2 fix) to `'hotel' => $hotel_arr` so the submitted values are saved instead of always preserving old data.

Also read accompagnatore and volo from POST (add after the hotel reading block):
```php
// Accompagnatore from POST
$acc_nome  = trim($_POST['acc_nome']  ?? '');
$acc_ruolo = trim($_POST['acc_ruolo'] ?? '');
$acc_bio   = trim($_POST['acc_bio']   ?? '');
$acc_foto  = trim($_POST['acc_foto']  ?? '');
$accompagnatore_data = ($acc_nome !== '')
    ? ['nome' => $acc_nome, 'ruolo' => $acc_ruolo, 'bio' => $acc_bio, 'foto' => $acc_foto]
    : ($trip['accompagnatore'] ?? null);

// Volo from POST
$volo_compagnia = trim($_POST['volo_compagnia'] ?? '');
$volo_numero    = trim($_POST['volo_numero']    ?? '');
$volo_partenza  = trim($_POST['volo_partenza']  ?? '');
$volo_arrivo    = trim($_POST['volo_arrivo']    ?? '');
$volo_note      = trim($_POST['volo_note']      ?? '');
$volo_data = ($volo_compagnia !== '' || $volo_numero !== '')
    ? ['compagnia' => $volo_compagnia, 'numero' => $volo_numero, 'partenza' => $volo_partenza, 'arrivo' => $volo_arrivo, 'note' => $volo_note]
    : ($trip['volo'] ?? null);
```

Then in $trip_data change:
  'accompagnatore' => $trip['accompagnatore'] ?? null,
  'volo'           => $trip['volo'] ?? null,
  'hotel'          => $hotel_arr,
To:
  'accompagnatore' => $accompagnatore_data,
  'volo'           => $volo_data,
  'hotel'          => $hotel_arr,

Add addHotelRow() JS function BEFORE the openPreview() function:
```javascript
function addHotelRow() {
    const container = document.getElementById('hotel-rows');
    const n = container.querySelectorAll('.itinerary-row').length + 1;
    const div = document.createElement('div');
    div.className = 'itinerary-row';
    div.innerHTML = `
        <span class="drag-handle"><i class="fa-solid fa-grip-vertical"></i></span>
        <span class="day-num">${n}</span>
        <div class="itinerary-fields">
            <input type="text" name="hotel_nome[]" placeholder="Nome hotel">
            <input type="text" name="hotel_localita[]" placeholder="Località">
            <input type="url" name="hotel_immagine[]" placeholder="URL immagine">
            <input type="text" name="hotel_stelle[]" placeholder="Stelle (es. 4★)">
            <textarea name="hotel_descrizione[]" rows="2" placeholder="Descrizione breve"></textarea>
        </div>
        <div class="itinerary-actions">
            <button type="button" class="btn-icon btn-danger-icon" onclick="removeRow(this)" title="Elimina"><i class="fa-solid fa-trash"></i></button>
        </div>`;
    container.appendChild(div);
}
```
  </action>
  <verify>
    Open admin/edit-trip.php in browser for an existing trip. Confirm:
    - Date fields populate correctly (date_start/date_end render in HTML inputs)
    - Tab nav shows Accompagnatore, Volo, Hotel tabs and panels render
    - Itinerary rows show location, date, image URL inputs
    - Save a trip and reload: confirm dates, accompagnatore, volo, hotel, itinerary extra fields persist in trips.json
    Manual JSON check: grep "date_start\|accompagnatore\|hotel" data/trips.json to verify correct keys
  </verify>
  <done>
    - trips.json uses date_start/date_end (not start_date/end_date)
    - accompagnatore, volo, hotel preserved (not null/empty) after save
    - New trip defaults use 'hotel' key
    - Itinerary entries include location, date, image_url
    - Three new tabs visible and panels render in admin
  </done>
</task>

<task type="auto">
  <name>Task 2: Fix viaggio.php — child age validation, NaN filter, dynamic bambini label</name>
  <files>viaggio.php</files>
  <action>
Make ALL of the following changes in viaggio.php:

**BUG 6 — Child age validation missing before webhook check:**

In the submit handler, AFTER the B2C telephone validation block (the closing brace of the `} else {` block at around line 1184) and BEFORE the `if (!CONFIG.webhook_url)` check (line 1186), insert:

```javascript
      // Validate child ages when child discounts enabled
      if (CONFIG.child_discounts_enabled && childCount > 0) {
        var ageInputs = document.querySelectorAll('#child-ages .qf-child-age-input');
        var allFilled = true;
        ageInputs.forEach(function(inp) {
          if (inp.value === '' || isNaN(parseInt(inp.value))) allFilled = false;
        });
        if (!allFilled) {
          errorDiv.textContent = 'Inserisci l\'età di tutti i bambini.';
          errorDiv.style.display = 'block';
          return;
        }
      }
```

**BUG 7 — NaN in eta_bambini payload (~line 1208):**

Change:
  eta_bambini: childAges.filter(function(a){ return a!==null; }).join(', '),
To:
  eta_bambini: childAges.filter(function(a){ return a!==null && !isNaN(a); }).join(', '),

**BUG 8 — Bambini label hardcoded "0–7 anni":**

In the HTML (around line 686), change:
  <label class="qf-label">Bambini <small style="font-weight:400;color:#666;">(0–7 anni)</small></label>
To:
  <label class="qf-label">Bambini <small style="font-weight:400;color:#666;" id="bambini-label-age">(0–17 anni)</small></label>

In the JS init block, AFTER the `updatePrice();` and `updateButtonStates();` calls (~lines 1100-1101), add:
```javascript
    // Update bambini label with real max age from CONFIG brackets
    (function() {
      var labelEl = document.getElementById('bambini-label-age');
      if (!labelEl) return;
      if (CONFIG.child_discounts_enabled && CONFIG.child_discount_brackets && CONFIG.child_discount_brackets.length > 0) {
        var maxAge = Math.max.apply(null, CONFIG.child_discount_brackets.map(function(b){ return b.max_age; }));
        labelEl.textContent = '(0\u2013' + maxAge + ' anni)';
      }
    })();
```
  </action>
  <verify>
    Open viaggio.php for a trip with child_discounts_enabled=true. Confirm:
    - Bambini label shows real max age (not hardcoded 7) when brackets are configured
    - Attempting to submit with bambini > 0 but empty age inputs shows validation error
    - Submitting with valid ages proceeds to webhook (no NaN in eta_bambini)
    Inspect browser console for any JS errors on page load.
  </verify>
  <done>
    - Bambini label reads dynamic max age from CONFIG.child_discount_brackets
    - Submit blocked with error message when child ages not filled
    - eta_bambini payload never contains NaN
  </done>
</task>

</tasks>

<verification>
After both tasks:
1. Open admin for any existing trip — confirm date fields load correctly, all 8 tabs visible
2. Save trip — reload JSON, verify date_start/date_end keys, accompagnatore/volo/hotel present
3. Open viaggio.php for a trip with child discounts — confirm bambini label shows correct max age
4. Try submitting quote form with bambini but no age entered — confirm validation error appears
</verification>

<success_criteria>
- All 8 bugs listed in task details are fixed
- No regressions: existing form save, tab switching, itinerary reorder still work
- trips.json keys: date_start, date_end, hotel (not start_date/end_date/hotels)
- viaggio.php: dynamic bambini label, child age validation, NaN-free payload
</success_criteria>

<output>
After completion, create `.planning/quick/12-fix-8-bugs-date-key-mismatch-preserve-ac/12-SUMMARY.md`
</output>
