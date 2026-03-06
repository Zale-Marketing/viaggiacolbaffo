<?php
require_once __DIR__ . '/includes/config.php';
require_once ROOT . '/includes/functions.php';

// --- Slug routing ---
$slug = $_GET['slug'] ?? '';
if (!$slug) { header("Location: /404"); exit; }

$trip = get_trip_by_slug($slug);
if (!$trip) { header("Location: /404"); exit; }

// --- Page variables ---
$page_title = htmlspecialchars($trip['title']) . ' — Viaggia col Baffo';
$hero_page  = true;

// Status display label
$status_labels = [
    'confermata'   => 'Confermata',
    'ultimi-posti' => 'Ultimi posti',
    'sold-out'     => 'Sold out',
    'programmata'  => 'In programmazione',
];
$status_label = $status_labels[$trip['status']] ?? ucfirst($trip['status']);

// Date formatting helpers
function fmt_date(string $d): string {
    $months = ['', 'Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'];
    [$y, $m, $day] = explode('-', $d);
    return "$day {$months[(int)$m]} $y";
}
$date_display = fmt_date($trip['date_start']) . ' – ' . fmt_date($trip['date_end']);

$form_config = $trip['form_config'] ?? [];
$has_form    = !empty($form_config);

require_once ROOT . '/includes/header.php';
?>

<main>

<!-- ========================================================
     TRIP HERO
     ======================================================== -->
<div class="trip-hero" style="background-image:url('<?php echo htmlspecialchars($trip['hero_image']); ?>')">
  <div class="trip-hero__overlay"></div>
  <div class="trip-hero__content container">
    <div class="status-pill status-<?php echo htmlspecialchars($trip['status']); ?>" style="display:inline-block;margin-bottom:0.75rem;">
      <?php echo htmlspecialchars($status_label); ?>
    </div>
    <h1 class="trip-hero__title"><?php echo htmlspecialchars($trip['title']); ?></h1>
    <div class="trip-hero__meta">
      <span><i class="fa-regular fa-calendar"></i> <?php echo htmlspecialchars($date_display); ?></span>
      <span><i class="fa-regular fa-clock"></i> <?php echo htmlspecialchars($trip['duration']); ?></span>
      <span><i class="fa-solid fa-tag"></i> Da €<?php echo number_format($trip['price_from'], 0, ',', '.'); ?> p.p.</span>
    </div>
    <?php if ($has_form): ?>
    <a href="#richiedi-preventivo" class="trip-hero__cta btn">Richiedi Preventivo</a>
    <?php endif; ?>
  </div>
</div>

<!-- ========================================================
     STICKY TOP BAR (appears after hero on scroll)
     ======================================================== -->
<div class="trip-topbar" id="trip-topbar">
  <span class="trip-topbar__name"><?php echo htmlspecialchars($trip['title']); ?></span>
  <?php if ($has_form): ?>
  <a href="#richiedi-preventivo" class="trip-topbar__cta btn btn--gold">Richiedi Preventivo</a>
  <?php endif; ?>
</div>

<!-- ========================================================
     HIGHLIGHTS BAR
     ======================================================== -->
<div class="trip-highlights">
  <div class="trip-highlights__grid">
    <div class="trip-highlights__item">
      <div class="trip-highlights__label">Date</div>
      <div class="trip-highlights__value"><?php echo htmlspecialchars($date_display); ?></div>
    </div>
    <div class="trip-highlights__item">
      <div class="trip-highlights__label">Durata</div>
      <div class="trip-highlights__value"><?php echo htmlspecialchars($trip['duration']); ?></div>
    </div>
    <div class="trip-highlights__item">
      <div class="trip-highlights__label">Prezzo da</div>
      <div class="trip-highlights__value">€<?php echo number_format($trip['price_from'], 0, ',', '.'); ?></div>
    </div>
    <div class="trip-highlights__item">
      <div class="trip-highlights__label">Disponibilità</div>
      <div class="trip-highlights__value"><?php echo htmlspecialchars($status_label); ?></div>
    </div>
  </div>
</div>

<!-- ========================================================
     STICKY TAB NAVIGATION
     ======================================================== -->
<div class="trip-tabs" id="trip-tabs">
  <nav class="trip-tabs__nav">
    <button class="trip-tabs__btn active" data-target="itinerario">Itinerario</button>
    <button class="trip-tabs__btn" data-target="cosa-include">Cosa Include</button>
    <button class="trip-tabs__btn" data-target="galleria">Galleria</button>
    <?php if ($has_form): ?>
    <button class="trip-tabs__btn" data-target="richiedi-preventivo">Richiedi Preventivo</button>
    <?php endif; ?>
  </nav>
</div>

<!-- ========================================================
     ITINERARY SECTION
     ======================================================== -->
<section class="trip-section" id="itinerario">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Itinerario</h2>
    </div>
    <div class="itinerary">
      <?php foreach ($trip['itinerary'] as $i => $day): ?>
      <div class="itinerary__item <?php echo $i === 0 ? 'open' : ''; ?>">
        <div class="itinerary__header">
          <div class="itinerary__day-num"><?php echo str_pad($day['day'], 2, '0', STR_PAD_LEFT); ?></div>
          <div class="itinerary__title"><?php echo htmlspecialchars($day['title']); ?></div>
          <i class="fa-solid fa-chevron-right itinerary__chevron"></i>
        </div>
        <div class="itinerary__body">
          <p class="itinerary__desc"><?php echo htmlspecialchars($day['description']); ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ========================================================
     COSA INCLUDE SECTION
     ======================================================== -->
<section class="trip-section trip-section--dark" id="cosa-include">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Cosa Include</h2>
    </div>
    <div class="includes-grid">
      <div>
        <div class="includes-col__title"><i class="fa-solid fa-circle-check" style="color:#2ecc71;margin-right:0.5rem;"></i>Incluso nel prezzo</div>
        <ul class="includes-list">
          <?php foreach ($trip['included'] as $item): ?>
          <li><i class="fa-solid fa-check includes-list__icon--yes"></i><?php echo htmlspecialchars($item); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div>
        <div class="includes-col__title"><i class="fa-solid fa-circle-xmark" style="color:#CC0031;margin-right:0.5rem;"></i>Non incluso</div>
        <ul class="includes-list">
          <?php foreach ($trip['excluded'] as $item): ?>
          <li><i class="fa-solid fa-xmark includes-list__icon--no"></i><?php echo htmlspecialchars($item); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- ========================================================
     GALLERY SECTION
     ======================================================== -->
<section class="trip-section trip-section--dark" id="galleria">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Galleria</h2>
    </div>
    <?php if (!empty($trip['gallery'])): ?>
    <div class="gallery-grid" id="gallery-grid">
      <?php foreach ($trip['gallery'] as $idx => $img_url): ?>
      <div class="gallery-item" data-index="<?php echo $idx; ?>">
        <img src="<?php echo htmlspecialchars($img_url); ?>" alt="<?php echo htmlspecialchars($trip['title']); ?> foto <?php echo $idx + 1; ?>" loading="lazy">
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- ========================================================
     LIGHTBOX
     ======================================================== -->
<div class="lightbox" id="lightbox" role="dialog" aria-modal="true">
  <button class="lightbox__close" id="lb-close" aria-label="Chiudi">&times;</button>
  <span class="lightbox__counter" id="lb-counter"></span>
  <button class="lightbox__prev" id="lb-prev" aria-label="Foto precedente"><i class="fa-solid fa-chevron-left"></i></button>
  <img class="lightbox__img" id="lb-img" src="" alt="">
  <button class="lightbox__next" id="lb-next" aria-label="Foto successiva"><i class="fa-solid fa-chevron-right"></i></button>
</div>

<!-- ========================================================
     TAGS SECTION
     ======================================================== -->
<?php if (!empty($trip['tags'])): ?>
<section class="trip-section" id="tags">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Questo viaggio è perfetto per:</h2>
    </div>
    <?php
    $continent_slugs = ['america','asia','europa','africa','oceania','medio-oriente'];
    ?>
    <div class="trip-tags">
      <?php foreach ($trip['tags'] as $tag): ?>
        <?php
        if (in_array($tag, $continent_slugs)) {
            $href = '/viaggi?continent=' . urlencode($tag);
        } else {
            $href = '/viaggi?tipo=' . urlencode($tag);
        }
        ?>
        <a href="<?php echo $href; ?>" class="trip-tag"><?php echo htmlspecialchars($tag); ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ========================================================
     RELATED TRIPS
     ======================================================== -->
<?php
// Get up to 3 trips sharing the same continent (excluding current trip)
$all_trips_raw = array_filter(load_trips(), fn($t) =>
    $t['published'] === true &&
    $t['slug'] !== $trip['slug'] &&
    $t['continent'] === $trip['continent']
);
$related = array_slice(array_values($all_trips_raw), 0, 3);
$status_labels_rel = ['confermata'=>'Confermata','ultimi-posti'=>'Ultimi posti','sold-out'=>'Sold out','programmata'=>'In programmazione'];
?>
<?php if (!empty($related)): ?>
<section class="trip-section section--dark" id="related">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Altri viaggi che potrebbero piacerti</h2>
    </div>
    <div class="related-grid">
      <?php foreach ($related as $rel): ?>
      <div class="trip-card">
        <img class="trip-card__image" src="<?php echo htmlspecialchars($rel['hero_image']); ?>" alt="<?php echo htmlspecialchars($rel['title']); ?>">
        <div class="trip-card__overlay"></div>
        <div class="trip-card__continent"><?php echo htmlspecialchars(ucfirst($rel['continent'])); ?></div>
        <div class="trip-card__status status--<?php echo htmlspecialchars($rel['status']); ?>"><?php echo htmlspecialchars($status_labels_rel[$rel['status']] ?? ucfirst($rel['status'])); ?></div>
        <div class="trip-card__content">
          <h3 class="trip-card__title"><?php echo htmlspecialchars($rel['title']); ?></h3>
          <div class="trip-card__dates"><?php echo htmlspecialchars(fmt_date($rel['date_start'])); ?></div>
          <div class="trip-card__price">Da €<?php echo number_format($rel['price_from'], 0, ',', '.'); ?></div>
          <a href="/viaggio/<?php echo htmlspecialchars($rel['slug']); ?>" class="btn btn--outline-white" style="margin-top:0.5rem;padding:8px 18px;font-size:0.85rem;">Scopri il viaggio</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($has_form): ?>
<!-- ========================================================
     QUOTE FORM SECTION
     ======================================================== -->
<section class="quote-form-section" id="richiedi-preventivo">
  <div class="quote-form-inner">
    <h2>Richiedi il tuo Preventivo</h2>
    <p style="text-align:center;color:var(--grey);margin-bottom:2rem;">
      Compila il modulo — Lorenzo ti risponde entro 24 ore.
    </p>

    <div id="quote-form-wrap">

      <!-- B2B/B2C toggle -->
      <div class="client-toggle" id="client-toggle" style="margin-bottom:1.5rem;">
        <button class="client-toggle__btn active" data-type="privato" type="button">Privato</button>
        <button class="client-toggle__btn" data-type="agenzia" type="button">Agenzia</button>
      </div>

      <!-- Agency code entry — visible only when Agenzia tab is active -->
      <div class="form-row" id="agency-code-row" style="display:none;">
        <label class="form-label" for="f-agency-code">Codice Agenzia *</label>
        <input class="form-input" type="password" id="f-agency-code" name="agency_code"
               placeholder="Inserisci il codice" autocomplete="off">
        <div id="agency-code-feedback" style="font-size:0.8rem;margin-top:0.35rem;"></div>
      </div>

      <div id="form-error-msg" class="form-error" style="display:none;"></div>

      <form id="quote-form" novalidate>

        <!-- Hidden fields -->
        <input type="hidden" name="trip_slug" value="<?php echo htmlspecialchars($trip['slug']); ?>">
        <input type="hidden" name="trip_title" value="<?php echo htmlspecialchars($trip['title']); ?>">
        <input type="hidden" name="tipo_cliente" id="tipo-cliente-hidden" value="privato">

        <!-- Nome + Cognome -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
          <div class="form-row">
            <label class="form-label" for="f-nome">Nome *</label>
            <input class="form-input" type="text" id="f-nome" name="nome" required>
          </div>
          <div class="form-row">
            <label class="form-label" for="f-cognome">Cognome *</label>
            <input class="form-input" type="text" id="f-cognome" name="cognome" required>
          </div>
        </div>

        <!-- Email + Telefono -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
          <div class="form-row">
            <label class="form-label" for="f-email">Email *</label>
            <input class="form-input" type="email" id="f-email" name="email" required>
          </div>
          <div class="form-row">
            <label class="form-label" for="f-telefono">Telefono</label>
            <input class="form-input" type="tel" id="f-telefono" name="telefono">
          </div>
        </div>

        <!-- Room type -->
        <?php if (!empty($form_config['room_types'])): ?>
        <div class="form-row">
          <label class="form-label" for="f-room-type">Tipo di camera</label>
          <select class="form-select" id="f-room-type" name="room_type">
            <?php foreach ($form_config['room_types'] as $rt): ?>
            <option value="<?php echo htmlspecialchars($rt['slug']); ?>" data-delta="<?php echo (int)$rt['price_delta']; ?>">
              <?php echo htmlspecialchars($rt['label']); ?>
              <?php if ($rt['price_delta'] > 0): ?>(+€<?php echo number_format($rt['price_delta'], 0, ',', '.'); ?>)<?php endif; ?>
              <?php if ($rt['price_delta'] < 0): ?>(<?php echo number_format($rt['price_delta'], 0, ',', '.'); ?>€)<?php endif; ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>

        <!-- Partecipanti: adulti -->
        <div class="form-row">
          <label class="form-label">Adulti *</label>
          <div class="counter-input">
            <button class="counter-btn" type="button" data-counter="adulti" data-action="dec">−</button>
            <span class="counter-val" id="adulti-val">2</span>
            <button class="counter-btn" type="button" data-counter="adulti" data-action="inc">+</button>
          </div>
          <input type="hidden" name="adulti" id="adulti-hidden" value="2">
        </div>

        <!-- Bambini -->
        <div class="form-row">
          <label class="form-label">Bambini</label>
          <div class="counter-input">
            <button class="counter-btn" type="button" data-counter="bambini" data-action="dec">−</button>
            <span class="counter-val" id="bambini-val">0</span>
            <button class="counter-btn" type="button" data-counter="bambini" data-action="inc">+</button>
          </div>
          <input type="hidden" name="bambini" id="bambini-hidden" value="0">
          <div class="child-ages" id="child-ages"></div>
        </div>

        <!-- Add-ons -->
        <?php if (!empty($form_config['addons'])): ?>
        <div class="form-row">
          <label class="form-label">Optional</label>
          <?php foreach ($form_config['addons'] as $addon): ?>
          <div class="addon-item">
            <input type="checkbox" id="addon-<?php echo htmlspecialchars($addon['slug']); ?>" name="addons[]" value="<?php echo htmlspecialchars($addon['slug']); ?>" data-price="<?php echo (int)$addon['price']; ?>">
            <label class="addon-item__label" for="addon-<?php echo htmlspecialchars($addon['slug']); ?>"><?php echo htmlspecialchars($addon['label']); ?></label>
            <span class="addon-item__price">+€<?php echo number_format($addon['price'], 0, ',', '.'); ?></span>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Price estimate box -->
        <div class="price-estimate" id="price-estimate">
          <div class="price-estimate__total" id="pe-total">€0</div>
          <div class="price-estimate__breakdown" id="pe-breakdown"></div>
          <?php if (!empty($form_config['competitor_benchmark'])): ?>
          <div class="price-estimate__savings" id="pe-savings"></div>
          <?php endif; ?>
        </div>

        <!-- Agency fields (hidden by default; revealed only after valid code) -->
        <div class="agency-fields" id="agency-fields" style="display:none;">
          <div class="form-row">
            <label class="form-label" for="f-nome-agenzia">Nome Agenzia *</label>
            <input class="form-input" type="text" id="f-nome-agenzia" name="nome_agenzia">
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div class="form-row">
              <label class="form-label" for="f-iata">Codice IATA (opzionale)</label>
              <input class="form-input" type="text" id="f-iata" name="codice_iata">
            </div>
            <div class="form-row">
              <label class="form-label" for="f-citta">Città / Provincia *</label>
              <input class="form-input" type="text" id="f-citta" name="citta">
            </div>
          </div>
          <div class="form-row">
            <label class="form-label" for="f-commissione">Commissione richiesta (%)</label>
            <input class="form-input" type="number" id="f-commissione" name="commissione" min="0" max="30" step="0.5">
          </div>
        </div>

        <!-- Note -->
        <div class="form-row">
          <label class="form-label" for="f-note">Note o domande</label>
          <textarea class="form-textarea" id="f-note" name="note" placeholder="Hai richieste speciali? Scrivici qui…"></textarea>
        </div>

        <button type="submit" class="btn btn--gold" style="width:100%;padding:1rem;font-size:1rem;">
          Invia Richiesta di Preventivo
        </button>

      </form>
    </div>

    <!-- WhatsApp CTA -->
    <div class="whatsapp-cta">
      <p>Preferisci scrivere su WhatsApp?
        <a href="https://wa.me/<?php echo str_replace([' ','+'], ['',''], WHATSAPP_NUMBER); ?>?text=<?php echo urlencode('Ciao Lorenzo! Sono interessato al viaggio ' . $trip['title']); ?>" target="_blank" rel="noopener">
          <i class="fa-brands fa-whatsapp"></i> Scrivici ora
        </a>
      </p>
    </div>

  </div>
</section>
<?php endif; ?>

</main>

<?php require_once ROOT . '/includes/footer.php'; ?>

<script>
(function () {
  // --- Sticky top bar ---
  var hero   = document.querySelector('.trip-hero');
  var topbar = document.getElementById('trip-topbar');
  var tabs   = document.getElementById('trip-tabs');

  function onScroll() {
    var heroBottom = hero ? hero.getBoundingClientRect().bottom : 0;
    if (topbar) topbar.classList.toggle('visible', heroBottom < 80);
  }
  window.addEventListener('scroll', onScroll, { passive: true });

  // --- Tab navigation: smooth scroll with offset ---
  document.querySelectorAll('.trip-tabs__btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.trip-tabs__btn').forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      var target = document.getElementById(btn.dataset.target);
      if (!target) return;
      var offset = (tabs ? tabs.offsetHeight : 0) + 8;
      var top = target.getBoundingClientRect().top + window.scrollY - offset;
      window.scrollTo({ top: top, behavior: 'smooth' });
    });
  });

  // --- Itinerary accordion (single-open) ---
  // Day 1 body: set initial max-height so it's visible (matches .itinerary__item.open CSS)
  document.querySelectorAll('.itinerary__item.open .itinerary__body').forEach(function (body) {
    body.style.maxHeight = body.scrollHeight + 'px';
  });

  document.querySelectorAll('.itinerary__header').forEach(function (header) {
    header.addEventListener('click', function () {
      var item     = header.parentElement;
      var isOpen   = item.classList.contains('open');
      var allItems = document.querySelectorAll('.itinerary__item');

      // Close all
      allItems.forEach(function (it) {
        it.classList.remove('open');
        it.querySelector('.itinerary__body').style.maxHeight = '0';
      });

      // Open clicked (if it was closed)
      if (!isOpen) {
        item.classList.add('open');
        item.querySelector('.itinerary__body').style.maxHeight =
          item.querySelector('.itinerary__body').scrollHeight + 'px';
      }
    });
  });

  // ----------------------------------------------------------------
  // GALLERY LIGHTBOX
  // ----------------------------------------------------------------
  var galleryImages = <?php echo json_encode(array_values($trip['gallery'] ?? []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
  var currentIdx    = 0;
  var lightbox      = document.getElementById('lightbox');
  var lbImg         = document.getElementById('lb-img');
  var lbCounter     = document.getElementById('lb-counter');

  function lbOpen(idx) {
    currentIdx = (idx + galleryImages.length) % galleryImages.length;
    lbImg.src  = galleryImages[currentIdx];
    lbCounter.textContent = (currentIdx + 1) + ' / ' + galleryImages.length;
    lightbox.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function lbClose() {
    lightbox.classList.remove('open');
    document.body.style.overflow = '';
  }

  document.querySelectorAll('.gallery-item').forEach(function (item) {
    item.addEventListener('click', function () { lbOpen(parseInt(item.dataset.index)); });
  });

  document.getElementById('lb-prev').addEventListener('click', function () { lbOpen(currentIdx - 1); });
  document.getElementById('lb-next').addEventListener('click', function () { lbOpen(currentIdx + 1); });
  document.getElementById('lb-close').addEventListener('click', lbClose);

  lightbox.addEventListener('click', function (e) {
    if (e.target === lightbox) lbClose();
  });

  document.addEventListener('keydown', function (e) {
    if (!lightbox.classList.contains('open')) return;
    if (e.key === 'ArrowLeft')  lbOpen(currentIdx - 1);
    if (e.key === 'ArrowRight') lbOpen(currentIdx + 1);
    if (e.key === 'Escape')     lbClose();
  });

  // Touch/swipe
  var touchStartX = 0;
  lightbox.addEventListener('touchstart', function (e) { touchStartX = e.touches[0].clientX; }, { passive: true });
  lightbox.addEventListener('touchend',   function (e) {
    var delta = e.changedTouches[0].clientX - touchStartX;
    if (Math.abs(delta) > 40) { delta < 0 ? lbOpen(currentIdx + 1) : lbOpen(currentIdx - 1); }
  });

  // ----------------------------------------------------------------
  // QUOTE FORM — Live price calculation + B2B toggle + agency code
  //              validation (SHA-256) + submission
  // ----------------------------------------------------------------
  <?php if ($has_form): ?>
  var formConfig = <?php echo json_encode($form_config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
  var pricePerPerson      = formConfig.price_per_person    || <?php echo (int)($trip['price_from'] ?? 0); ?>;
  var competitorBenchmark = formConfig.competitor_benchmark || 0;
  var agencyCodeHash      = formConfig.agency_code_hash    || '';   // expected SHA-256 hex (lowercase)
  var adultCount  = 2;
  var childCount  = 0;

  // -- Child age inputs --
  function rebuildChildAges() {
    var container = document.getElementById('child-ages');
    container.innerHTML = '';
    for (var i = 0; i < childCount; i++) {
      var inp = document.createElement('input');
      inp.type        = 'number';
      inp.name        = 'eta_bambini[]';
      inp.className   = 'form-input child-age-input';
      inp.placeholder = 'Età ' + (i + 1);
      inp.min         = 0;
      inp.max         = 17;
      container.appendChild(inp);
    }
  }

  // -- Counter buttons --
  document.querySelectorAll('.counter-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var counter = btn.dataset.counter;
      var action  = btn.dataset.action;
      if (counter === 'adulti') {
        adultCount = Math.max(1, adultCount + (action === 'inc' ? 1 : -1));
        document.getElementById('adulti-val').textContent = adultCount;
        document.getElementById('adulti-hidden').value    = adultCount;
      } else {
        childCount = Math.max(0, childCount + (action === 'inc' ? 1 : -1));
        document.getElementById('bambini-val').textContent = childCount;
        document.getElementById('bambini-hidden').value    = childCount;
        rebuildChildAges();
      }
      updatePrice();
    });
  });

  // -- Price calculation --
  function updatePrice() {
    var roomSel    = document.getElementById('f-room-type');
    var roomDelta  = roomSel ? parseInt(roomSel.options[roomSel.selectedIndex].dataset.delta || 0) : 0;
    var addonTotal = 0;
    document.querySelectorAll('.addon-item input[type=checkbox]:checked').forEach(function (cb) {
      addonTotal += parseInt(cb.dataset.price || 0);
    });
    var perPerson = pricePerPerson + roomDelta;
    var total     = (perPerson * adultCount) + (addonTotal * (adultCount + childCount));
    var peTotal   = document.getElementById('pe-total');
    var peBreak   = document.getElementById('pe-breakdown');
    var peSavings = document.getElementById('pe-savings');
    if (peTotal)  peTotal.textContent  = '€' + total.toLocaleString('it-IT');
    if (peBreak)  peBreak.textContent  = '€' + perPerson.toLocaleString('it-IT') + ' × ' + adultCount + ' adulti' +
                                         (addonTotal > 0 ? ' + €' + addonTotal.toLocaleString('it-IT') + ' optional' : '');
    if (peSavings && competitorBenchmark > 0) {
      var savings = (competitorBenchmark - perPerson) * adultCount;
      peSavings.textContent = savings > 0 ? 'Risparmia €' + savings.toLocaleString('it-IT') + ' rispetto al prezzo medio di mercato' : '';
    }
  }

  if (document.getElementById('f-room-type')) {
    document.getElementById('f-room-type').addEventListener('change', updatePrice);
  }
  document.querySelectorAll('.addon-item input[type=checkbox]').forEach(function (cb) {
    cb.addEventListener('change', updatePrice);
  });
  updatePrice(); // Initial render

  // -- Agency code SHA-256 validation --
  // Converts ArrayBuffer to lowercase hex string
  function bufToHex(buf) {
    return Array.from(new Uint8Array(buf))
      .map(function (b) { return b.toString(16).padStart(2, '0'); })
      .join('');
  }

  // Hashes the entered code with SHA-256 and compares against agencyCodeHash.
  // Reveals agency-fields on match; shows feedback message in either case.
  // Falls back to showing fields on any non-empty entry when hash is absent.
  function validateAgencyCode(codeValue) {
    var feedback     = document.getElementById('agency-code-feedback');
    var agencyFields = document.getElementById('agency-fields');
    if (!agencyFields) return;

    // Fallback: no hash configured — show fields for any non-empty code
    if (!agencyCodeHash) {
      if (codeValue.trim()) {
        agencyFields.style.display = 'block';
        if (feedback) { feedback.textContent = ''; }
      } else {
        agencyFields.style.display = 'none';
      }
      return;
    }

    if (!codeValue.trim()) {
      agencyFields.style.display = 'none';
      if (feedback) { feedback.textContent = ''; feedback.style.color = ''; }
      return;
    }

    var encoder = new TextEncoder();
    crypto.subtle.digest('SHA-256', encoder.encode(codeValue)).then(function (hashBuf) {
      var hex = bufToHex(hashBuf);
      if (hex === agencyCodeHash) {
        agencyFields.style.display = 'block';
        if (feedback) {
          feedback.textContent = 'Codice valido — campi agenzia sbloccati.';
          feedback.style.color = '#2ecc71';
        }
      } else {
        agencyFields.style.display = 'none';
        if (feedback) {
          feedback.textContent = 'Codice non valido.';
          feedback.style.color = '#CC0031';
        }
      }
    });
  }

  var agencyCodeInput = document.getElementById('f-agency-code');
  if (agencyCodeInput) {
    agencyCodeInput.addEventListener('input', function () {
      validateAgencyCode(agencyCodeInput.value);
    });
  }

  // -- B2B toggle --
  document.querySelectorAll('.client-toggle__btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.client-toggle__btn').forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      var type = btn.dataset.type;
      document.getElementById('tipo-cliente-hidden').value = type;

      var agencyCodeRow = document.getElementById('agency-code-row');
      var agencyFields  = document.getElementById('agency-fields');
      var feedback      = document.getElementById('agency-code-feedback');

      if (type === 'agenzia') {
        // Show code entry field; agency-fields remain hidden until code validates
        if (agencyCodeRow) agencyCodeRow.style.display = 'block';
        // Re-validate whatever is already typed (clears stale state on re-toggle)
        if (agencyCodeInput) validateAgencyCode(agencyCodeInput.value);
      } else {
        // Switched back to Privato: hide both code field and agency-specific fields
        if (agencyCodeRow) agencyCodeRow.style.display = 'none';
        if (agencyFields)  agencyFields.style.display  = 'none';
        if (feedback) { feedback.textContent = ''; feedback.style.color = ''; }
      }
    });
  });

  // -- Form submission --
  var quoteForm = document.getElementById('quote-form');
  if (quoteForm) {
    quoteForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var errorDiv = document.getElementById('form-error-msg');
      errorDiv.style.display = 'none';

      // Basic validation
      var nome    = document.getElementById('f-nome').value.trim();
      var cognome = document.getElementById('f-cognome').value.trim();
      var email   = document.getElementById('f-email').value.trim();
      if (!nome || !cognome || !email) {
        errorDiv.textContent = 'Compila i campi obbligatori: Nome, Cognome, Email.';
        errorDiv.style.display = 'block';
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }

      var submitBtn = quoteForm.querySelector('button[type=submit]');
      submitBtn.disabled    = true;
      submitBtn.textContent = 'Invio in corso…';

      var formData = new FormData(quoteForm);

      fetch('/api/submit-form.php', {
        method: 'POST',
        body: formData
      })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data.success) {
          document.getElementById('quote-form-wrap').innerHTML =
            '<div class="form-success">' +
            '<i class="fa-solid fa-circle-check" style="font-size:2.5rem;color:#2ecc71;margin-bottom:1rem;display:block;"></i>' +
            '<h3 style="font-family:var(--font-heading);margin-bottom:0.5rem;">Richiesta inviata!</h3>' +
            '<p>Lorenzo ti risponderà entro 24 ore. Grazie per aver scelto Viaggia col Baffo.</p>' +
            '</div>';
        } else {
          errorDiv.textContent = data.error || 'Errore durante l\'invio. Riprova o contattaci su WhatsApp.';
          errorDiv.style.display = 'block';
          submitBtn.disabled    = false;
          submitBtn.textContent = 'Invia Richiesta di Preventivo';
        }
      })
      .catch(function () {
        errorDiv.textContent = 'Errore di rete. Controlla la connessione e riprova.';
        errorDiv.style.display = 'block';
        submitBtn.disabled    = false;
        submitBtn.textContent = 'Invia Richiesta di Preventivo';
      });
    });
  }
  <?php endif; ?>
})();
</script>
