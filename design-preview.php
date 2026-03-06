<?php
require_once __DIR__ . '/includes/config.php';
$page_title = 'Design Preview - Phase 1';
require_once ROOT . '/includes/header.php';
?>

<!-- WARNING BANNER -->
<div style="background:#f39c12;color:#000;text-align:center;padding:0.75rem 1.5rem;font-weight:700;font-family:sans-serif;">
  DESIGN PREVIEW &mdash; Phase 1 validation page. Delete or protect after sign-off.
</div>

<!-- 1. COLOR SWATCHES -->
<section class="section container">
  <div class="section-header">
    <h2 class="section-header__title">Color Tokens</h2>
    <p class="section-header__subtitle">All CSS custom property values</p>
  </div>
  <div style="display:flex;flex-wrap:wrap;gap:1.5rem;justify-content:center;">

    <div style="text-align:center;">
      <div style="width:80px;height:80px;background:#000744;border-radius:8px;"></div>
      <p style="margin:0.5rem 0 0;font-size:0.75rem;">--primary / --black<br>#000744</p>
    </div>

    <div style="text-align:center;">
      <div style="width:80px;height:80px;background:#CC0031;border-radius:8px;"></div>
      <p style="margin:0.5rem 0 0;font-size:0.75rem;">--accent / --gold<br>#CC0031</p>
    </div>

    <div style="text-align:center;">
      <div style="width:80px;height:80px;background:#FFFFFF;border-radius:8px;border:1px solid #ccc;"></div>
      <p style="margin:0.5rem 0 0;font-size:0.75rem;">--white<br>#FFFFFF</p>
    </div>

    <div style="text-align:center;">
      <div style="width:80px;height:80px;background:#111827;border-radius:8px;"></div>
      <p style="margin:0.5rem 0 0;font-size:0.75rem;">--dark<br>#111827</p>
    </div>

    <div style="text-align:center;">
      <div style="width:80px;height:80px;background:#1a1f3e;border-radius:8px;"></div>
      <p style="margin:0.5rem 0 0;font-size:0.75rem;">--dark-card<br>#1a1f3e</p>
    </div>

    <div style="text-align:center;">
      <div style="width:80px;height:80px;background:#888888;border-radius:8px;"></div>
      <p style="margin:0.5rem 0 0;font-size:0.75rem;">--grey<br>#888888</p>
    </div>

    <div style="text-align:center;">
      <div style="width:80px;height:80px;background:#2ecc71;border-radius:8px;"></div>
      <p style="margin:0.5rem 0 0;font-size:0.75rem;">--status-green<br>#2ecc71</p>
    </div>

    <div style="text-align:center;">
      <div style="width:80px;height:80px;background:#e67e22;border-radius:8px;"></div>
      <p style="margin:0.5rem 0 0;font-size:0.75rem;">--status-orange<br>#e67e22</p>
    </div>

    <div style="text-align:center;">
      <div style="width:80px;height:80px;background:#CC0031;border-radius:8px;"></div>
      <p style="margin:0.5rem 0 0;font-size:0.75rem;">--status-red<br>#CC0031</p>
    </div>

  </div>
</section>

<!-- 2. TYPOGRAPHY SCALE -->
<section class="section section--dark">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Typography Scale</h2>
      <p class="section-header__subtitle">Playfair Display (headings) &amp; Inter (body)</p>
    </div>

    <p style="font-size:0.75rem;color:var(--grey);margin-bottom:0.25rem;">H1 — Playfair Display 400</p>
    <h1 style="font-family:var(--font-heading);margin-bottom:1.5rem;">Viaggia col Baffo: Esperienze Autentiche</h1>

    <p style="font-size:0.75rem;color:var(--grey);margin-bottom:0.25rem;">H2 — Playfair Display 700</p>
    <h2 style="font-family:var(--font-heading);margin-bottom:1.5rem;">I Nostri Viaggi Esclusivi</h2>

    <p style="font-size:0.75rem;color:var(--grey);margin-bottom:0.25rem;">H3 — Playfair Display</p>
    <h3 style="font-family:var(--font-heading);margin-bottom:1.5rem;">West America Aprile 2026</h3>

    <p style="font-size:0.75rem;color:var(--grey);margin-bottom:0.25rem;">H4 — Playfair Display</p>
    <h4 style="font-family:var(--font-heading);margin-bottom:1.5rem;">Giorno 1: Arrivo a Los Angeles</h4>

    <p style="font-size:0.75rem;color:var(--grey);margin-bottom:0.25rem;">Body — Inter 400</p>
    <p style="font-family:var(--font-body);">Ogni viaggio con Lorenzo &egrave; un'esperienza unica, vissuta con la passione di chi ama scoprire il mondo in modo autentico. Non sei un turista &mdash; sei un viaggiatore.</p>
  </div>
</section>

<!-- 3. SECTION HEADER COMPONENT -->
<section class="section container">
  <p style="font-size:0.75rem;color:var(--grey);text-align:center;margin-bottom:1rem;">Section Header Component (with gold ::after underline)</p>
  <div class="section-header">
    <h2 class="section-header__title">I Nostri Viaggi</h2>
    <p class="section-header__subtitle">Ogni viaggio e' un'esperienza unica con Lorenzo</p>
  </div>
</section>

<!-- 4. BUTTONS -->
<section class="section section--dark">
  <div class="container" style="text-align:center;">
    <div class="section-header">
      <h2 class="section-header__title">Buttons</h2>
      <p class="section-header__subtitle">All button variants — never use red as a primary action</p>
    </div>
    <p style="font-size:0.75rem;color:var(--grey);margin-bottom:1rem;">On dark background:</p>
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;align-items:flex-start;margin-bottom:2rem;">
      <a href="#" class="btn btn-primary">Scopri il viaggio</a>
      <a href="#" class="btn btn--outline-white">Richiedi info</a>
      <div style="text-align:center;">
        <a href="#" class="btn btn-accent">Urgenza (uso limitato)</a>
        <p style="font-size:0.75rem;color:#888;margin-top:8px;">Solo per banner urgenza — mai come CTA principale</p>
      </div>
    </div>
    <p style="font-size:0.75rem;color:var(--grey);margin-bottom:1rem;">On white background:</p>
    <div style="background:#fff;padding:1.5rem;border-radius:8px;display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
      <a href="#" class="btn btn-primary">Scopri il viaggio</a>
      <a href="#" class="btn btn-secondary">Richiedi info</a>
    </div>
  </div>
</section>

<!-- 4b. STATUS PILLS -->
<section class="section container">
  <div class="section-header">
    <h2 class="section-header__title">Status Pills</h2>
    <p class="section-header__subtitle">Trip availability badges — sold-out is grey, not red</p>
  </div>
  <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
    <span class="status-pill status-confermata">Confermata</span>
    <span class="status-pill status-ultimi-posti">Ultimi Posti</span>
    <span class="status-pill status-sold-out">Sold Out</span>
    <span class="status-pill status-programmata">Programmata</span>
  </div>
</section>

<!-- 5. SINGLE TRIP CARD -->
<section class="section container">
  <div class="section-header">
    <h2 class="section-header__title">Trip Card (Single)</h2>
    <p class="section-header__subtitle">Max-width 380px to show actual card proportions</p>
  </div>
  <div style="max-width:380px;margin:0 auto;">
    <div class="trip-card">
      <img class="trip-card__image" src="https://images.unsplash.com/photo-1549880338-65ddcdfd017b?w=800" alt="West America">
      <div class="trip-card__overlay"></div>
      <span class="trip-card__continent">America</span>
      <span class="trip-card__status status--ultimi-posti">Ultimi Posti</span>
      <div class="trip-card__content">
        <h3 class="trip-card__title">West America Aprile 2026</h3>
        <p class="trip-card__dates">5 Apr - 19 Apr 2026</p>
        <p class="trip-card__price">Da 3.490 &euro;</p>
        <a class="trip-card__cta btn btn--gold" href="/viaggio/west-america-aprile-2026">Scopri il viaggio</a>
      </div>
    </div>
  </div>
</section>

<!-- 6. TRIP GRID — 3 cards, all status states -->
<section class="section section--dark">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Trip Grid</h2>
      <p class="section-header__subtitle">1-column mobile &rarr; 2-column tablet &rarr; 3-column desktop</p>
    </div>
    <div class="trip-grid">

      <!-- Card 1: Confermata (green) -->
      <div class="trip-card">
        <img class="trip-card__image" src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800" alt="Giappone">
        <div class="trip-card__overlay"></div>
        <span class="trip-card__continent">Asia</span>
        <span class="trip-card__status status--confermata">Confermata</span>
        <div class="trip-card__content">
          <h3 class="trip-card__title">Giappone Sakura 2026</h3>
          <p class="trip-card__dates">28 Mar - 11 Apr 2026</p>
          <p class="trip-card__price">Da 4.290 &euro;</p>
          <a class="trip-card__cta btn btn--gold" href="/viaggio/giappone-sakura-2026">Scopri il viaggio</a>
        </div>
      </div>

      <!-- Card 2: Ultimi Posti (orange) -->
      <div class="trip-card">
        <img class="trip-card__image" src="https://images.unsplash.com/photo-1549880338-65ddcdfd017b?w=800" alt="West America">
        <div class="trip-card__overlay"></div>
        <span class="trip-card__continent">America</span>
        <span class="trip-card__status status--ultimi-posti">Ultimi Posti</span>
        <div class="trip-card__content">
          <h3 class="trip-card__title">West America Aprile 2026</h3>
          <p class="trip-card__dates">5 Apr - 19 Apr 2026</p>
          <p class="trip-card__price">Da 3.490 &euro;</p>
          <a class="trip-card__cta btn btn--gold" href="/viaggio/west-america-aprile-2026">Scopri il viaggio</a>
        </div>
      </div>

      <!-- Card 3: Sold Out (red) -->
      <div class="trip-card">
        <img class="trip-card__image" src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=800" alt="Marocco">
        <div class="trip-card__overlay"></div>
        <span class="trip-card__continent">Africa</span>
        <span class="trip-card__status status--sold-out">Sold Out</span>
        <div class="trip-card__content">
          <h3 class="trip-card__title">Marocco Febbraio 2026</h3>
          <p class="trip-card__dates">10 Feb - 20 Feb 2026</p>
          <p class="trip-card__price">Da 2.190 &euro;</p>
          <a class="trip-card__cta btn btn--gold" href="/viaggio/marocco-febbraio-2026">Scopri il viaggio</a>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- 7. FONT AWESOME ICONS -->
<section class="section container">
  <div class="section-header">
    <h2 class="section-header__title">Font Awesome Icons</h2>
    <p class="section-header__subtitle">All icons used across the project</p>
  </div>
  <div style="display:flex;flex-wrap:wrap;gap:2rem;justify-content:center;font-size:1.5rem;">
    <div style="text-align:center;">
      <i class="fa-solid fa-check"></i>
      <p style="font-size:0.75rem;margin:0.5rem 0 0;">fa-check</p>
    </div>
    <div style="text-align:center;">
      <i class="fa-solid fa-xmark"></i>
      <p style="font-size:0.75rem;margin:0.5rem 0 0;">fa-xmark</p>
    </div>
    <div style="text-align:center;">
      <i class="fa-brands fa-whatsapp"></i>
      <p style="font-size:0.75rem;margin:0.5rem 0 0;">fa-whatsapp</p>
    </div>
    <div style="text-align:center;">
      <i class="fa-solid fa-location-dot"></i>
      <p style="font-size:0.75rem;margin:0.5rem 0 0;">fa-location-dot</p>
    </div>
    <div style="text-align:center;">
      <i class="fa-solid fa-calendar"></i>
      <p style="font-size:0.75rem;margin:0.5rem 0 0;">fa-calendar</p>
    </div>
    <div style="text-align:center;">
      <i class="fa-solid fa-user"></i>
      <p style="font-size:0.75rem;margin:0.5rem 0 0;">fa-user</p>
    </div>
    <div style="text-align:center;">
      <i class="fa-solid fa-star"></i>
      <p style="font-size:0.75rem;margin:0.5rem 0 0;">fa-star</p>
    </div>
    <div style="text-align:center;">
      <i class="fa-solid fa-chevron-down"></i>
      <p style="font-size:0.75rem;margin:0.5rem 0 0;">fa-chevron-down</p>
    </div>
  </div>
</section>

<?php require_once ROOT . '/includes/footer.php'; ?>
