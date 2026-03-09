---
phase: quick-17
plan: 01
type: execute
wave: 1
depends_on: []
files_modified:
  - viaggio.php
  - assets/css/style.css
autonomous: true
requirements: [LEAD-GATE-01]
must_haves:
  truths:
    - "Visitor sees hero + highlights + first 2 itinerary days without a gate"
    - "Everything below day 2 is blurred and overlaid with the lead gate form"
    - "After submitting valid Nome/Cognome/Email/Telefono, the gate dissolves and full page is visible"
    - "Unlock state persists in localStorage — page refresh never re-shows the gate"
    - "On success, form data POSTs to WAITLIST_WEBHOOK_URL (if configured); fails silently and unlocks anyway"
  artifacts:
    - path: viaggio.php
      provides: "GATE object, gate HTML overlay, split itinerary foreach, gated-content wrappers, JS gate logic"
    - path: assets/css/style.css
      provides: "lead-gate and gated-content CSS rules appended at end of file"
  key_links:
    - from: "viaggio.php GATE var"
      to: "JS gate logic"
      via: "GATE.slug used to build localStorage key, GATE.webhook used for fetch()"
    - from: ".gated-content elements"
      to: "JS unlockGate()"
      via: "classList.remove('gated-content--hidden')"
    - from: "#lg-submit click"
      to: "unlockGate()"
      via: "fetch(GATE.webhook).then(doUnlock) or direct doUnlock when no webhook"
---

<objective>
Implement a lead gate on viaggio.php that shows the hero, highlights, and first 2 itinerary days freely, then blurs all remaining content behind a form asking Nome, Cognome, Email, Telefono. On valid submission, data is POSTed to WAITLIST_WEBHOOK_URL and the gate dissolves permanently (localStorage).

Purpose: Capture visitor leads before they access the full trip itinerary, hotels, pricing, and gallery.
Output: viaggio.php with gate HTML + JS + PHP split logic; style.css with gate CSS appended.
</objective>

<execution_context>
@./.claude/get-shit-done/workflows/execute-plan.md
@./.claude/get-shit-done/templates/summary.md
</execution_context>

<context>
@.planning/STATE.md

Key viaggio.php structure (line references):
- Line 57: `require_once ROOT . '/includes/header.php';` — PHP top ends here
- Lines 484–507: `<?php if ($has_form): ?> <script> const CONFIG = {...}; </script>` — CONFIG block, conditional on form
- Line 806: Unconditional `<script>` block starts (always present)
- Line 807: `function toggleClientEmail()` (global)
- Line 816: IIFE opens: `(function () {`
- Line 172: `<!-- ITINERARY SECTION — TIMELINE -->` comment + `<section id="itinerario">`
- Line 179–195: Itinerary foreach loop (to be replaced with split version)
- Line 200: `<?php if (!empty($accompagnatore['nome'])): ?>` — accompagnatore section
- Line 221: `<?php if (!is_null($volo)): ?>` — volo section
- Line 298: `<?php if (!empty($trip['hotel'])): ?>` — alloggi section
- Line 356: `<section class="trip-section trip-section--dark" id="cosa-include">` — always present
- Line 385: `<section class="trip-section trip-section--dark" id="galleria">` — always present
- Line 408–414: Lightbox HTML (NOT gated — keep outside)
- Line 419: `<?php if (!empty($trip['tags'])): ?>` — tags section
- Line 456: `<?php if (!empty($related)): ?>` — related section
- Line 483: `<?php if ($has_form): ?>` — quote form section (conditional)
- Line 802: `</main>`
- Line 804: footer include
- Line 806: unconditional `<script>` block
- IIFE closing is near end of file
</context>

<tasks>

<task type="auto">
  <name>Task 1: PHP + HTML changes in viaggio.php — GATE object, gate overlay, split itinerary, gated-content wrappers</name>
  <files>viaggio.php</files>
  <action>
Make four targeted edits to viaggio.php. Read the full file first to confirm exact line numbers match, then apply:

**Edit A — Add GATE object to unconditional script block**

The unconditional `<script>` block starts at approximately line 806 (after `</main>` and the footer include). Inside that block, BEFORE `function toggleClientEmail()`, insert:

```php
var GATE = {
  slug: <?= json_encode($slug) ?>,
  webhook: <?= json_encode(defined('WAITLIST_WEBHOOK_URL') ? WAITLIST_WEBHOOK_URL : '') ?>
};
```

This must be in the unconditional script block (not inside `<?php if ($has_form): ?>`) because the gate is always active.

**Edit B — Insert lead gate overlay HTML before the itinerary section**

Find `<!-- ========================================================
     ITINERARY SECTION — TIMELINE -->` and insert the gate HTML immediately before that comment block. The HTML to insert verbatim:

```html
<!-- ========================================================
     LEAD GATE OVERLAY
     ======================================================== -->
<div id="lead-gate" class="lead-gate">
  <div class="lead-gate__blur-hint">
    <i class="fa-solid fa-lock"></i>
    <span>Sblocca il programma completo</span>
  </div>
  <div class="lead-gate__card">
    <div class="lead-gate__icon"><i class="fa-solid fa-map-location-dot"></i></div>
    <h2 class="lead-gate__title">Scopri tutti i dettagli del viaggio</h2>
    <p class="lead-gate__subtitle">Inserisci i tuoi dati per accedere all'itinerario completo, agli alloggi, ai prezzi e a tutto quello che è incluso.</p>
    <div class="lead-gate__form" id="lead-gate-form">
      <div class="lead-gate__row">
        <div class="lead-gate__field">
          <input type="text" id="lg-nome" placeholder="Nome" autocomplete="given-name">
        </div>
        <div class="lead-gate__field">
          <input type="text" id="lg-cognome" placeholder="Cognome" autocomplete="family-name">
        </div>
      </div>
      <div class="lead-gate__row">
        <div class="lead-gate__field">
          <input type="email" id="lg-email" placeholder="Email" autocomplete="email">
        </div>
        <div class="lead-gate__field">
          <input type="tel" id="lg-telefono" placeholder="Telefono" autocomplete="tel">
        </div>
      </div>
      <div id="lg-error" class="lead-gate__error" style="display:none;"></div>
      <button type="button" id="lg-submit" class="lead-gate__btn">
        <span id="lg-btn-text">Scopri il programma completo</span>
        <span id="lg-btn-spinner" style="display:none;"><i class="fa-solid fa-spinner fa-spin"></i></span>
      </button>
      <p class="lead-gate__privacy">
        <i class="fa-solid fa-shield-halved"></i>
        Nessuno spam. I tuoi dati sono al sicuro.
      </p>
    </div>
  </div>
</div>
```

**Edit C — Split itinerary foreach into visible (days 1-2) + gated (day 3+)**

Find the itinerary foreach block inside `<div class="timeline">`:
```php
      <?php foreach (($trip['itinerary'] ?? []) as $day): ?>
      <div class="timeline-item">
        ...
      </div>
      <?php endforeach; ?>
```

Replace with the split version using `array_slice`:
```php
    <?php
    $itinerary_days = $trip['itinerary'] ?? [];
    $visible_days   = array_slice($itinerary_days, 0, 2);
    $gated_days     = array_slice($itinerary_days, 2);
    ?>
    <?php foreach ($visible_days as $day): ?>
    <div class="timeline-item">
      <div class="timeline-dot"><?php echo str_pad((int)$day['day'], 2, '0', STR_PAD_LEFT); ?></div>
      <div class="timeline-card">
        <?php if (!empty($day['image_url'])): ?>
        <img class="timeline-card__photo" src="<?php echo htmlspecialchars($day['image_url']); ?>" alt="<?php echo htmlspecialchars($day['title']); ?>" loading="lazy">
        <?php endif; ?>
        <div class="timeline-card__body">
          <?php if (!empty($day['location'])): ?>
          <div class="timeline-card__location"><?php echo htmlspecialchars($day['location']); ?></div>
          <?php endif; ?>
          <div class="timeline-card__title"><?php echo htmlspecialchars($day['title']); ?></div>
          <div class="timeline-card__desc"><?php echo strip_tags($day['description'] ?? '', '<p><br><strong><em><ul><ol><li><b><i>'); ?></div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>

    <?php if (!empty($gated_days)): ?>
    <div class="gated-content" id="gated-content">
      <?php foreach ($gated_days as $day): ?>
      <div class="timeline-item">
        <div class="timeline-dot"><?php echo str_pad((int)$day['day'], 2, '0', STR_PAD_LEFT); ?></div>
        <div class="timeline-card">
          <?php if (!empty($day['image_url'])): ?>
          <img class="timeline-card__photo" src="<?php echo htmlspecialchars($day['image_url']); ?>" alt="<?php echo htmlspecialchars($day['title']); ?>" loading="lazy">
          <?php endif; ?>
          <div class="timeline-card__body">
            <?php if (!empty($day['location'])): ?>
            <div class="timeline-card__location"><?php echo htmlspecialchars($day['location']); ?></div>
            <?php endif; ?>
            <div class="timeline-card__title"><?php echo htmlspecialchars($day['title']); ?></div>
            <div class="timeline-card__desc"><?php echo strip_tags($day['description'] ?? '', '<p><br><strong><em><ul><ol><li><b><i>'); ?></div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
```

**Edit D — Wrap gated sections with gated-content divs**

Wrap these sections with `<div class="gated-content" id="gated-XXX">...</div>`. Preserve all existing conditional PHP guards around each section.

Sections to wrap (in order they appear in the file):

1. accompagnatore section: wrap `<?php if (!empty($accompagnatore['nome'])): ?> ... <?php endif; ?>` → `<div class="gated-content" id="gated-accompagnatore"> <?php if ... endif; ?> </div>`

2. volo section: wrap `<?php if (!is_null($volo)): ?> ... <?php endif; ?>` → `<div class="gated-content" id="gated-volo"> <?php if ... endif; ?> </div>`

3. alloggi section: wrap `<?php if (!empty($trip['hotel'])): ?> ... <?php endif; ?>` → `<div class="gated-content" id="gated-alloggi"> <?php if ... endif; ?> </div>`

4. cosa-include section: wrap the entire `<section ... id="cosa-include"> ... </section>` → `<div class="gated-content" id="gated-cosa-include"> <section ...>...</section> </div>`

5. galleria section: wrap the entire `<section ... id="galleria"> ... </section>` → `<div class="gated-content" id="gated-galleria"> <section ...>...</section> </div>`

   NOTE: Do NOT wrap the lightbox `<div class="lightbox" ...>`. Leave it outside.

6. tags section: wrap `<?php if (!empty($trip['tags'])): ?> ... <?php endif; ?>` → `<div class="gated-content" id="gated-tags"> <?php if ... endif; ?> </div>`

7. related section: wrap `<?php if (!empty($related)): ?> ... <?php endif; ?>` → `<div class="gated-content" id="gated-related"> <?php if ... endif; ?> </div>`

8. quote form section: wrap `<?php if ($has_form): ?> ... <?php endif; ?>` → `<div class="gated-content" id="gated-preventivo"> <?php if ... endif; ?> </div>`

Do NOT wrap: trip-hero, trip-topbar, trip-highlights, trip-tabs, the itinerary section header+first 2 days, or the lightbox.
  </action>
  <verify>
    <automated>grep -n "lead-gate\|gated-content\|GATE\s*=" /c/Users/Zanni/viaggiacolbaffo/viaggio.php | head -40</automated>
  </verify>
  <done>
    - `var GATE = {` appears in the unconditional script block
    - `id="lead-gate"` overlay exists before itinerary section
    - `array_slice` split of itinerary present
    - `class="gated-content"` wrappers present for all 8 sections (gated-content, gated-accompagnatore, gated-volo, gated-alloggi, gated-cosa-include, gated-galleria, gated-tags, gated-related, gated-preventivo)
  </done>
</task>

<task type="auto">
  <name>Task 2: JS gate logic in viaggio.php IIFE + lead gate CSS in style.css</name>
  <files>viaggio.php, assets/css/style.css</files>
  <action>
**Part A — JS gate logic in viaggio.php**

Inside the IIFE `(function () {` that starts around line 816, at the VERY BEGINNING (before `// --- Sticky top bar ---`), insert the gate logic block:

```js
  // ── LEAD GATE ──────────────────────────────────────────────────
  var STORAGE_KEY = 'vcb_unlocked_' + GATE.slug;
  var gateEl      = document.getElementById('lead-gate');
  var gatedEls    = document.querySelectorAll('.gated-content');

  function unlockGate() {
    if (gateEl) {
      gateEl.classList.add('lead-gate--unlocked');
      setTimeout(function() { gateEl.style.display = 'none'; }, 400);
    }
    gatedEls.forEach(function(el) {
      el.classList.remove('gated-content--hidden');
    });
    try { localStorage.setItem(STORAGE_KEY, '1'); } catch(e) {}
  }

  function isUnlocked() {
    try { return localStorage.getItem(STORAGE_KEY) === '1'; } catch(e) { return false; }
  }

  if (isUnlocked()) {
    unlockGate();
  } else {
    gatedEls.forEach(function(el) {
      el.classList.add('gated-content--hidden');
    });
  }

  var lgSubmit = document.getElementById('lg-submit');
  var lgError  = document.getElementById('lg-error');

  if (lgSubmit) {
    lgSubmit.addEventListener('click', function() {
      var nome     = (document.getElementById('lg-nome')     || {}).value || '';
      var cognome  = (document.getElementById('lg-cognome')  || {}).value || '';
      var email    = (document.getElementById('lg-email')    || {}).value || '';
      var telefono = (document.getElementById('lg-telefono') || {}).value || '';

      if (!nome.trim() || !cognome.trim() || !email.trim() || !telefono.trim()) {
        lgError.textContent = 'Compila tutti i campi per continuare.';
        lgError.style.display = 'block';
        return;
      }
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        lgError.textContent = 'Inserisci un indirizzo email valido.';
        lgError.style.display = 'block';
        return;
      }
      lgError.style.display = 'none';

      document.getElementById('lg-btn-text').style.display    = 'none';
      document.getElementById('lg-btn-spinner').style.display = 'inline';
      lgSubmit.disabled = true;

      var payload = {
        nome: nome, cognome: cognome, email: email, telefono: telefono,
        viaggio: GATE.slug, source: 'lead_gate'
      };

      var doUnlock = function() {
        document.getElementById('lg-btn-text').style.display    = 'inline';
        document.getElementById('lg-btn-spinner').style.display = 'none';
        lgSubmit.disabled = false;
        unlockGate();
      };

      if (GATE.webhook) {
        fetch(GATE.webhook, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        })
        .then(function() { doUnlock(); })
        .catch(function() { doUnlock(); });
      } else {
        doUnlock();
      }
    });
  }
  // ── END LEAD GATE ──────────────────────────────────────────────
```

**Part B — Append lead gate CSS to assets/css/style.css**

Read style.css, then append the following block at the very end of the file (after all existing rules):

```css
/* ================================================================
   LEAD GATE
   ================================================================ */

.gated-content--hidden {
  filter: blur(6px);
  opacity: 0.35;
  pointer-events: none;
  user-select: none;
  position: relative;
}

.lead-gate {
  position: relative;
  z-index: 50;
  background: linear-gradient(180deg, transparent 0%, #060b20 18%);
  margin-top: -120px;
  padding: 120px 24px 0;
  transition: opacity 0.4s ease;
}

.lead-gate--unlocked {
  opacity: 0;
  pointer-events: none;
}

.lead-gate__blur-hint {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  color: rgba(255,255,255,0.35);
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  margin-bottom: 24px;
}

.lead-gate__blur-hint i {
  font-size: 1rem;
  color: #CC0031;
}

.lead-gate__card {
  background: #0d1330;
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 24px;
  padding: 48px 48px 40px;
  max-width: 680px;
  margin: 0 auto;
  text-align: center;
  box-shadow: 0 24px 80px rgba(0,0,0,0.5);
}

.lead-gate__icon {
  width: 56px;
  height: 56px;
  background: linear-gradient(135deg, #000744, #001199);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  font-size: 1.4rem;
  color: #fff;
}

.lead-gate__title {
  font-family: 'Playfair Display', serif;
  font-size: 1.6rem;
  color: #fff;
  font-weight: 700;
  margin-bottom: 12px;
  line-height: 1.3;
}

.lead-gate__subtitle {
  font-size: 0.9rem;
  color: rgba(255,255,255,0.55);
  line-height: 1.65;
  margin-bottom: 32px;
  max-width: 500px;
  margin-left: auto;
  margin-right: auto;
}

.lead-gate__form { width: 100%; }

.lead-gate__row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-bottom: 12px;
}

.lead-gate__field input {
  width: 100%;
  padding: 13px 16px;
  background: rgba(255,255,255,0.06);
  border: 1.5px solid rgba(255,255,255,0.1);
  border-radius: 10px;
  color: #fff;
  font-size: 0.9rem;
  font-family: inherit;
  outline: none;
  transition: border-color 0.2s;
  -webkit-appearance: none;
}

.lead-gate__field input::placeholder { color: rgba(255,255,255,0.3); }
.lead-gate__field input:focus { border-color: rgba(255,255,255,0.4); }

.lead-gate__error {
  background: rgba(204,0,49,0.15);
  border: 1px solid rgba(204,0,49,0.3);
  color: #ff6b8a;
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 0.83rem;
  margin-bottom: 12px;
  text-align: left;
}

.lead-gate__btn {
  width: 100%;
  padding: 15px 24px;
  background: #CC0031;
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 700;
  font-family: inherit;
  cursor: pointer;
  transition: background 0.2s, transform 0.15s;
  margin-top: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.lead-gate__btn:hover:not(:disabled) {
  background: #a80028;
  transform: translateY(-1px);
}

.lead-gate__btn:disabled { opacity: 0.7; cursor: not-allowed; }

.lead-gate__privacy {
  font-size: 0.75rem;
  color: rgba(255,255,255,0.25);
  margin-top: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

@media (max-width: 600px) {
  .lead-gate__card { padding: 32px 20px 28px; border-radius: 16px; }
  .lead-gate__row { grid-template-columns: 1fr; gap: 10px; }
  .lead-gate__title { font-size: 1.3rem; }
}
```
  </action>
  <verify>
    <automated>grep -n "LEAD GATE\|gated-content--hidden\|lead-gate__btn\|STORAGE_KEY\|unlockGate" /c/Users/Zanni/viaggiacolbaffo/assets/css/style.css /c/Users/Zanni/viaggiacolbaffo/viaggio.php | head -30</automated>
  </verify>
  <done>
    - `STORAGE_KEY` and `unlockGate` function present inside the IIFE in viaggio.php
    - `gated-content--hidden` CSS rule present in style.css
    - `lead-gate__btn` CSS rule present in style.css
    - `@media (max-width: 600px)` gate responsive block present at end of style.css
  </done>
</task>

</tasks>

<verification>
After both tasks:
1. `grep -c "gated-content" viaggio.php` returns 9+ (one per gated section + the JS references)
2. `grep -n "var GATE" viaggio.php` shows the GATE object in the unconditional script block (after line 804)
3. `grep -n "STORAGE_KEY" viaggio.php` shows the line inside the IIFE (after `(function () {`)
4. `grep -n "lead-gate" assets/css/style.css | tail -5` shows gate CSS at end of file
5. `grep -n "lead-gate__card\|gated-content--hidden" assets/css/style.css` returns results
</verification>

<success_criteria>
- Hero, highlights, and first 2 itinerary days render without blur
- All content from day 3 onward plus accompagnatore, volo, alloggi, cosa-include, galleria, tags, related, and quote form carry `class="gated-content"` and are hidden with blur on page load
- The lead gate card appears between the visible itinerary days and the blurred content
- Submitting with valid inputs (all 4 fields + valid email) triggers the webhook fetch (if WAITLIST_WEBHOOK_URL is set) then removes the gate and unhides all gated content
- Submitting with any blank field shows inline error and does not unlock
- Refreshing after unlock skips the gate (localStorage key present)
- Style is consistent with site brand: navy #000744, red #CC0031, Playfair Display title
</success_criteria>

<output>
After completion, create `.planning/quick/17-lead-gate-on-viaggio-php-unlock-full-tri/17-SUMMARY.md` using the summary template.
</output>
