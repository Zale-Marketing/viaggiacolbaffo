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
     REGISTRATION FORM (B2B-05)
     ======================================================== -->
<section class="section section--dark" id="registration">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Registra la Tua Agenzia</h2>
    </div>
    <div class="b2b-tally-wrap">
      <?php if (defined('TALLY_B2B_URL') && TALLY_B2B_URL): ?>
        <iframe src="<?= htmlspecialchars(TALLY_B2B_URL) ?>"
                width="100%" style="min-height:500px;border:none;" frameborder="0"
                title="Modulo registrazione agenzia partner">
        </iframe>
      <?php elseif (defined('WHATSAPP_B2B_FALLBACK') && WHATSAPP_B2B_FALLBACK): ?>
        <div style="text-align:center">
          <p style="color:rgba(255,255,255,0.75);margin-bottom:1.5rem">Scrivici su WhatsApp per avviare la collaborazione. Risponderemo entro 24 ore.</p>
          <a href="<?= htmlspecialchars(WHATSAPP_B2B_FALLBACK) ?>"
             class="btn btn--gold" target="_blank" rel="noopener">
            <i class="fa-brands fa-whatsapp"></i> Contattaci su WhatsApp
          </a>
        </div>
      <?php else: ?>
        <div style="text-align:center">
          <p style="color:rgba(255,255,255,0.75);margin-bottom:1.5rem">Per registrarti come agenzia partner, scrivici all'indirizzo email indicato nel footer.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require_once ROOT . '/includes/footer.php';
