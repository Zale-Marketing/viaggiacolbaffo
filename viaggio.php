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

<!-- QUOTE FORM — Plan 03 appends here -->

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
})();
</script>
