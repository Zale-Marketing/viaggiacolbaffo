---
phase: quick-18
plan: 18
type: execute
wave: 1
depends_on: []
files_modified:
  - viaggio.php
  - assets/css/style.css
autonomous: true
requirements: []
must_haves:
  truths:
    - "Sticky bar slides up from bottom after user scrolls past last visible day"
    - "Clicking the sticky bar opens the bottom sheet with the lead form"
    - "Submitting valid form data closes the sheet and reveals all gated content"
    - "Already-unlocked visitors see no gate bar or sheet on page load"
    - "Old #lead-gate overlay div is completely removed from viaggio.php"
  artifacts:
    - path: "viaggio.php"
      provides: "gate-bar, gate-overlay, gate-sheet HTML; new JS block"
    - path: "assets/css/style.css"
      provides: "gate-bar, gate-overlay, gate-sheet styles"
  key_links:
    - from: "gate-bar-btn click"
      to: "gate-sheet--open class"
      via: "openSheet() JS function"
    - from: "IntersectionObserver sentinel"
      to: "gate-bar--visible class"
      via: "last visible .timeline-item"
    - from: "lg-submit click"
      to: "unlockGate() / .gated-content--hidden removal"
      via: "fetch GATE.webhook or direct doUnlock()"
---

<objective>
Replace the old full-page #lead-gate overlay on viaggio.php with a sticky bottom bar + slide-up bottom sheet pattern. The bar appears when the user scrolls past the last visible day (IntersectionObserver). Clicking it opens a bottom sheet with the lead form. Submitting the form unlocks gated content and persists the unlock in localStorage.

Purpose: Better mobile UX — the old overlay blocked the entire viewport; the new pattern is non-intrusive until the user reaches the gate point.
Output: viaggio.php with new HTML + JS; assets/css/style.css with new gate styles.
</objective>

<execution_context>
@./.claude/get-shit-done/workflows/execute-plan.md
@./.claude/get-shit-done/templates/summary.md
</execution_context>

<context>
@.planning/STATE.md

Key constraints from prior decisions:
- PHP + vanilla JS (no framework)
- localStorage key pattern: vcb_unlocked_{slug} (established in Quick-17)
- GATE object already defined in viaggio.php unconditional script block
- .gated-content--hidden class controls blur/opacity (introduced in Quick-17)
- Webhook POST fails silently — doUnlock() always called in catch
- Brand colors: navy #000744, red #CC0031
</context>

<tasks>

<task type="auto">
  <name>Task 1: Replace HTML — remove old gate div, add bar + sheet before </body></name>
  <files>viaggio.php</files>
  <action>
    Make two edits to viaggio.php:

    EDIT A — DELETE the old lead gate overlay block.
    Find the HTML comment block that begins with:
      <!-- ========================================================
           LEAD GATE OVERLAY
    and delete everything from that comment through to (and including) the closing </div> that ends the element with id="lead-gate". This is the entire old overlay div.

    EDIT B — INSERT new gate HTML immediately before the closing </body> tag.
    Insert the following block (verbatim):

<!-- ========================================================
     LEAD GATE — sticky bar + bottom sheet
     ======================================================== -->

<!-- Sticky bottom bar -->
<div id="gate-bar" class="gate-bar">
  <div class="gate-bar__text">
    <i class="fa-solid fa-lock"></i>
    <span>Scopri l'itinerario completo, gli alloggi e i prezzi</span>
  </div>
  <button type="button" id="gate-bar-btn" class="gate-bar__btn">Sblocca ora</button>
</div>

<!-- Bottom sheet overlay -->
<div id="gate-overlay" class="gate-overlay"></div>

<!-- Bottom sheet -->
<div id="gate-sheet" class="gate-sheet">
  <div class="gate-sheet__handle"></div>
  <div class="gate-sheet__inner">
    <div class="gate-sheet__icon"><i class="fa-solid fa-map-location-dot"></i></div>
    <h2 class="gate-sheet__title">Scopri tutti i dettagli del viaggio</h2>
    <p class="gate-sheet__subtitle">Inserisci i tuoi dati per accedere all'itinerario completo, agli alloggi, ai prezzi e a tutto quello che è incluso.</p>
    <div class="gate-sheet__row">
      <div class="gate-sheet__field">
        <input type="text" id="lg-nome" placeholder="Nome" autocomplete="given-name">
      </div>
      <div class="gate-sheet__field">
        <input type="text" id="lg-cognome" placeholder="Cognome" autocomplete="family-name">
      </div>
    </div>
    <div class="gate-sheet__row">
      <div class="gate-sheet__field">
        <input type="email" id="lg-email" placeholder="Email" autocomplete="email">
      </div>
      <div class="gate-sheet__field">
        <input type="tel" id="lg-telefono" placeholder="Telefono" autocomplete="tel">
      </div>
    </div>
    <div class="gate-sheet__checks">
      <label class="gate-sheet__check">
        <input type="checkbox" id="lg-privacy" required>
        <span>Ho letto e accetto la <a href="/privacy-policy" target="_blank">Privacy Policy</a>. Consenso obbligatorio. *</span>
      </label>
      <label class="gate-sheet__check">
        <input type="checkbox" id="lg-marketing">
        <span>Acconsento a ricevere comunicazioni promozionali e offerte personalizzate. (Facoltativo)</span>
      </label>
    </div>
    <div id="lg-error" class="gate-sheet__error" style="display:none;"></div>
    <button type="button" id="lg-submit" class="gate-sheet__btn">
      <span id="lg-btn-text">Scopri il programma completo</span>
      <span id="lg-btn-spinner" style="display:none;"><i class="fa-solid fa-spinner fa-spin"></i></span>
    </button>
    <p class="gate-sheet__privacy-note">
      <i class="fa-solid fa-shield-halved"></i>
      Nessuno spam. I tuoi dati sono al sicuro.
    </p>
  </div>
</div>
  </action>
  <verify>grep -n "gate-bar\|gate-sheet\|gate-overlay\|lead-gate" viaggio.php — must show gate-bar/gate-sheet/gate-overlay present and NO id="lead-gate" remaining</verify>
  <done>viaggio.php has no #lead-gate div; has gate-bar, gate-overlay, gate-sheet divs before </body></done>
</task>

<task type="auto">
  <name>Task 2: Replace gate JS in viaggio.php</name>
  <files>viaggio.php</files>
  <action>
    Find the JS block delimited by:
      // ── LEAD GATE ──────────────────────────────────────────────────
    and:
      // ── END LEAD GATE ──────────────────────────────────────────────

    Replace everything between (and including) those two delimiter lines with:

  // ── LEAD GATE ──────────────────────────────────────────────────
  var STORAGE_KEY  = 'vcb_unlocked_' + GATE.slug;
  var gatedEls     = document.querySelectorAll('.gated-content');
  var gateBar      = document.getElementById('gate-bar');
  var gateBarBtn   = document.getElementById('gate-bar-btn');
  var gateSheet    = document.getElementById('gate-sheet');
  var gateOverlay  = document.getElementById('gate-overlay');
  var lgSubmit     = document.getElementById('lg-submit');
  var lgError      = document.getElementById('lg-error');

  // The sentinel element: bottom of the 2nd visible day
  // We watch the last .timeline-item NOT inside .gated-content
  var visibleDays  = document.querySelectorAll('.timeline-item:not(.gated-content .timeline-item)');
  var sentinel     = visibleDays.length ? visibleDays[visibleDays.length - 1] : null;

  function isUnlocked() {
    try { return localStorage.getItem(STORAGE_KEY) === '1'; } catch(e) { return false; }
  }

  function unlockGate() {
    // Hide bar and sheet
    if (gateBar)    { gateBar.classList.remove('gate-bar--visible'); }
    if (gateSheet)  { closeSheet(); }
    // Reveal all gated content
    gatedEls.forEach(function(el) {
      el.classList.remove('gated-content--hidden');
    });
    try { localStorage.setItem(STORAGE_KEY, '1'); } catch(e) {}
  }

  function openSheet() {
    if (!gateSheet || !gateOverlay) return;
    gateSheet.classList.add('gate-sheet--open');
    gateOverlay.classList.add('gate-overlay--visible');
    document.body.style.overflow = 'hidden';
  }

  function closeSheet() {
    if (!gateSheet || !gateOverlay) return;
    gateSheet.classList.remove('gate-sheet--open');
    gateOverlay.classList.remove('gate-overlay--visible');
    document.body.style.overflow = '';
  }

  // If already unlocked on load, skip everything
  if (isUnlocked()) {
    unlockGate();
  } else {
    // Hide all gated content
    gatedEls.forEach(function(el) {
      el.classList.add('gated-content--hidden');
    });

    // IntersectionObserver: show bar when sentinel scrolls out of view (user passed it)
    if (sentinel && gateBar && 'IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
          if (!isUnlocked()) {
            if (!entry.isIntersecting) {
              // Sentinel is above the viewport = user scrolled past it
              gateBar.classList.add('gate-bar--visible');
            } else {
              // Sentinel back in view = user scrolled back up
              gateBar.classList.remove('gate-bar--visible');
              closeSheet();
            }
          }
        });
      }, { threshold: 0 });
      observer.observe(sentinel);
    }

    // Bar click → open sheet
    if (gateBar) {
      gateBar.addEventListener('click', openSheet);
    }

    // Overlay click → close sheet
    if (gateOverlay) {
      gateOverlay.addEventListener('click', closeSheet);
    }

    // Submit
    if (lgSubmit) {
      lgSubmit.addEventListener('click', function() {
        var nome     = (document.getElementById('lg-nome')     || {}).value || '';
        var cognome  = (document.getElementById('lg-cognome')  || {}).value || '';
        var email    = (document.getElementById('lg-email')    || {}).value || '';
        var telefono = (document.getElementById('lg-telefono') || {}).value || '';
        var privacy  = document.getElementById('lg-privacy');

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
        if (privacy && !privacy.checked) {
          lgError.textContent = 'Devi accettare la Privacy Policy per continuare.';
          lgError.style.display = 'block';
          return;
        }
        lgError.style.display = 'none';

        document.getElementById('lg-btn-text').style.display    = 'none';
        document.getElementById('lg-btn-spinner').style.display = 'inline';
        lgSubmit.disabled = true;

        var marketing = document.getElementById('lg-marketing');
        var payload = {
          nome: nome, cognome: cognome, email: email, telefono: telefono,
          privacy: true,
          marketing: marketing ? marketing.checked : false,
          viaggio: GATE.slug,
          source: 'lead_gate'
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
  }
  // ── END LEAD GATE ──────────────────────────────────────────────
  </action>
  <verify>grep -n "gateBar\|gateSheet\|IntersectionObserver\|openSheet\|closeSheet" viaggio.php — all references must be present</verify>
  <done>viaggio.php JS block contains new sentinel-based IntersectionObserver logic and openSheet/closeSheet functions; no reference to old #lead-gate overlay remains</done>
</task>

<task type="auto">
  <name>Task 3: Replace lead gate CSS block in style.css</name>
  <files>assets/css/style.css</files>
  <action>
    Find the CSS block that begins with:
      /* ================================================================
         LEAD GATE
    and ends at the closing brace of the mobile media query for the gate (the @media (max-width: 600px) block that contains gate-related rules).

    Replace that entire block (from the opening comment to the last closing brace of the mobile media query) with:

/* ================================================================
   LEAD GATE — sticky bar + bottom sheet
   ================================================================ */

/* Gated content: blurred when hidden */
.gated-content--hidden {
  filter: blur(7px);
  opacity: 0.3;
  pointer-events: none;
  user-select: none;
}

/* ── Sticky bottom bar ── */
.gate-bar {
  position: fixed;
  bottom: 0; left: 0; right: 0;
  z-index: 300;
  background: #000744;
  border-top: 1px solid rgba(255,255,255,0.1);
  padding: 14px 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  transform: translateY(100%);
  transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);
  box-shadow: 0 -4px 24px rgba(0,0,0,0.4);
}

.gate-bar--visible {
  transform: translateY(0);
}

.gate-bar__text {
  display: flex;
  align-items: center;
  gap: 10px;
  color: rgba(255,255,255,0.85);
  font-size: 0.9rem;
  font-weight: 500;
  min-width: 0;
}

.gate-bar__text i {
  color: #CC0031;
  font-size: 1rem;
  flex-shrink: 0;
}

.gate-bar__text span {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.gate-bar__btn {
  background: #CC0031;
  color: #fff;
  border: none;
  padding: 11px 28px;
  border-radius: 8px;
  font-weight: 700;
  font-size: 0.9rem;
  font-family: inherit;
  cursor: pointer;
  flex-shrink: 0;
  transition: background 0.2s, transform 0.15s;
  white-space: nowrap;
}

.gate-bar__btn:hover {
  background: #a80028;
  transform: translateY(-1px);
}

/* ── Overlay ── */
.gate-overlay {
  position: fixed;
  inset: 0;
  z-index: 310;
  background: rgba(0,0,0,0.6);
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.3s ease;
  backdrop-filter: blur(2px);
}

.gate-overlay--visible {
  opacity: 1;
  pointer-events: all;
}

/* ── Bottom sheet ── */
.gate-sheet {
  position: fixed;
  bottom: 0; left: 0; right: 0;
  z-index: 320;
  background: #0d1330;
  border-top: 1px solid rgba(255,255,255,0.1);
  border-radius: 24px 24px 0 0;
  padding-bottom: env(safe-area-inset-bottom, 0px);
  transform: translateY(100%);
  transition: transform 0.4s cubic-bezier(0.4,0,0.2,1);
  max-height: 92vh;
  overflow-y: auto;
}

.gate-sheet--open {
  transform: translateY(0);
}

.gate-sheet__handle {
  width: 40px;
  height: 4px;
  background: rgba(255,255,255,0.15);
  border-radius: 2px;
  margin: 14px auto 0;
}

.gate-sheet__inner {
  padding: 24px 32px 36px;
  max-width: 640px;
  margin: 0 auto;
  text-align: center;
}

.gate-sheet__icon {
  width: 52px;
  height: 52px;
  background: linear-gradient(135deg, #000744, #001199);
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
  font-size: 1.3rem;
  color: #fff;
}

.gate-sheet__title {
  font-family: 'Playfair Display', serif;
  font-size: 1.5rem;
  color: #fff;
  font-weight: 700;
  margin-bottom: 10px;
  line-height: 1.3;
}

.gate-sheet__subtitle {
  font-size: 0.88rem;
  color: rgba(255,255,255,0.5);
  line-height: 1.6;
  margin-bottom: 28px;
}

.gate-sheet__row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  margin-bottom: 10px;
}

.gate-sheet__field input {
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
  box-sizing: border-box;
  -webkit-appearance: none;
}

.gate-sheet__field input::placeholder { color: rgba(255,255,255,0.3); }
.gate-sheet__field input:focus { border-color: rgba(255,255,255,0.35); }

/* Checkboxes */
.gate-sheet__checks {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin: 16px 0 4px;
  text-align: left;
}

.gate-sheet__check {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  cursor: pointer;
}

.gate-sheet__check input[type="checkbox"] {
  width: 17px;
  height: 17px;
  margin-top: 2px;
  flex-shrink: 0;
  accent-color: #CC0031;
  cursor: pointer;
}

.gate-sheet__check span {
  font-size: 0.78rem;
  color: rgba(255,255,255,0.45);
  line-height: 1.5;
}

.gate-sheet__check a {
  color: rgba(255,255,255,0.7);
  text-decoration: underline;
}

/* Error */
.gate-sheet__error {
  background: rgba(204,0,49,0.15);
  border: 1px solid rgba(204,0,49,0.3);
  color: #ff6b8a;
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 0.83rem;
  margin: 10px 0 4px;
  text-align: left;
}

/* Submit button */
.gate-sheet__btn {
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
  margin-top: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: background 0.2s, transform 0.15s;
}

.gate-sheet__btn:hover:not(:disabled) {
  background: #a80028;
  transform: translateY(-1px);
}

.gate-sheet__btn:disabled { opacity: 0.7; cursor: not-allowed; }

.gate-sheet__privacy-note {
  font-size: 0.73rem;
  color: rgba(255,255,255,0.2);
  margin-top: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

/* Mobile */
@media (max-width: 600px) {
  .gate-bar { padding: 12px 16px; }
  .gate-bar__text span { display: none; }
  .gate-bar__text::after { content: 'Sblocca il viaggio completo'; color: rgba(255,255,255,0.85); font-size: 0.85rem; }
  .gate-sheet__inner { padding: 20px 20px 32px; }
  .gate-sheet__row { grid-template-columns: 1fr; gap: 8px; }
  .gate-sheet__title { font-size: 1.25rem; }
}
  </action>
  <verify>grep -n "gate-bar\|gate-sheet\|gate-overlay\|gated-content--hidden" assets/css/style.css — all new classes must be present; grep for old "#lead-gate" must return nothing</verify>
  <done>assets/css/style.css contains gate-bar, gate-overlay, gate-sheet classes with mobile media query; old #lead-gate CSS is gone</done>
</task>

</tasks>

<verification>
After all tasks complete:
1. grep -n "lead-gate\|#lead-gate" viaggio.php — must return 0 results
2. grep -n "gate-bar--visible\|gate-sheet--open\|IntersectionObserver" viaggio.php — must return results
3. grep -n "#lead-gate" assets/css/style.css — must return 0 results
4. grep -n "gate-sheet__btn\|gate-bar--visible\|gated-content--hidden" assets/css/style.css — must return results
</verification>

<success_criteria>
- viaggio.php: old LEAD GATE OVERLAY div deleted, new gate-bar + gate-overlay + gate-sheet HTML present before </body>
- viaggio.php: JS block uses IntersectionObserver on last visible .timeline-item; openSheet/closeSheet functions present; no reference to old overlay
- assets/css/style.css: old lead gate CSS replaced with gate-bar/gate-overlay/gate-sheet rules including .gated-content--hidden and mobile @media block
- No regressions: GATE object, STORAGE_KEY, and gated-content wrappers from Quick-17 are preserved and functional
</success_criteria>

<output>
After completion, create `.planning/quick/18-refactor-lead-gate-ux-sticky-bottom-bar-/18-SUMMARY.md`
</output>
