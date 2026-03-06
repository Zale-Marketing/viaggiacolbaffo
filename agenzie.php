<?php
require_once __DIR__ . '/includes/config.php';
require_once ROOT . '/includes/functions.php';

$page_title      = 'Diventa Agenzia Partner — Viaggia Col Baffo';
$meta_description = 'Unisciti alla rete di agenzie partner di Viaggia Col Baffo. Commissioni competitive, catalogo pronto da vendere, clienti sempre tuoi. Garanzia scritta.';

$hero_page = true;
require_once ROOT . '/includes/header.php';
?>

<!-- ========================================================
     HERO (B2B-01)
     ======================================================== -->
<section class="dest-hero">
  <img
    class="dest-hero__img"
    src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=1600&q=80"
    alt="Agenzie partner — Viaggia Col Baffo"
    loading="eager">
  <div class="dest-hero__overlay"></div>
  <div class="dest-hero__content container">
    <h1 class="dest-hero__title" style="font-size:clamp(2.5rem,6vw,4.5rem);margin-bottom:1rem;">Diventa Agenzia Partner</h1>
    <p style="color:rgba(255,255,255,0.85);font-size:1.15rem;max-width:620px;margin:0 0 2rem;line-height:1.7;">
      Cresciamo insieme — porta i tuoi clienti a scoprire il mondo con Lorenzo
    </p>
    <div style="display:flex;flex-wrap:wrap;gap:1rem;">
      <a href="#registration" class="btn btn--gold">Registrati ora</a>
      <a href="#come-funziona" class="btn btn--outline-white">Scopri come funziona</a>
    </div>
  </div>
</section>

<!-- ========================================================
     TRUST BAR (B2B-02)
     ======================================================== -->
<div class="b2b-trust-bar">
  <div class="container">
    <ul class="b2b-trust-bar__list">
      <li class="b2b-trust-bar__item"><i class="fa-solid fa-shield-halved"></i> Garanzia scritta</li>
      <li class="b2b-trust-bar__item"><i class="fa-solid fa-percent"></i> Commissioni competitive</li>
      <li class="b2b-trust-bar__item"><i class="fa-solid fa-headset"></i> Supporto dedicato</li>
      <li class="b2b-trust-bar__item"><i class="fa-solid fa-briefcase"></i> Materiali marketing inclusi</li>
    </ul>
  </div>
</div>

<!-- ========================================================
     VALUE PROPOSITION CARDS (B2B-03)
     ======================================================== -->
<section class="section section--dark">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Perché Scegliere Col Baffo</h2>
    </div>
    <div class="b2b-value-grid">

      <div class="b2b-value-card">
        <div class="b2b-value-card__icon"><i class="fa-solid fa-handshake"></i></div>
        <h3 class="b2b-value-card__title">I Tuoi Clienti Restano Tuoi</h3>
        <p class="b2b-value-card__text">
          Non contatteremo mai i tuoi clienti direttamente per proporre i nostri servizi.
          Se dovessero prenotare con noi in futuro, ti riconosceremo comunque la tua commissione — per iscritto.
        </p>
      </div>

      <div class="b2b-value-card">
        <div class="b2b-value-card__icon"><i class="fa-solid fa-coins"></i></div>
        <h3 class="b2b-value-card__title">Commissioni Competitive</h3>
        <p class="b2b-value-card__text">
          Guadagna una commissione su ogni prenotazione confermata.
          La percentuale esatta è definita per ogni viaggio — contattaci per i dettagli.
        </p>
      </div>

      <div class="b2b-value-card">
        <div class="b2b-value-card__icon"><i class="fa-solid fa-folder-open"></i></div>
        <h3 class="b2b-value-card__title">Catalogo Pronto da Vendere</h3>
        <p class="b2b-value-card__text">
          Accedi immediatamente al nostro catalogo completo di itinerari curati, materiali marketing,
          e schede viaggio pronte per la vendita.
        </p>
      </div>

    </div>
  </div>
</section>

<!-- ========================================================
     HOW IT WORKS (B2B-04)
     ======================================================== -->
<section class="section" id="come-funziona">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Come Funziona</h2>
    </div>
    <div class="b2b-steps">

      <div class="b2b-step">
        <div class="b2b-step__number">1</div>
        <h3 class="b2b-step__title">Registrati</h3>
        <p class="b2b-step__text">Compila il modulo qui sotto con i dati della tua agenzia. Risponderemo entro 24 ore.</p>
      </div>

      <div class="b2b-step">
        <div class="b2b-step__number">2</div>
        <h3 class="b2b-step__title">Ricevi il Catalogo</h3>
        <p class="b2b-step__text">Ti inviamo il catalogo completo con schede viaggio, prezzi e materiali di vendita pronti all'uso.</p>
      </div>

      <div class="b2b-step">
        <div class="b2b-step__number">3</div>
        <h3 class="b2b-step__title">Inizia a Guadagnare</h3>
        <p class="b2b-step__text">Ogni prenotazione confermata genera una commissione. Semplice, trasparente, garantito per iscritto.</p>
      </div>

    </div>
  </div>
</section>

<!-- ========================================================
     WRITTEN GUARANTEE (B2B-03 detail / B2B-02 expansion)
     ======================================================== -->
<section class="section section--dark">
  <div class="container" style="max-width:720px">
    <div class="b2b-guarantee">
      <h3 class="b2b-guarantee__title">I tuoi clienti restano tuoi, sempre</h3>
      <p class="b2b-guarantee__text">Non contatteremo mai direttamente i tuoi clienti per proporre i nostri servizi.</p>
      <p class="b2b-guarantee__text">Se in futuro un tuo cliente dovesse prenotare direttamente con noi (senza passare dalla tua agenzia), ti riconosceremo comunque la tua commissione.</p>
      <p class="b2b-guarantee__text" style="font-style:italic;color:rgba(255,255,255,0.6);font-size:0.9rem">— Lorenzo Baffo, Fondatore di Viaggia Col Baffo</p>
    </div>
  </div>
</section>

<!-- ========================================================
     AGENCY TESTIMONIAL (B2B-06)
     ======================================================== -->
<section class="section">
  <div class="container" style="max-width:720px">
    <!-- TODO: sostituire con testimonianza reale dopo il lancio -->
    <div class="testimonial-card">
      <div class="testimonial-card__stars">
        <i class="fa-solid fa-star"></i>
        <i class="fa-solid fa-star"></i>
        <i class="fa-solid fa-star"></i>
        <i class="fa-solid fa-star"></i>
        <i class="fa-solid fa-star"></i>
      </div>
      <p class="testimonial-card__text">
        "Lavoro con Viaggia Col Baffo da due anni. I miei clienti tornano sempre entusiasti e io ho la certezza di proporre qualcosa di eccellente. La commissione è puntuale e la collaborazione è trasparente fin dal primo giorno."
      </p>
      <div class="testimonial-card__author">
        <strong>Marco Ferretti</strong>
        <span>Agenzia Viaggi Ferretti, Milano</span>
      </div>
    </div>
  </div>
</section>

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

<?php require_once ROOT . '/includes/footer.php';
