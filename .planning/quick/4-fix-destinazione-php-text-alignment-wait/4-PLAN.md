---
quick: 4
type: execute
wave: 1
depends_on: []
files_modified:
  - destinazione.php
  - agenzie.php
autonomous: true
must_haves:
  truths:
    - "Intro paragraphs under 'Scopri [Destination]' are left-aligned with max-width 800px centered on page"
    - "Waitlist form (shown when no trips exist) uses navy gradient card with red icon, dark inputs, and red submit button"
    - "On successful waitlist submission the form is replaced by a checkmark + success message"
    - "agenzie.php #registration section contains the full multi-step partner form (Dati Agenzia, Referente, Consensi)"
    - "Navigating to destinazione.php?slug=unknownslug returns HTTP 404 and shows 'Pagina non trovata' — no redirect"
  artifacts:
    - path: "destinazione.php"
      provides: "Destination page with fixed alignment, redesigned waitlist, and 404 handling"
    - path: "agenzie.php"
      provides: "B2B page with inline partner registration form"
  key_links:
    - from: "destinazione.php waitlist form"
      to: "/api/submit-waitlist.php"
      via: "fetch POST"
    - from: "agenzie.php partner form"
      to: "https://connect.pabbly.com/workflow/sendwebhookdata/IjU3NjYwNTY0MDYzZjA0MzM1MjY1NTUzNzUxMzMi_pc"
      via: "fetch POST JSON"
---

<objective>
Fix four specific issues across destinazione.php and agenzie.php.

Purpose: Polish the destination and B2B pages — correct text alignment, replace off-brand waitlist form, replace Tally iframe with inline partner form, and return proper 404 for unknown slugs.
Output: Updated destinazione.php and agenzie.php — no other files touched.
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
  <name>Task 1: Fix destinazione.php — text alignment, waitlist form redesign, 404 for unknown slugs</name>
  <files>destinazione.php</files>
  <action>
Make three targeted changes to destinazione.php:

--- CHANGE 1: Text alignment in the editorial intro section (lines 56-65) ---

The section-header title is already centered via .section-header CSS.
The `.dest-intro` paragraphs need to be constrained and left-aligned for readability.
Add an inline wrapper div around the paragraphs:

Replace:
```php
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Scopri <?= htmlspecialchars($dest['name']) ?></h2>
    </div>
    <?php foreach ($dest['intro_paragraphs'] as $paragraph): ?>
      <p class="dest-intro"><?= htmlspecialchars($paragraph) ?></p>
    <?php endforeach; ?>
  </div>
</section>
```

With:
```php
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Scopri <?= htmlspecialchars($dest['name']) ?></h2>
    </div>
    <div style="max-width:800px;margin:0 auto;">
      <?php foreach ($dest['intro_paragraphs'] as $paragraph): ?>
        <p class="dest-intro" style="text-align:left;"><?= htmlspecialchars($paragraph) ?></p>
      <?php endforeach; ?>
    </div>
  </div>
</section>
```

--- CHANGE 2: Replace the waitlist form (the entire <?php else: ?> branch, lines 175-204) ---

Replace the entire else block (from `<?php else: ?>` through the closing `<?php endif; ?>` of that branch, keeping the `<?php if ($has_trips): ?>` branch intact) with:

```php
    <?php else: ?>

      <section class="b2b-form-section" style="padding:3rem 0;">
        <style>
          .waitlist-card {
            background: linear-gradient(135deg, #000744 0%, #000a66 100%);
            border-radius: 16px;
            padding: 3rem;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 20px 60px rgba(0,7,68,0.4);
          }
          .waitlist-card__title {
            font-family: 'Playfair Display', serif;
            color: #fff;
            text-align: center;
            font-size: 1.8rem;
            margin: 1rem 0 0.75rem;
          }
          .waitlist-card__sub {
            color: rgba(255,255,255,0.8);
            text-align: center;
            margin-bottom: 2rem;
            line-height: 1.6;
          }
          .waitlist-card .wl-label {
            display: block;
            color: rgba(255,255,255,0.7);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.4rem;
          }
          .waitlist-card .wl-input {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            border-radius: 8px;
            padding: 12px 16px;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 1rem;
            font-size: 1rem;
          }
          .waitlist-card .wl-input::placeholder { color: rgba(255,255,255,0.5); }
          .waitlist-card .wl-input:focus {
            border-color: #CC0031;
            outline: none;
          }
          .waitlist-card .wl-btn {
            background: #CC0031;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 14px 32px;
            width: 100%;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
          }
          .waitlist-card .wl-btn:hover {
            background: #a80028;
            transform: translateY(-2px);
          }
          .waitlist-card .wl-success {
            text-align: center;
            color: #fff;
            font-size: 1.1rem;
            padding: 1rem 0;
          }
          .waitlist-card .wl-success i {
            display: block;
            font-size: 3rem;
            color: #CC0031;
            margin-bottom: 1rem;
          }
        </style>

        <div class="waitlist-card">
          <i class="fas fa-map-marked-alt" style="font-size:3rem; color:#CC0031; margin-bottom:1rem; display:block; text-align:center"></i>
          <h2 class="waitlist-card__title">Nessun viaggio attivo per questa destinazione</h2>
          <p class="waitlist-card__sub">Vuoi che organizziamo qualcosa di speciale per te? Lasciaci i tuoi dati e ti contatteremo.</p>

          <form id="waitlist-form">
            <input type="hidden" name="destination_slug" value="<?= htmlspecialchars($slug) ?>">
            <input type="hidden" name="destination_name" value="<?= htmlspecialchars($dest['name']) ?>">

            <label class="wl-label" for="wl-nome">Nome</label>
            <input class="wl-input" type="text" id="wl-nome" name="nome" required placeholder="Il tuo nome">

            <label class="wl-label" for="wl-email">Email</label>
            <input class="wl-input" type="email" id="wl-email" name="email" required placeholder="La tua email">

            <label class="wl-label" for="wl-telefono">Telefono</label>
            <input class="wl-input" type="tel" id="wl-telefono" name="telefono" placeholder="Il tuo numero (opzionale)">

            <button type="submit" class="wl-btn">Iscriviti alla lista d&apos;attesa</button>
          </form>

          <div id="waitlist-success" class="wl-success" style="display:none;">
            <i class="fas fa-check-circle"></i>
            Perfetto! Ti contatteremo presto 🎉
          </div>
        </div>
      </section>

    <?php endif; ?>
```

Also remove the old inline `<script>` block at the bottom of the file (lines 210-244) and replace it with the updated script that handles the new success state:

```html
<script>
(function() {
  const form = document.getElementById('waitlist-form');
  if (!form) return;
  form.addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = this.querySelector('.wl-btn');
    btn.disabled = true;
    btn.textContent = 'Invio in corso...';

    const data = new FormData(this);
    try {
      const res = await fetch('/api/submit-waitlist.php', { method: 'POST', body: data });
      const json = await res.json();
      if (json.success) {
        form.style.display = 'none';
        const success = document.getElementById('waitlist-success');
        if (success) success.style.display = 'block';
      } else {
        throw new Error(json.error || 'Errore');
      }
    } catch (err) {
      btn.disabled = false;
      btn.textContent = "Iscriviti alla lista d'attesa";
      alert('Errore: ' + err.message);
    }
  });
})();
</script>
```

--- CHANGE 3: Fix 404 for unknown slugs (lines 6-11) ---

Replace the current redirect logic:
```php
$slug = $_GET['slug'] ?? '';
$valid_slugs = ['america', 'asia', 'europa', 'africa', 'oceania', 'medio-oriente'];
if (!$slug || !in_array($slug, $valid_slugs)) {
    header('Location: /404');
    exit;
}
```

With a proper 404 response (no redirect):
```php
$slug = $_GET['slug'] ?? '';
$valid_slugs = ['america', 'asia', 'europa', 'africa', 'oceania', 'medio-oriente'];
if (!$slug || !in_array($slug, $valid_slugs)) {
    http_response_code(404);
    $page_title = 'Pagina non trovata — Viaggia Col Baffo';
    require_once ROOT . '/includes/header.php';
    echo '<main style="text-align:center;padding:6rem 1rem;">';
    echo '<h1 style="font-family:\'Playfair Display\',serif;font-size:2.5rem;color:#000744;margin-bottom:1rem;">Pagina non trovata</h1>';
    echo '<p style="color:#555;margin-bottom:2rem;">La destinazione che cerchi non esiste.</p>';
    echo '<a href="/" class="btn btn--gold">Torna alla home</a>';
    echo '</main>';
    require_once ROOT . '/includes/footer.php';
    exit;
}
```

NOTE: The `$destinations` array comes from `includes/destinations-data.php` which is required at the top. The check against `$valid_slugs` already covers all valid keys, so the 404 branch will fire for any slug not in that list. The `$dest = $destinations[$slug]` line below is only reached when slug is valid.
  </action>
  <verify>
    <automated>grep -n "http_response_code(404)" /c/Users/Zanni/viaggiacolbaffo/destinazione.php && grep -n "waitlist-card" /c/Users/Zanni/viaggiacolbaffo/destinazione.php && grep -n "text-align:left" /c/Users/Zanni/viaggiacolbaffo/destinazione.php</automated>
  </verify>
  <done>
    - destinazione.php?slug=unknownslug returns HTTP 404 with "Pagina non trovata" (no redirect)
    - Intro paragraphs have text-align:left inside an 800px centered wrapper
    - Waitlist form uses navy gradient card styling with red submit button
    - On success the form hides and the success message shows
  </done>
</task>

<task type="auto">
  <name>Task 2: Replace agenzie.php registration form section with inline partner form</name>
  <files>agenzie.php</files>
  <action>
Replace the entire REGISTRATION FORM section of agenzie.php (the section from the comment `<!-- REGISTRATION FORM (B2B-05) -->` through to `</section>` that closes it, around lines 162-189) with the following complete section.

Keep everything before and after unchanged: hero, trust bar, value props, how-it-works, written guarantee, testimonial sections all stay exactly as they are.

Replace the registration section with:

```php
<!-- ========================================================
     REGISTRATION FORM (B2B-05) — inline partner form
     ======================================================== -->
<section class="b2b-form-section" id="registration">

<style>
.b2b-form-section {
  padding: 4rem 1rem;
  background: #f8f9fa;
}
.form-container {
  max-width: 860px;
  margin: 0 auto;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 8px 40px rgba(0,7,68,0.10);
  overflow: hidden;
}
.form-header {
  background: linear-gradient(135deg, #000744 0%, #000a66 100%);
  color: #fff;
  padding: 2.5rem 2.5rem 2rem;
  text-align: center;
}
.form-header h1 {
  font-family: 'Playfair Display', serif;
  font-size: 2rem;
  margin: 0 0 0.75rem;
}
.form-header p {
  color: rgba(255,255,255,0.82);
  max-width: 560px;
  margin: 0 auto;
  line-height: 1.6;
  font-size: 1rem;
}
.guarantee-section {
  display: flex;
  gap: 1.25rem;
  align-items: flex-start;
  background: #fff8e1;
  border-left: 4px solid #CC0031;
  padding: 1.5rem 2rem;
  margin: 0;
}
.guarantee-icon { font-size: 2rem; flex-shrink: 0; }
.guarantee-content h3 { font-size: 1rem; font-weight: 700; color: #000744; margin: 0 0 0.5rem; }
.guarantee-content p { font-size: 0.9rem; color: #444; margin: 0 0 0.5rem; line-height: 1.55; }
.guarantee-content p:last-child { margin-bottom: 0; }
.benefits-bar {
  display: flex;
  gap: 1rem;
  background: #000744;
  padding: 1rem 2rem;
  flex-wrap: wrap;
}
.benefit-item {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  color: rgba(255,255,255,0.85);
  font-size: 0.9rem;
}
.benefit-item .icon { font-size: 1.1rem; }
.form-content {
  padding: 2rem 2.5rem;
}
.form-content.last-section { padding-bottom: 2.5rem; }
.form-section {
  margin-bottom: 2rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid #e9ecef;
}
.form-section:last-of-type { border-bottom: none; margin-bottom: 0; }
.section-title {
  font-size: 1.15rem;
  font-weight: 700;
  color: #000744;
  margin: 0 0 0.25rem;
}
.section-description {
  font-size: 0.88rem;
  color: #777;
  margin: 0 0 1.25rem;
}
.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-bottom: 0;
}
.form-row.single { grid-template-columns: 1fr; }
.form-row.triple { grid-template-columns: 2fr 1fr 1fr; }
@media (max-width: 640px) {
  .form-row, .form-row.triple { grid-template-columns: 1fr; }
  .form-header, .form-content { padding: 1.5rem; }
  .guarantee-section { padding: 1.25rem 1.5rem; }
}
.form-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 1rem;
}
.form-group label {
  font-size: 0.85rem;
  font-weight: 600;
  color: #333;
  margin-bottom: 0.35rem;
}
.required { color: #CC0031; }
.field-hint {
  font-size: 0.78rem;
  color: #888;
  margin-top: 0.3rem;
}
.form-group input,
.form-group select,
.form-group textarea {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 10px 14px;
  font-size: 0.95rem;
  color: #222;
  background: #fafafa;
  transition: border-color 0.2s;
  width: 100%;
  box-sizing: border-box;
}
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  border-color: #000744;
  outline: none;
  background: #fff;
}
.form-group textarea { min-height: 100px; resize: vertical; }
.radio-group {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}
.radio-option {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  background: #f0f2f8;
  border: 1px solid #dde1f0;
  border-radius: 8px;
  padding: 8px 14px;
  cursor: pointer;
  font-size: 0.9rem;
  color: #333;
  transition: border-color 0.2s, background 0.2s;
}
.radio-option input { margin: 0; cursor: pointer; }
.radio-option:has(input:checked) {
  background: #000744;
  border-color: #000744;
  color: #fff;
}
.checkbox-group {
  display: flex;
  gap: 0.75rem;
  align-items: flex-start;
  margin-bottom: 1rem;
  background: #f8f9fa;
  border-radius: 8px;
  padding: 1rem;
}
.checkbox-group input[type=checkbox] {
  width: 18px;
  height: 18px;
  flex-shrink: 0;
  margin-top: 2px;
  accent-color: #000744;
  cursor: pointer;
}
.checkbox-group label {
  font-size: 0.88rem;
  color: #333;
  cursor: pointer;
  margin: 0;
  font-weight: 400;
}
.checkbox-group label strong { display: block; margin-bottom: 0.2rem; }
.checkbox-group label small { color: #777; font-size: 0.82rem; }
.submit-btn {
  background: #CC0031;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 14px 32px;
  width: 100%;
  font-size: 1.05rem;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.2s, transform 0.2s;
  margin-top: 0.5rem;
}
.submit-btn:hover { background: #a80028; transform: translateY(-2px); }
.submit-btn:disabled { background: #aaa; cursor: not-allowed; transform: none; }
.privacy-note {
  font-size: 0.82rem;
  color: #888;
  text-align: center;
  margin-top: 0.75rem;
}
.message {
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1rem;
  display: none;
  font-size: 0.95rem;
}
.message.success { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; display: block; }
.message.error   { background: #ffebee; color: #c62828; border: 1px solid #ef9a9a; display: block; }
.loading { display: none; text-align: center; padding: 1rem; }
.loading.active { display: block; }
.spinner {
  width: 36px; height: 36px;
  border: 3px solid #e0e0e0;
  border-top-color: #000744;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  margin: 0 auto 0.5rem;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>

<div class="form-container">
    <div class="form-header">
        <h1>Diventa Partner</h1>
        <p>Unisciti alla nostra rete di agenzie partner e offri ai tuoi clienti esperienze di viaggio uniche con commissioni competitive</p>
    </div>
    <div class="guarantee-section">
        <div class="guarantee-icon">🛡️</div>
        <div class="guarantee-content">
            <h3>Garanzia Clienti: I Tuoi Clienti Restano Tuoi, Sempre</h3>
            <p>Sappiamo quanto sia importante per te proteggere il rapporto con i tuoi clienti. Per questo ti garantiamo per iscritto che <strong>non contatteremo mai direttamente i tuoi clienti</strong> per proporre i nostri servizi.</p>
            <p>E c'è di più: se in futuro un tuo cliente dovesse prenotare direttamente con noi (senza passare dalla tua agenzia), <strong>ti riconosceremo comunque la tua commissione</strong>. Il tuo lavoro di fidelizzazione viene sempre rispettato e premiato.</p>
        </div>
    </div>
    <div class="benefits-bar">
        <div class="benefit-item"><span class="icon">🎯</span><span>Supporto dedicato</span></div>
        <div class="benefit-item"><span class="icon">⚡</span><span>Preventivi in 24h</span></div>
    </div>
    <div class="form-content last-section">
        <form id="partnerForm">
            <div class="form-section">
                <h2 class="section-title">Dati Agenzia</h2>
                <p class="section-description">Inserisci i dati ufficiali della tua agenzia di viaggi</p>
                <div class="form-row">
                    <div class="form-group">
                        <label for="ragioneSociale">Ragione Sociale <span class="required">*</span></label>
                        <input type="text" id="ragioneSociale" name="ragioneSociale" required placeholder="Es. Viaggi Rossi S.r.l.">
                    </div>
                    <div class="form-group">
                        <label for="nomeCommerciale">Nome Commerciale <span class="required">*</span></label>
                        <input type="text" id="nomeCommerciale" name="nomeCommerciale" required placeholder="Es. Agenzia Viaggi Rossi">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="partitaIva">Partita IVA <span class="required">*</span></label>
                        <input type="text" id="partitaIva" name="partitaIva" required placeholder="IT12345678901" maxlength="13">
                    </div>
                    <div class="form-group">
                        <label for="codiceFiscale">Codice Fiscale <span class="required">*</span></label>
                        <input type="text" id="codiceFiscale" name="codiceFiscale" required placeholder="12345678901">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="codiceUnivoco">Codice Univoco (SDI) <span class="required">*</span></label>
                        <input type="text" id="codiceUnivoco" name="codiceUnivoco" required placeholder="Es. A1B2C3D" maxlength="7">
                        <span class="field-hint">Codice destinatario per fatturazione elettronica (7 caratteri)</span>
                    </div>
                    <div class="form-group">
                        <label for="licenza">Numero Licenza/Autorizzazione</label>
                        <input type="text" id="licenza" name="licenza" placeholder="Es. 12345/2020">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="annoFondazione">Anno di Fondazione <span class="required">*</span></label>
                        <input type="number" id="annoFondazione" name="annoFondazione" required min="1900" max="2025" placeholder="Es. 2010">
                    </div>
                    <div class="form-group">
                        <label for="fondoGaranzia">Fondo di Garanzia</label>
                        <input type="text" id="fondoGaranzia" name="fondoGaranzia" placeholder="Es. Fondo Garanzia Turismo">
                        <span class="field-hint">Richiesto per legge - comunicalo successivamente se non disponibile ora</span>
                    </div>
                </div>
                <div class="form-row single">
                    <div class="form-group">
                        <label for="indirizzo">Indirizzo Sede <span class="required">*</span></label>
                        <input type="text" id="indirizzo" name="indirizzo" required placeholder="Via Roma 123">
                    </div>
                </div>
                <div class="form-row triple">
                    <div class="form-group">
                        <label for="citta">Città <span class="required">*</span></label>
                        <input type="text" id="citta" name="citta" required placeholder="Roma">
                    </div>
                    <div class="form-group">
                        <label for="cap">CAP <span class="required">*</span></label>
                        <input type="text" id="cap" name="cap" required placeholder="00100" maxlength="5">
                    </div>
                    <div class="form-group">
                        <label for="provincia">Provincia <span class="required">*</span></label>
                        <input type="text" id="provincia" name="provincia" required placeholder="RM" maxlength="2">
                    </div>
                </div>
            </div>
            <div class="form-section">
                <h2 class="section-title">Referente Principale</h2>
                <p class="section-description">La persona che gestirà la partnership con noi</p>
                <div class="form-row">
                    <div class="form-group">
                        <label for="nomeReferente">Nome <span class="required">*</span></label>
                        <input type="text" id="nomeReferente" name="nomeReferente" required>
                    </div>
                    <div class="form-group">
                        <label for="cognomeReferente">Cognome <span class="required">*</span></label>
                        <input type="text" id="cognomeReferente" name="cognomeReferente" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="ruolo">Ruolo in Agenzia <span class="required">*</span></label>
                        <select id="ruolo" name="ruolo" required>
                            <option value="">Seleziona...</option>
                            <option value="titolare">Titolare</option>
                            <option value="direttore">Direttore</option>
                            <option value="responsabile_vendite">Responsabile Vendite</option>
                            <option value="agente">Agente di Viaggio</option>
                            <option value="altro">Altro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Telefono Diretto <span class="required">*</span></label>
                        <input type="tel" id="telefono" name="telefono" required placeholder="+39 333 1234567">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required placeholder="nome@agenzia.it">
                    </div>
                    <div class="form-group">
                        <label for="emailPec">PEC Agenzia</label>
                        <input type="email" id="emailPec" name="emailPec" placeholder="agenzia@pec.it">
                    </div>
                </div>
            </div>
            <div class="form-section">
                <h2 class="section-title">Come ci hai conosciuto?</h2>
                <div class="form-group">
                    <div class="radio-group" id="comeConosciuto">
                        <label class="radio-option"><input type="radio" name="comeConosciuto" value="social"><span>Social Media</span></label>
                        <label class="radio-option"><input type="radio" name="comeConosciuto" value="google"><span>Ricerca Google</span></label>
                        <label class="radio-option"><input type="radio" name="comeConosciuto" value="passaparola"><span>Passaparola</span></label>
                        <label class="radio-option"><input type="radio" name="comeConosciuto" value="commerciale"><span>Commerciale</span></label>
                        <label class="radio-option"><input type="radio" name="comeConosciuto" value="altro"><span>Altro</span></label>
                    </div>
                </div>
                <div class="form-row single" style="margin-top:20px;">
                    <div class="form-group">
                        <label for="note">Note o Richieste Particolari</label>
                        <textarea id="note" name="note" placeholder="Raccontaci di più sulla tua agenzia..."></textarea>
                    </div>
                </div>
            </div>
            <div class="form-section">
                <h2 class="section-title">Consensi e Privacy</h2>
                <div class="checkbox-group">
                    <input type="checkbox" id="accettaTermini" name="accettaTermini" required>
                    <label for="accettaTermini"><strong>Accetto i Termini e Condizioni della Partnership <span class="required">*</span></strong><small>Ho letto e accetto i termini e condizioni del programma partner</small></label>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="accettaPrivacy" name="accettaPrivacy" required>
                    <label for="accettaPrivacy"><strong>Acconsento al trattamento dei dati <span class="required">*</span></strong><small>Ho letto e accetto la privacy policy ai sensi del GDPR</small></label>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="accettaNewsletter" name="accettaNewsletter">
                    <label for="accettaNewsletter"><strong>Voglio ricevere aggiornamenti e promozioni</strong><small>Ricevi news su nuove destinazioni, offerte speciali e materiali marketing</small></label>
                </div>
            </div>
            <div class="message" id="partnerMessage"></div>
            <div class="loading" id="partnerLoading"><div class="spinner"></div><p>Invio richiesta in corso...</p></div>
            <button type="submit" class="submit-btn" id="submitBtn">Invia Richiesta di Partnership</button>
            <p class="privacy-note">I tuoi dati sono al sicuro. Riceverai una risposta entro 48 ore lavorative.</p>
        </form>
    </div>
</div>

<script>
(function() {
  const WEBHOOK_URL = 'https://connect.pabbly.com/workflow/sendwebhookdata/IjU3NjYwNTY0MDYzZjA0MzM1MjY1NTUzNzUxMzMi_pc';

  const form = document.getElementById('partnerForm');
  const msgEl = document.getElementById('partnerMessage');
  const loadingEl = document.getElementById('partnerLoading');
  const submitBtn = document.getElementById('submitBtn');

  if (!form) return;

  form.addEventListener('submit', async function(e) {
    e.preventDefault();

    submitBtn.disabled = true;
    loadingEl.classList.add('active');
    msgEl.className = 'message';
    msgEl.textContent = '';

    const formData = new FormData(this);
    const payload = {};
    formData.forEach((v, k) => { payload[k] = v; });

    // Collect radio value
    const radio = form.querySelector('input[name="comeConosciuto"]:checked');
    if (radio) payload['comeConosciuto'] = radio.value;

    try {
      const res = await fetch(WEBHOOK_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      if (res.ok) {
        loadingEl.classList.remove('active');
        form.style.display = 'none';
        msgEl.className = 'message success';
        msgEl.textContent = 'Richiesta inviata con successo! Ti contatteremo entro 48 ore lavorative.';
      } else {
        throw new Error('Risposta non valida dal server');
      }
    } catch (err) {
      loadingEl.classList.remove('active');
      msgEl.className = 'message error';
      msgEl.textContent = 'Errore durante l\'invio. Riprova o contattaci direttamente.';
      submitBtn.disabled = false;
    }
  });
})();
</script>

</section>
```

Do NOT touch any other section of agenzie.php. The hero, trust bar, value props, how-it-works, written guarantee, and testimonial sections must remain exactly as they are.
  </action>
  <verify>
    <automated>grep -n "partnerForm" /c/Users/Zanni/viaggiacolbaffo/agenzie.php && grep -n "pabbly.com" /c/Users/Zanni/viaggiacolbaffo/agenzie.php && grep -n "ragioneSociale" /c/Users/Zanni/viaggiacolbaffo/agenzie.php</automated>
  </verify>
  <done>
    - agenzie.php #registration section contains the full inline form (no Tally iframe)
    - Form POSTs JSON to the Pabbly webhook URL on submit
    - Loading spinner shows during submission, success message replaces form on success
    - All other page sections (hero, trust bar, value props, steps, guarantee, testimonial) unchanged
  </done>
</task>

<task type="auto">
  <name>Task 3: Commit all changes</name>
  <files></files>
  <action>
Stage and commit both modified files with the exact commit message specified:

```bash
cd /c/Users/Zanni/viaggiacolbaffo && git add destinazione.php agenzie.php && git commit -m "fix: destinazione text alignment, waitlist form redesign, agenzie B2B form, 404 for unknown slugs"
```

Do not use `git add -A` — only add the two files that were modified.
  </action>
  <verify>
    <automated>cd /c/Users/Zanni/viaggiacolbaffo && git log --oneline -1</automated>
  </verify>
  <done>Git log shows the commit with the exact message above as the most recent commit.</done>
</task>

</tasks>

<verification>
After all tasks complete:
1. grep confirms `http_response_code(404)` is in destinazione.php (ISSUE 4)
2. grep confirms `text-align:left` is in the intro wrapper in destinazione.php (ISSUE 1)
3. grep confirms `waitlist-card` class and `linear-gradient(135deg, #000744` are in destinazione.php (ISSUE 2)
4. grep confirms `partnerForm` and `pabbly.com` webhook URL are in agenzie.php (ISSUE 3)
5. git log shows the commit with the required message
</verification>

<success_criteria>
- destinazione.php?slug=america: intro paragraphs are left-aligned within an 800px centered container
- destinazione.php?slug=africa (no trips): shows navy gradient waitlist card, not the old plain form
- destinazione.php?slug=unknownslug: HTTP 404 response with "Pagina non trovata" page — no redirect
- agenzie.php#registration: shows the full inline multi-step partner form, not a Tally iframe or WhatsApp fallback
- One git commit containing both file changes with the specified commit message
</success_criteria>

<output>
After completion, create `.planning/quick/4-fix-destinazione-php-text-alignment-wait/4-SUMMARY.md`
</output>
