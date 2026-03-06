---
quick: 3
type: execute
wave: 1
depends_on: []
files_modified:
  - assets/css/style.css
  - viaggio.php
  - data/trips.json
autonomous: true
must_haves:
  truths:
    - "Timeline vertical red line is visible between day dots"
    - "Timeline dots are red with white border and red ring shadow"
    - "Timeline cards have dark background with red left border"
    - "Volo section shows redesigned two-column card layout (andata/ritorno)"
    - "Volo section remains between accompagnatore and tabs, always visible as collapsible"
    - "Tabs nav shows: Itinerario | Alloggi | Cosa Include | Galleria | Richiedi Preventivo"
    - "Alloggi tab renders three hotel cards for West America trip"
    - "Hotel data is in data/trips.json under the west-america-aprile-2026 entry"
  artifacts:
    - path: "assets/css/style.css"
      provides: "Timeline, volo redesign, hotel section CSS"
    - path: "viaggio.php"
      provides: "Updated volo HTML, hotel section, updated tab nav"
    - path: "data/trips.json"
      provides: "hotel array on west-america-aprile-2026 trip"
  key_links:
    - from: "viaggio.php hotel section"
      to: "$trip['hotel']"
      via: "PHP foreach over hotel array"
    - from: "Tab button data-target=alloggi"
      to: "section#alloggi"
      via: "existing JS tab scroll handler"
---

<objective>
Fix timeline visual contrast (red gradient line + red dots), redesign the volo section
with new two-column card CSS and updated PHP HTML, add a new Alloggi tab with hotel
cards (data in trips.json), and reorder the tab nav to match the final section order.

Purpose: Make viaggio.php visually polished and complete with hotel accommodation info.
Output: Updated style.css, viaggio.php, data/trips.json — no /admin/ changes.
</objective>

<execution_context>
@./.claude/get-shit-done/workflows/execute-plan.md
@./.claude/get-shit-done/templates/summary.md
</execution_context>

<context>
@.planning/STATE.md

Key constraints:
- PHP + vanilla JS — no framework
- trips.json as sole data store
- No changes to /admin/ folder
- style.css CSS variable --gold is actually red #CC0031 (established convention)
- Tab JS scroll handler uses data-target matching section id (existing, no changes needed)
</context>

<tasks>

<task type="auto">
  <name>Task 1: Fix timeline CSS + add volo redesign CSS + hotel section CSS in style.css</name>
  <files>assets/css/style.css</files>
  <action>
    In assets/css/style.css make three targeted CSS replacements:

    **1. Replace .timeline::before block** (currently lines ~2036-2045, background:#000744, width:2px):
    ```css
    .timeline::before {
      content: '';
      position: absolute;
      left: 50%;
      top: 0;
      bottom: 0;
      width: 3px;
      background: linear-gradient(to bottom, transparent, #CC0031 10%, #CC0031 90%, transparent);
      transform: translateX(-50%);
      opacity: 0.8;
    }
    ```

    **2. Replace .timeline-dot block** (currently lines ~2061-2078, background:#000744, border:rgba(255,255,255,0.2)):
    ```css
    .timeline-dot {
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #CC0031;
      border: 3px solid #FFFFFF;
      box-shadow: 0 0 0 3px #CC0031, 0 4px 12px rgba(204,0,49,0.4);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 0.75rem;
      font-weight: 700;
      z-index: 1;
      top: 1.25rem;
    }
    ```

    **3. Replace .timeline-card block** (currently lines ~2080-2087):
    ```css
    .timeline-card {
      background: #0d1332;
      border-radius: 12px;
      overflow: hidden;
      max-width: 420px;
      width: 100%;
      border: 1px solid rgba(255,255,255,0.12);
      border-left: 3px solid #CC0031;
      box-shadow: 0 8px 32px rgba(0,0,0,0.4);
    }
    ```

    **4. Add .timeline-card:hover rule** immediately after .timeline-card closing brace:
    ```css
    .timeline-card:hover {
      border-left-color: #000744;
      transform: translateY(-2px);
      box-shadow: 0 12px 40px rgba(0,0,0,0.5);
      transition: all 0.3s ease;
    }
    ```

    **5. Replace ALL .volo-* rules** (currently lines ~2217-2309). Remove the entire block from `.volo-section {` through the closing `@media` for `.volo-cards` and replace with:
    ```css
    .volo-section { padding: 60px 24px; background: #080d24; }
    .volo-header-btn {
      display: flex; align-items: center; gap: 12px;
      background: #000744; color: #fff;
      border: none; border-radius: 10px;
      padding: 14px 28px; font-size: 1rem; font-weight: 600;
      cursor: pointer; margin: 0 auto 32px;
      transition: all 0.3s ease;
    }
    .volo-header-btn:hover { background: #001199; }
    .volo-cards-grid {
      display: grid; grid-template-columns: 1fr 1fr; gap: 24px;
      max-width: 900px; margin: 0 auto;
    }
    .volo-card {
      background: linear-gradient(135deg, #0a1040 0%, #000744 100%);
      border-radius: 16px; padding: 28px;
      border: 1px solid rgba(255,255,255,0.1);
      position: relative; overflow: hidden;
    }
    .volo-card::before {
      content: '\2708';
      position: absolute; right: 20px; top: 20px;
      font-size: 4rem; opacity: 0.06; color: #fff;
    }
    .volo-card-type { font-size: 0.7rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #CC0031; margin-bottom: 12px; }
    .volo-route { font-family: 'Playfair Display', serif; font-size: 1.4rem; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; }
    .volo-route-arrow { color: #CC0031; font-style: normal; }
    .volo-details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .volo-detail-label { font-size: 0.7rem; color: #888; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 3px; }
    .volo-detail-value { font-size: 0.95rem; color: #fff; font-weight: 500; }
    .volo-airline { display: inline-flex; align-items: center; gap: 6px; background: rgba(204,0,49,0.15); border: 1px solid rgba(204,0,49,0.3); color: #CC0031; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; margin-bottom: 20px; }
    .volo-scalo { margin-top: 16px; padding: 10px 14px; background: rgba(255,255,255,0.05); border-radius: 8px; font-size: 0.8rem; color: #aaa; }
    @media (max-width: 768px) { .volo-cards-grid { grid-template-columns: 1fr; } }
    ```

    **6. Add hotel section CSS** — append after the volo block:
    ```css
    /* ========================================================
       HOTEL / ALLOGGI SECTION
       ======================================================== */
    .hotel-section { padding: 60px 24px; background: #060b20; }
    .hotel-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 24px; max-width: 1100px; margin: 0 auto; }
    .hotel-card { background: #1a1f3e; border-radius: 12px; overflow: hidden; position: relative; }
    .hotel-card-img { width: 100%; height: 200px; object-fit: cover; }
    .hotel-card-body { padding: 20px; }
    .hotel-badge-city { position: absolute; top: 12px; left: 12px; background: #000744; color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .hotel-badge-notti { position: absolute; top: 12px; right: 12px; background: #CC0031; color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .hotel-stars { color: #e6b800; font-size: 1rem; margin-bottom: 6px; }
    .hotel-name { font-family: 'Playfair Display', serif; font-size: 1.1rem; color: #fff; font-weight: 700; margin-bottom: 8px; }
    .hotel-desc { color: #aaa; font-size: 0.85rem; line-height: 1.6; margin-bottom: 12px; }
    .hotel-address { font-size: 0.8rem; color: #777; margin-bottom: 12px; }
    .hotel-colazione-yes { display: inline-block; background: rgba(39,174,96,0.15); border: 1px solid rgba(39,174,96,0.3); color: #27ae60; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .hotel-colazione-no { display: inline-block; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #888; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; }
    @media (max-width: 900px) { .hotel-grid { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 600px) { .hotel-grid { grid-template-columns: 1fr; } }
    ```

    Note: Class name `.volo-details-grid` is used instead of `.volo-details` to avoid collision
    with the existing JS-controlled `id="volo-details"` element that will be removed in Task 2.
  </action>
  <verify>
    grep -n "linear-gradient(to bottom, transparent, #CC0031" /c/Users/Zanni/viaggiacolbaffo/assets/css/style.css
    grep -n "hotel-grid\|hotel-card\|volo-cards-grid\|volo-header-btn" /c/Users/Zanni/viaggiacolbaffo/assets/css/style.css
  </verify>
  <done>
    style.css contains: red gradient timeline::before, red .timeline-dot with white border,
    dark .timeline-card with red left border and hover, new .volo-* rules with .volo-cards-grid
    and .volo-header-btn, new .hotel-* rules. Old volo rules fully replaced.
  </done>
</task>

<task type="auto">
  <name>Task 2: Update viaggio.php — volo HTML, add hotel section, reorder tabs</name>
  <files>viaggio.php</files>
  <action>
    Make three targeted rewrites inside viaggio.php:

    **A. Replace the entire DETTAGLI VOLO SECTION block** (the `<?php if (!is_null($volo)): ?>` block,
    lines ~122-165). Replace with:

    ```php
    <?php if (!is_null($volo)): ?>
    <!-- ========================================================
         DETTAGLI VOLO SECTION
         ======================================================== -->
    <div class="volo-section">
      <div class="container">
        <button class="volo-header-btn" id="volo-toggle" type="button">
          &#9992; Dettagli Volo <i class="fa-solid fa-chevron-down" id="volo-chevron" style="transition:transform 0.3s ease;"></i>
        </button>
        <div id="volo-details" style="display:none;">
          <?php if (!($volo['incluso'] ?? false)): ?>
          <div style="text-align:center;color:#aaa;padding:20px 0;">Il volo non è incluso nel prezzo del viaggio.</div>
          <?php else: ?>
          <div class="volo-cards-grid">
            <?php if (!empty($volo['andata'])): $a = $volo['andata']; ?>
            <div class="volo-card">
              <div class="volo-card-type">Volo Andata</div>
              <div class="volo-airline">&#9992; <?php echo htmlspecialchars($a['compagnia'] ?? ''); ?> &middot; <?php echo htmlspecialchars($a['numero_volo'] ?? ''); ?></div>
              <div class="volo-route">
                <?php echo htmlspecialchars($a['partenza_aeroporto'] ?? ''); ?>
                <i class="volo-route-arrow">&#8594;</i>
                <?php echo htmlspecialchars($a['arrivo_aeroporto'] ?? ''); ?>
              </div>
              <div class="volo-details-grid">
                <div>
                  <div class="volo-detail-label">Data</div>
                  <div class="volo-detail-value"><?php echo htmlspecialchars($a['data'] ?? ''); ?></div>
                </div>
                <div>
                  <div class="volo-detail-label">Partenza</div>
                  <div class="volo-detail-value"><?php echo htmlspecialchars($a['orario_partenza'] ?? ''); ?></div>
                </div>
                <div>
                  <div class="volo-detail-label">Arrivo</div>
                  <div class="volo-detail-value"><?php echo htmlspecialchars($a['orario_arrivo'] ?? ''); ?></div>
                </div>
              </div>
              <?php if (!empty($a['scalo'])): ?>
              <div class="volo-scalo">&#128199; Scalo: <?php echo htmlspecialchars($a['scalo']); ?></div>
              <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php if (!empty($volo['ritorno'])): $r = $volo['ritorno']; ?>
            <div class="volo-card">
              <div class="volo-card-type">Volo Ritorno</div>
              <div class="volo-airline">&#9992; <?php echo htmlspecialchars($r['compagnia'] ?? ''); ?> &middot; <?php echo htmlspecialchars($r['numero_volo'] ?? ''); ?></div>
              <div class="volo-route">
                <?php echo htmlspecialchars($r['partenza_aeroporto'] ?? ''); ?>
                <i class="volo-route-arrow">&#8594;</i>
                <?php echo htmlspecialchars($r['arrivo_aeroporto'] ?? ''); ?>
              </div>
              <div class="volo-details-grid">
                <div>
                  <div class="volo-detail-label">Data</div>
                  <div class="volo-detail-value"><?php echo htmlspecialchars($r['data'] ?? ''); ?></div>
                </div>
                <div>
                  <div class="volo-detail-label">Partenza</div>
                  <div class="volo-detail-value"><?php echo htmlspecialchars($r['orario_partenza'] ?? ''); ?></div>
                </div>
                <div>
                  <div class="volo-detail-label">Arrivo</div>
                  <div class="volo-detail-value"><?php echo htmlspecialchars($r['orario_arrivo'] ?? ''); ?></div>
                </div>
              </div>
              <?php if (!empty($r['scalo'])): ?>
              <div class="volo-scalo">&#128199; Scalo: <?php echo htmlspecialchars($r['scalo']); ?></div>
              <?php endif; ?>
            </div>
            <?php endif; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>
    ```

    **B. Replace the STICKY TAB NAVIGATION block** (lines ~167-179). Replace with:

    ```php
    <!-- ========================================================
         STICKY TAB NAVIGATION
         ======================================================== -->
    <div class="trip-tabs" id="trip-tabs">
      <nav class="trip-tabs__nav">
        <button class="trip-tabs__btn active" data-target="itinerario">Itinerario</button>
        <?php if (!empty($trip['hotel'])): ?>
        <button class="trip-tabs__btn" data-target="alloggi">Alloggi</button>
        <?php endif; ?>
        <button class="trip-tabs__btn" data-target="cosa-include">Cosa Include</button>
        <button class="trip-tabs__btn" data-target="galleria">Galleria</button>
        <?php if ($has_form): ?>
        <button class="trip-tabs__btn" data-target="richiedi-preventivo">Richiedi Preventivo</button>
        <?php endif; ?>
      </nav>
    </div>
    ```

    **C. Insert HOTEL/ALLOGGI SECTION** — add immediately after the closing `</section>` of the
    ITINERARY SECTION (after line ~209) and before the COSA INCLUDE comment:

    ```php
    <?php if (!empty($trip['hotel'])): ?>
    <!-- ========================================================
         ALLOGGI SECTION
         Hotel data editable from admin panel (Phase 6)
         ======================================================== -->
    <section class="trip-section hotel-section" id="alloggi">
      <div class="container">
        <div class="section-header">
          <h2 class="section-header__title">Alloggi</h2>
        </div>
        <div class="hotel-grid">
          <?php foreach ($trip['hotel'] as $hotel): ?>
          <div class="hotel-card">
            <img class="hotel-card-img" src="<?php echo htmlspecialchars($hotel['image_url'] ?? ''); ?>" alt="<?php echo htmlspecialchars($hotel['nome']); ?>" loading="lazy">
            <span class="hotel-badge-city"><?php echo htmlspecialchars($hotel['citta']); ?></span>
            <span class="hotel-badge-notti"><?php echo (int)$hotel['notti']; ?> notti</span>
            <div class="hotel-card-body">
              <div class="hotel-stars"><?php echo str_repeat('&#9733;', (int)($hotel['stelle'] ?? 0)); ?></div>
              <div class="hotel-name"><?php echo htmlspecialchars($hotel['nome']); ?></div>
              <p class="hotel-desc"><?php echo htmlspecialchars($hotel['descrizione'] ?? ''); ?></p>
              <div class="hotel-address">&#128205; <?php echo htmlspecialchars($hotel['indirizzo'] ?? ''); ?></div>
              <?php if ($hotel['inclusa_colazione'] ?? false): ?>
              <span class="hotel-colazione-yes">&#10003; Colazione inclusa</span>
              <?php else: ?>
              <span class="hotel-colazione-no">Colazione non inclusa</span>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>
    ```

    **D. Update the volo toggle JS** — in the inline `<script>` at the bottom, find the
    `// --- Volo toggle ---` block and replace it with:

    ```js
    // --- Volo toggle ---
    var voloToggle  = document.getElementById('volo-toggle');
    var voloDetails = document.getElementById('volo-details');
    var voloChevron = document.getElementById('volo-chevron');
    if (voloToggle && voloDetails) {
      voloToggle.addEventListener('click', function () {
        var isOpen = voloDetails.style.display !== 'none';
        voloDetails.style.display = isOpen ? 'none' : 'block';
        if (voloChevron) voloChevron.style.transform = isOpen ? '' : 'rotate(180deg)';
      });
    }
    ```
  </action>
  <verify>
    grep -n "volo-header-btn\|volo-cards-grid\|volo-card-type\|hotel-section\|data-target=\"alloggi\"\|Alloggi" /c/Users/Zanni/viaggiacolbaffo/viaggio.php
  </verify>
  <done>
    viaggio.php contains: volo-header-btn button, volo-cards-grid wrapper, volo-card-type divs,
    hotel-section with hotel-grid, tab nav with Alloggi button (conditionally shown when
    $trip['hotel'] is set), Richiedi Preventivo tab (was "Richiedi Preventivo"). JS volo toggle
    uses style.display instead of classList (matches new non-open default).
  </done>
</task>

<task type="auto">
  <name>Task 3: Add hotel array to west-america-aprile-2026 in trips.json</name>
  <files>data/trips.json</files>
  <action>
    Read data/trips.json. Find the "west-america-aprile-2026" trip object (first entry).
    Add a "hotel" key with the following array, inserted before the closing brace of
    that trip object (e.g., after "form_config" or whichever is the last key):

    ```json
    "hotel": [
      {
        "citta": "Los Angeles",
        "nome": "Hotel Santa Monica Beach",
        "stelle": 4,
        "notti": 3,
        "descrizione": "Hotel moderno a pochi passi dalla spiaggia di Santa Monica.",
        "image_url": "https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800",
        "indirizzo": "Santa Monica, CA",
        "inclusa_colazione": true
      },
      {
        "citta": "Las Vegas",
        "nome": "The LINQ Hotel",
        "stelle": 4,
        "notti": 3,
        "descrizione": "Sul famoso Strip di Las Vegas, posizione centrale.",
        "image_url": "https://images.unsplash.com/photo-1605649487212-47bdab064df7?w=800",
        "indirizzo": "Las Vegas Strip, NV",
        "inclusa_colazione": false
      },
      {
        "citta": "San Francisco",
        "nome": "Union Square Boutique Hotel",
        "stelle": 4,
        "notti": 4,
        "descrizione": "Nel cuore di San Francisco, vicino ai principali attrazioni.",
        "image_url": "https://images.unsplash.com/photo-1501594907352-04cda38ebc29?w=800",
        "indirizzo": "Union Square, San Francisco, CA",
        "inclusa_colazione": true
      }
    ]
    ```

    After editing, validate JSON is well-formed by running:
    python3 -c "import json,sys; json.load(open('data/trips.json')); print('JSON valid')"
    (or use php -r if python3 unavailable: php -r "json_decode(file_get_contents('data/trips.json')); echo json_last_error()===0?'OK':'ERR';")
  </action>
  <verify>
    grep -c "Santa Monica Beach\|LINQ Hotel\|Union Square" /c/Users/Zanni/viaggiacolbaffo/data/trips.json
  </verify>
  <done>
    data/trips.json contains "hotel" key on west-america-aprile-2026 with 3 entries (LA, Vegas, SF).
    JSON is valid (no parse errors). grep returns count of 3.
  </done>
</task>

</tasks>

<verification>
After all three tasks, spot-check the page:
- Open viaggio.php?slug=west-america-aprile-2026 in browser
- Timeline line should appear as red gradient between day dots
- Day dots should be red with white border
- Tab nav order: Itinerario | Alloggi | Cosa Include | Galleria | Richiedi Preventivo
- Clicking "Alloggi" tab scrolls to 3 hotel cards
- Clicking "Dettagli Volo" button opens/closes the two-column volo cards
</verification>

<success_criteria>
- style.css: red timeline line, red dots, dark cards with red left border, new volo CSS, hotel CSS
- viaggio.php: new volo HTML using volo-cards-grid, hotel section renders from $trip['hotel'],
  tab nav shows Alloggi when hotel data present, Richiedi Preventivo is last tab
- data/trips.json: west-america-aprile-2026 has "hotel" array with 3 objects, JSON valid
</success_criteria>

<output>
After completion, create `.planning/quick/3-fix-timeline-visuals-redesign-volo-secti/3-SUMMARY.md`
with what was changed and any notable implementation notes.
</output>
