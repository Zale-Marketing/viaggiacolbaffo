<?php
require_once __DIR__ . '/includes/config.php';
require_once ROOT . '/includes/functions.php';

// --- Slug routing ---
$slug = $_GET['slug'] ?? '';
if (!$slug) { header("Location: /404"); exit; }

$trip = get_trip_by_slug($slug);
if (!$trip) { header("Location: /404"); exit; }

// --- Deleted gate: deleted trips are never publicly accessible ---
if ($trip['deleted'] ?? false) {
    header("Location: /404");
    exit;
}

// --- Preview token: allows viewing unpublished trips with a valid token ---
$preview_token = $_GET['preview'] ?? '';
$is_preview = $preview_token !== '' && ($trip['preview_token'] ?? '') === $preview_token;

// --- Published gate: unpublished trips return 404 unless preview token matches ---
if (!($trip['published'] ?? false) && !$is_preview) {
    header("Location: /404");
    exit;
}

// --- Page variables ---
$page_title = htmlspecialchars($trip['title'] ?? 'Viaggio') . ' — Viaggia col Baffo';
$hero_page  = true;

// Status display label
$status_labels = [
    'confermata'   => 'Confermata',
    'ultimi-posti' => 'Ultimi posti',
    'sold-out'     => 'Sold out',
    'programmata'  => 'In programmazione',
];
$trip_status  = $trip['status'] ?? 'programmata';
$status_label = $status_labels[$trip_status] ?? ucfirst($trip_status);

// Date formatting helpers
function fmt_date($d): string {
    if (empty($d)) return 'Da definire';
    $months = ['', 'Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'];
    $parts = explode('-', $d);
    if (count($parts) < 3) return $d;
    [$y, $m, $day] = $parts;
    return "$day {$months[(int)$m]} $y";
}
$date_display = fmt_date($trip['date_start'] ?? '') . ' – ' . fmt_date($trip['date_end'] ?? '');

$form_config = $trip['form_config'] ?? [];
$fc          = $form_config; // alias for template
$has_form    = isset($form_config['prezzo_base_persona']) && (int)($form_config['prezzo_base_persona']) > 0;

require_once ROOT . '/includes/header.php';
?>

<main>

<!-- ========================================================
     TRIP HERO
     ======================================================== -->
<div class="trip-hero" style="background-image:url('<?php echo htmlspecialchars($trip['hero_image'] ?? ''); ?>')">
  <div class="trip-hero__overlay"></div>
  <div class="trip-hero__content container">
    <div class="status-pill status-<?php echo htmlspecialchars($trip_status); ?>" style="display:inline-block;margin-bottom:0.75rem;">
      <?php echo htmlspecialchars($status_label); ?>
    </div>
    <h1 class="trip-hero__title"><?php echo htmlspecialchars($trip['title'] ?? ''); ?></h1>
    <div class="trip-hero__meta">
      <span><i class="fa-regular fa-calendar"></i> <?php echo htmlspecialchars($date_display); ?></span>
      <span><i class="fa-regular fa-clock"></i> <?php echo htmlspecialchars($trip['duration'] ?? ''); ?></span>
      <span><i class="fa-solid fa-tag"></i> Da €<?php echo number_format($trip['price_from'] ?? 0, 0, ',', '.'); ?> p.p.</span>
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

    <!-- LEFT: title + dates -->
    <div class="trip-topbar__left">
      <div class="trip-topbar__name"><?php echo htmlspecialchars($trip['title'] ?? ''); ?></div>
      <div class="trip-topbar__dates">
        <i class="fa-regular fa-calendar"></i>
        <?php echo htmlspecialchars($date_display); ?>
      </div>
    </div>

    <!-- CENTER: savings + status -->
    <div class="trip-topbar__center">
      <?php if (!empty($fc['competitor_enabled'])): ?>
      <span class="trip-topbar__savings" id="topbar-savings"></span>
      <?php endif; ?>
      <span class="trip-topbar__status trip-topbar__status--<?php echo htmlspecialchars($trip_status); ?>">
        <?php
        $topbar_icons = [
          'confermata'   => '✓',
          'ultimi-posti' => '⚡',
          'sold-out'     => '✕',
          'programmata'  => '◷',
        ];
        echo ($topbar_icons[$trip_status] ?? '●') . ' ' . htmlspecialchars($status_label);
        ?>
      </span>
    </div>

    <!-- RIGHT: CTA -->
    <div class="trip-topbar__right">
      <?php if ($has_form): ?>
      <a href="#richiedi-preventivo" class="trip-topbar__cta">Richiedi Preventivo</a>
      <?php endif; ?>
    </div>

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
      <div class="trip-highlights__value"><?php echo htmlspecialchars($trip['duration'] ?? ''); ?></div>
    </div>
    <div class="trip-highlights__item">
      <div class="trip-highlights__label">Prezzo da</div>
      <div class="trip-highlights__value">€<?php echo number_format($trip['price_from'] ?? 0, 0, ',', '.'); ?></div>
    </div>
    <div class="trip-highlights__item">
      <div class="trip-highlights__label">Disponibilità</div>
      <div class="trip-highlights__value"><?php echo htmlspecialchars($status_label); ?></div>
    </div>
  </div>
</div>

<?php
$accompagnatore = $trip['accompagnatore'] ?? null;
$volo           = $trip['volo'] ?? null;
?>

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

<!-- ========================================================
     ITINERARY SECTION — TIMELINE
     ======================================================== -->
<section class="trip-section" id="itinerario">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Itinerario</h2>
    </div>
    <div class="timeline">
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
    </div>
  </div>
</section>

<div class="gated-content" id="gated-accompagnatore">
<?php if (!empty($accompagnatore['nome'])): ?>
<!-- ========================================================
     ACCOMPAGNATORE SECTION
     ======================================================== -->
<div class="accompagnatore-section">
  <div class="container">
    <div class="accompagnatore-card">
      <img class="accompagnatore-card__photo"
           src="<?php echo htmlspecialchars($accompagnatore['foto'] ?? ''); ?>"
           alt="<?php echo htmlspecialchars($accompagnatore['nome']); ?>">
      <div class="accompagnatore-card__info">
        <div class="accompagnatore-card__badge">&#9679; Accompagna questo viaggio</div>
        <div class="accompagnatore-card__name"><?php echo htmlspecialchars($accompagnatore['nome']); ?></div>
        <div class="accompagnatore-card__titolo"><?php echo htmlspecialchars($accompagnatore['titolo'] ?? ''); ?></div>
        <p class="accompagnatore-card__bio"><?php echo htmlspecialchars($accompagnatore['bio'] ?? ''); ?></p>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
</div>

<div class="gated-content" id="gated-volo">
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
</div>

<div class="gated-content" id="gated-alloggi">
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
    <div class="hotel-list">
      <?php foreach ($trip['hotel'] as $hotel): ?>
      <div class="hotel-row">
        <div class="hotel-row__img-wrap">
          <?php if (!empty($hotel['image_url'])): ?>
          <img src="<?php echo htmlspecialchars($hotel['image_url']); ?>"
               alt="<?php echo htmlspecialchars($hotel['nome']); ?>"
               loading="lazy" class="hotel-row__img">
          <?php else: ?>
          <div class="hotel-row__img-placeholder"><i class="fa-solid fa-hotel"></i></div>
          <?php endif; ?>
          <span class="hotel-badge-notti"><?php echo (int)$hotel['notti']; ?> notti</span>
        </div>
        <div class="hotel-row__body">
          <div class="hotel-row__top">
            <div>
              <div class="hotel-row__city">
                <i class="fa-solid fa-location-dot"></i>
                <?php echo htmlspecialchars($hotel['citta']); ?>
              </div>
              <div class="hotel-row__name"><?php echo htmlspecialchars($hotel['nome']); ?></div>
              <div class="hotel-stars"><?php echo str_repeat('&#9733;', (int)($hotel['stelle'] ?? 0)); ?></div>
            </div>
            <div class="hotel-row__badges">
              <?php if ($hotel['inclusa_colazione'] ?? false): ?>
              <span class="hotel-colazione-yes"><i class="fa-solid fa-mug-hot"></i> Colazione inclusa</span>
              <?php else: ?>
              <span class="hotel-colazione-no">Colazione non inclusa</span>
              <?php endif; ?>
            </div>
          </div>
          <?php if (!empty($hotel['descrizione'])): ?>
          <p class="hotel-row__desc"><?php echo htmlspecialchars($hotel['descrizione']); ?></p>
          <?php endif; ?>
          <?php if (!empty($hotel['indirizzo'])): ?>
          <div class="hotel-row__address">
            <i class="fa-solid fa-map-pin"></i>
            <?php echo htmlspecialchars($hotel['indirizzo']); ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
</div>

<div class="gated-content" id="gated-cosa-include">
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
          <?php foreach (($trip['included'] ?? []) as $item): ?>
          <li><i class="fa-solid fa-check includes-list__icon--yes"></i><?php echo htmlspecialchars($item); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div>
        <div class="includes-col__title"><i class="fa-solid fa-circle-xmark" style="color:#CC0031;margin-right:0.5rem;"></i>Non incluso</div>
        <ul class="includes-list">
          <?php foreach (($trip['excluded'] ?? []) as $item): ?>
          <li><i class="fa-solid fa-xmark includes-list__icon--no"></i><?php echo htmlspecialchars($item); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</section>
</div>

<div class="gated-content" id="gated-galleria">
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
</div>

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

<div class="gated-content" id="gated-tags">
<!-- ========================================================
     TAGS SECTION
     ======================================================== -->
<?php if (!empty($trip['tags'])): ?>
<section class="trip-section tags-section" id="tags">
  <div class="container">
    <h2 class="section-header__title">Questo viaggio è perfetto per:</h2>
    <?php
    $continent_slugs = ['america','asia','europa','africa','oceania','medio-oriente'];
    ?>
    <div class="tags-cloud">
      <?php foreach ($trip['tags'] as $tag): ?>
        <?php
        if (in_array($tag, $continent_slugs)) {
            $href = '/viaggi?continent=' . urlencode($tag);
        } else {
            $href = '/viaggi?tipo=' . urlencode($tag);
        }
        ?>
        <a href="<?php echo $href; ?>" class="tag-pill"><?php echo htmlspecialchars($tag); ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
</div>

<div class="gated-content" id="gated-related">
<!-- ========================================================
     RELATED TRIPS
     ======================================================== -->
<?php
// Get up to 3 trips sharing the same continent (excluding current trip)
$all_trips_raw = array_filter(load_trips(), fn($t) =>
    ($t['published'] ?? false) === true &&
    ($t['deleted'] ?? false) === false &&
    ($t['slug'] ?? '') !== ($trip['slug'] ?? '') &&
    ($t['continent'] ?? '') === ($trip['continent'] ?? '')
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
        <img class="trip-card__image" src="<?php echo htmlspecialchars($rel['hero_image'] ?? ''); ?>" alt="<?php echo htmlspecialchars($rel['title'] ?? ''); ?>">
        <div class="trip-card__overlay"></div>
        <div class="trip-card__continent"><?php echo htmlspecialchars(ucfirst($rel['continent'] ?? '')); ?></div>
        <?php $rel_status = $rel['status'] ?? 'programmata'; ?>
        <div class="trip-card__status status--<?php echo htmlspecialchars($rel_status); ?>"><?php echo htmlspecialchars($status_labels_rel[$rel_status] ?? ucfirst($rel_status)); ?></div>
        <div class="trip-card__content">
          <h3 class="trip-card__title"><?php echo htmlspecialchars($rel['title'] ?? ''); ?></h3>
          <div class="trip-card__dates"><?php echo htmlspecialchars(fmt_date($rel['date_start'] ?? '')); ?></div>
          <div class="trip-card__price">Da €<?php echo number_format($rel['price_from'] ?? 0, 0, ',', '.'); ?></div>
          <a href="/viaggio/<?php echo htmlspecialchars($rel['slug'] ?? ''); ?>" class="btn btn--outline-white" style="margin-top:0.5rem;padding:8px 18px;font-size:0.85rem;">Scopri il viaggio</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
</div>

<div class="gated-content" id="gated-preventivo">
<?php if ($has_form): ?>
<script>
const CONFIG = {
  nome_viaggio: "<?= htmlspecialchars($trip['title'] ?? '') ?>",
  prezzo_base_persona: <?= (int)($fc['prezzo_base_persona'] ?? 4350) ?>,
  room_types: <?= json_encode($fc['room_types'] ?? []) ?>,
  supplemento_singola: <?= (int)($fc['supplemento_singola'] ?? 1600) ?>,
  sconto_terzo_letto: <?= (int)($fc['sconto_terzo_letto'] ?? 0) ?>,
  sconto_quarto_letto: <?= (int)($fc['sconto_quarto_letto'] ?? 0) ?>,
  sconto_quinto_letto: <?= (int)($fc['sconto_quinto_letto'] ?? 0) ?>,
  child_discounts_enabled: <?= !empty($fc['child_discounts_enabled']) ? 'true' : 'false' ?>,
  child_discount_brackets: <?= json_encode($fc['child_discount_brackets'] ?? []) ?>,
  insurance_enabled: <?= !empty($fc['insurance_enabled']) ? 'true' : 'false' ?>,
  percentuale_assicurazione: <?= ($fc['percentuale_assicurazione'] ?? 5) / 100 ?>,
  competitor_enabled: <?= !empty($fc['competitor_enabled']) ? 'true' : 'false' ?>,
  prezzo_concorrenza_persona: <?= (int)($fc['prezzo_concorrenza_persona'] ?? 7000) ?>,
  prezzo_concorrenza_letti_extra: <?= (int)($fc['prezzo_concorrenza_letti_extra'] ?? 5000) ?>,
  agency_code_hash: "<?= htmlspecialchars($fc['agency_code_hash'] ?? 'af97d1baebca1eaae1ce418c082402e60c2529ef719983ad7c8dda6ea1f8e8ee') ?>",
  webhook_url: "<?= htmlspecialchars($fc['webhook_url'] ?? '') ?>"
};
if (!CONFIG.room_types || CONFIG.room_types.length === 0) {
  document.getElementById('quote-form-wrap').innerHTML =
    '<div class="qf-error">Il form non è ancora configurato. Contatta Lorenzo direttamente su WhatsApp.</div>';
}
</script>
<!-- ========================================================
     QUOTE FORM SECTION
     ======================================================== -->
<style>
.qf-wrap { max-width:900px; margin:0 auto; font-family:inherit; }
.qf-header {
  background: linear-gradient(135deg,#000744 0%,#000a66 100%);
  color:#fff; padding:28px 32px; border-radius:12px 12px 0 0;
}
.qf-header h2 { margin:0 0 6px; font-size:24px; font-weight:700; }
.qf-header p  { margin:0; opacity:.8; font-size:15px; }
.qf-body { background:#fff; border:2px solid #000744; border-top:none; padding:28px 32px; border-radius:0 0 12px 12px; }
.qf-section-title {
  color:#000744; font-size:20px; font-weight:700;
  display:flex; align-items:center; gap:10px; margin:0 0 18px;
}
.qf-section-title::before {
  content:''; display:inline-block; width:4px; height:24px;
  background:#cc0031; border-radius:2px; flex-shrink:0;
}
.qf-section { margin-bottom:24px; }
.qf-toggle-wrap {
  display:inline-flex; border-radius:50px; overflow:hidden;
  border:2px solid #000744; margin-bottom:24px;
}
.qf-toggle-btn {
  padding:10px 28px; border:none; background:#fff; color:#000744;
  font-weight:600; font-size:15px; cursor:pointer; transition:all .2s;
}
.qf-toggle-btn.active { background:#cc0031; color:#fff; }
.qf-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.qf-field { display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
.qf-label { font-weight:600; color:#000744; font-size:14px; }
.qf-input, .qf-textarea {
  padding:12px 16px; border:2px solid #e0e0e0; border-radius:8px;
  font-size:15px; transition:border-color .2s, box-shadow .2s; font-family:inherit;
}
.qf-input:focus, .qf-textarea:focus {
  outline:none; border-color:#000744;
  box-shadow:0 0 0 3px rgba(0,7,68,.1);
}
.qf-textarea { resize:vertical; min-height:90px; }
.qf-counter-wrap { display:flex; align-items:center; gap:12px; }
.qf-counter-btn {
  width:40px; height:40px; border:2px solid #000744; background:#fff;
  color:#000744; border-radius:8px; font-size:20px; font-weight:bold;
  cursor:pointer; transition:all .2s; display:flex; align-items:center; justify-content:center;
}
.qf-counter-btn:hover:not(:disabled) { background:#000744; color:#fff; }
.qf-counter-btn:disabled { opacity:.3; cursor:default; }
.qf-counter-val { font-size:24px; font-weight:700; color:#000744; min-width:40px; text-align:center; }
.qf-price-box {
  background:linear-gradient(135deg,#000744 0%,#000a66 100%);
  color:#fff; padding:25px; border-radius:12px; margin:24px 0;
  box-shadow:0 8px 16px rgba(0,7,68,.2);
}
.qf-price-line {
  display:flex; justify-content:space-between; padding:10px 0;
  border-bottom:1px solid rgba(255,255,255,.2); font-size:15px;
}
.qf-price-line:last-child { border-bottom:none; }
.qf-total-line {
  background:#fff; border-radius:8px; padding:15px 18px;
  color:#000744; font-size:24px; font-weight:700;
  display:flex; justify-content:space-between; align-items:center; margin-top:16px;
}
.qf-savings {
  border-radius:8px; padding:15px; text-align:center;
  margin-top:15px; font-weight:600; font-size:15px;
}
.qf-savings.positive { background:#fff; border:2px solid #28a745; color:#28a745; }
.qf-savings.neutral  { background:#fff; border:2px solid #000744; color:#000744; }
.qf-checkbox-group {
  padding:16px; background:#f8f9fa; border-radius:8px;
  border:2px solid transparent; transition:border-color .2s; margin-bottom:12px;
}
.qf-checkbox-group:hover { border-color:#000744; }
.qf-checkbox-group label { display:flex; align-items:center; gap:10px; cursor:pointer; font-size:15px; color: #333 !important; }
.qf-checkbox-group label strong { color: #000744 !important; }
.qf-checkbox-group label small { color: #666 !important; }
.qf-checkbox-group input[type=checkbox] { accent-color:#cc0031; width:18px; height:18px; }
.qf-agency-banner {
  background:#f0f8f0; border:1px solid #2ecc71; border-radius:6px;
  padding:12px 16px; margin-bottom:16px; font-size:14px; color:#1a7a40;
}
.qf-submit {
  width:100%; padding:18px; background:linear-gradient(135deg,#cc0031,#e60038);
  color:#fff; border:none; border-radius:8px; font-size:18px; font-weight:700;
  cursor:pointer; box-shadow:0 4px 12px rgba(204,0,49,.3); transition:all .2s;
}
.qf-submit:hover:not(:disabled) { transform:translateY(-2px); box-shadow:0 8px 20px rgba(204,0,49,.4); }
.qf-submit:disabled { opacity:.6; cursor:default; transform:none; }
.qf-error { background:#f8d7da; color:#721c24; border:2px solid #f5c6cb; border-radius:8px; padding:14px 18px; margin-bottom:16px; }
.qf-success { background:#d4edda; color:#155724; border:2px solid #c3e6cb; border-radius:8px; padding:24px; text-align:center; }
.qf-spinner {
  display:inline-block; width:40px; height:40px;
  border:4px solid #f3f3f3; border-top:4px solid #000744;
  border-radius:50%; animation:qfspin .8s linear infinite; margin:20px auto;
}
@keyframes qfspin { to { transform:rotate(360deg); } }
.qf-child-age-input { width:90px; padding:8px 12px; border:2px solid #e0e0e0; border-radius:6px; font-size:14px; margin-top:8px; }
.qf-child-age-input:focus { outline:none; border-color:#000744; }
@media (max-width:768px) {
  .qf-header { padding:20px; }
  .qf-body { padding:20px; }
  .qf-grid { grid-template-columns:1fr; }
  .qf-toggle-wrap { width:100%; }
  .qf-toggle-btn { flex:1; text-align:center; }
  .qf-counter-wrap { justify-content:center; }
}
.whatsapp-cta { color: #333 !important; }
.whatsapp-cta p { color: #333 !important; }
.whatsapp-cta a { color: #cc0031 !important; font-weight: 600; }
</style>

<section class="quote-form-section" id="richiedi-preventivo">
<div class="qf-wrap">
  <div class="qf-header">
    <h2>Richiedi il tuo Preventivo</h2>
    <p>Compila il modulo — Lorenzo ti risponde entro 24 ore.</p>
  </div>
  <div class="qf-body">

    <div id="quote-form-wrap">

      <!-- B2B/B2C toggle -->
      <div style="display:flex;justify-content:center;margin-bottom:24px;">
      <div class="qf-toggle-wrap" style="margin-bottom:0;">
        <button class="qf-toggle-btn active" type="button" data-type="agenzia" id="btn-agenzia">Agenzia</button>
        <button class="qf-toggle-btn" type="button" data-type="privato" id="btn-privato">Privato</button>
      </div>
      </div>

      <div id="form-error-msg" style="display:none;" class="qf-error"></div>

      <form id="quote-form" novalidate>
        <input type="hidden" id="tipo-cliente-hidden" name="tipo_cliente" value="agenzia">

        <!-- B2B Fields -->
        <div id="b2b-fields">
          <div class="qf-section-title">Dati Agenzia</div>

          <!-- 1. Nome Agenzia — full width -->
          <div class="qf-field">
            <label class="qf-label" for="nomeAgenzia">Nome Agenzia *</label>
            <input class="qf-input" type="text" id="nomeAgenzia" name="nome_agenzia" required>
          </div>

          <!-- 2+3. Codice Agenzia + Email Agenzia (50/50) -->
          <div class="qf-grid">
            <div class="qf-field">
              <label class="qf-label" for="codiceAgenzia">Codice Agenzia *</label>
              <input class="qf-input" type="password" id="codiceAgenzia" name="agency_code" autocomplete="off" placeholder="Inserisci il codice agenzia" required>
              <span class="qf-error-text" id="codiceAgenzia-error"></span>
              <div id="agency-code-feedback" style="font-size:13px;margin-top:4px;"></div>
            </div>
            <div class="qf-field">
              <label class="qf-label" for="emailAgenzia">Email Agenzia *</label>
              <input class="qf-input" type="email" id="emailAgenzia" name="email_agenzia" required>
            </div>
          </div>

          <!-- 4+5. Telefono + Nome Cliente Finale (50/50) -->
          <div class="qf-grid" style="margin-top:16px;">
            <div class="qf-field">
              <label class="qf-label" for="telefonoAgenzia">Telefono *</label>
              <input class="qf-input" type="tel" id="telefonoAgenzia" name="telefono" required>
            </div>
            <div class="qf-field">
              <label class="qf-label" for="nomeCliente">Nome Cliente Finale *</label>
              <input class="qf-input" type="text" id="nomeCliente" name="nome_cliente_finale" required>
            </div>
          </div>

          <!-- 6. Checkbox + conditional email -->
          <div style="margin-top:16px;">
            <div class="qf-checkbox-group">
              <label>
                <input type="checkbox" id="inviaEmailCliente" name="invia_al_cliente" value="1" onclick="toggleClientEmail()">
                Invia preventivo anche al cliente
              </label>
            </div>
            <div id="emailClienteBox" class="qf-field" style="display:none;">
              <label class="qf-label" for="emailCliente">Email Cliente *</label>
              <input class="qf-input" type="email" id="emailCliente" name="email_cliente">
            </div>
          </div>

          <!-- 7. Agency guarantee message -->
          <div style="padding:15px;background:#f8f9fa;border-left:4px solid #000744;border-radius:4px;font-size:13px;color:#555;margin-top:16px;">
            🛡️ <strong>Garanzia per le Agenzie:</strong> Non contatteremo mai direttamente il vostro cliente. Qualora in futuro il cliente decidesse di prenotare con noi senza passare dalla vostra agenzia, vi riconosceremo comunque la vostra commissione.
          </div>
        </div>

        <!-- B2C Fields -->
        <div id="b2c-fields" style="display:none;">
          <div class="qf-section-title">I tuoi dati</div>
          <div class="qf-grid">
            <div class="qf-field">
              <label class="qf-label" for="f-nome">Nome *</label>
              <input class="qf-input" type="text" id="f-nome" name="nome">
            </div>
            <div class="qf-field">
              <label class="qf-label" for="f-cognome">Cognome *</label>
              <input class="qf-input" type="text" id="f-cognome" name="cognome">
            </div>
          </div>
          <div class="qf-grid">
            <div class="qf-field">
              <label class="qf-label" for="f-email">Email *</label>
              <input class="qf-input" type="email" id="f-email" name="email">
            </div>
            <div class="qf-field">
              <label class="qf-label" for="f-telefono">Telefono *</label>
              <input class="qf-input" type="tel" id="f-telefono" name="telefono" required>
            </div>
          </div>
        </div>

        <!-- Group Composition -->
        <div class="qf-section" style="margin-top:20px;">
          <div class="qf-section-title">Composizione Gruppo</div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
            <div class="qf-field">
              <label class="qf-label">Adulti</label>
              <div class="qf-counter-wrap">
                <button class="qf-counter-btn" type="button" id="btn-adulti-dec">−</button>
                <span class="qf-counter-val" id="adulti-val">2</span>
                <button class="qf-counter-btn" type="button" id="btn-adulti-inc">+</button>
              </div>
              <input type="hidden" name="adulti" id="adulti-hidden" value="2">
            </div>
            <div class="qf-field" id="bambini-row" style="display:block;">
              <label class="qf-label" style="margin-bottom:10px;display:block;" id="bambini-label">Bambini <small id="bambini-label-age" style="font-weight:400;color:#666;font-size:12px;">(0–17 anni)</small></label>
              <div class="qf-counter-wrap">
                <button class="qf-counter-btn" type="button" id="btn-bambini-dec">−</button>
                <span class="qf-counter-val" id="bambini-val">0</span>
                <button class="qf-counter-btn" type="button" id="btn-bambini-inc">+</button>
              </div>
              <input type="hidden" name="bambini" id="bambini-hidden" value="0">
            </div>
          </div>
          <div id="child-ages" style="display:flex;flex-wrap:wrap;gap:10px;margin-top:16px;"></div>
          <div id="group-error" style="display:none;color:#cc0031;font-size:14px;margin-top:8px;font-weight:600;"></div>
        </div>

        <!-- Insurance -->
        <?php if (!empty($fc['insurance_enabled'])): ?>
        <div class="qf-checkbox-group">
          <label>
            <input type="checkbox" id="cb-assicurazione" name="assicurazione" value="1">
            <span>
              <strong>Aggiungi Assicurazione Viaggio (+<?= (int)($fc['percentuale_assicurazione'] ?? 5) ?>%)</strong><br>
              <small>Protezione completa per il tuo viaggio</small>
            </span>
          </label>
        </div>
        <?php endif; ?>

        <!-- Price Box -->
        <div class="qf-price-box" id="price-box">
          <div id="price-lines"></div>
          <div class="qf-total-line">
            <span>TOTALE FINALE</span>
            <span id="pe-total">€0</span>
          </div>
          <div id="pe-savings" style="display:none;"></div>
        </div>

        <!-- Note -->
        <div class="qf-field">
          <label class="qf-label" for="f-note">Note o domande</label>
          <textarea class="qf-textarea" id="f-note" name="note" placeholder="Hai richieste speciali? Scrivici qui…"></textarea>
        </div>

        <button type="submit" class="qf-submit" id="qf-submit-btn">
          Invia Richiesta di Preventivo
        </button>
      </form>
    </div>

    <!-- WhatsApp CTA -->
    <div class="whatsapp-cta" style="margin-top:24px;">
      <p>Preferisci scrivere su WhatsApp?
        <a href="https://wa.me/<?php echo str_replace([' ','+'], ['',''], WHATSAPP_NUMBER); ?>?text=<?php echo urlencode('Ciao Lorenzo! Sono interessato al viaggio ' . ($trip['title'] ?? '')); ?>" target="_blank" rel="noopener">
          <i class="fa-brands fa-whatsapp"></i> Scrivici ora
        </a>
      </p>
    </div>
  </div>
</div>
</section>
<?php endif; ?>
</div>

</main>

<?php require_once ROOT . '/includes/footer.php'; ?>

<script>
var GATE = {
  slug: <?= json_encode($slug) ?>,
  webhook: <?= json_encode(defined('WAITLIST_WEBHOOK_URL') ? WAITLIST_WEBHOOK_URL : '') ?>
};
function toggleClientEmail() {
  var checkbox = document.getElementById('inviaEmailCliente');
  var box = document.getElementById('emailClienteBox');
  var emailInput = document.getElementById('emailCliente');
  if (!checkbox || !box || !emailInput) return;
  box.style.display = checkbox.checked ? 'block' : 'none';
  emailInput.required = checkbox.checked;
  if (!checkbox.checked) emailInput.value = '';
}
(function () {
  // --- Sticky top bar ---
  var hero   = document.querySelector('.trip-hero');
  var topbar = document.getElementById('trip-topbar');
  var tabs   = document.getElementById('trip-tabs');

  function onScroll() {
    var heroBottom = hero ? hero.getBoundingClientRect().bottom : 0;
    var topbarVisible = heroBottom < 80;
    if (topbar) topbar.classList.toggle('visible', topbarVisible);
    // Keep tabs below topbar when it's visible, below header when not
    if (tabs) tabs.style.top = topbarVisible ? '56px' : '60px';
  }
  window.addEventListener('scroll', onScroll, { passive: true });

  // Populate topbar savings badge (for 2 people, no insurance, as reference)
  if (typeof CONFIG !== 'undefined' && CONFIG.competitor_enabled) {
    var topbarSavings = document.getElementById('topbar-savings');
    if (topbarSavings) {
      var ref_ours = CONFIG.prezzo_base_persona * 2;
      var ref_comp = CONFIG.prezzo_concorrenza_persona * 2;
      var ref_save = ref_comp - ref_ours;
      if (ref_save > 0) {
          topbarSavings.innerHTML = '<i class="fa-solid fa-piggy-bank"></i> Con il Baffo risparmi <strong>€' + ref_save.toLocaleString('it-IT') + '</strong>';
        topbarSavings.style.display = 'inline-flex';
      }
    }
  }

  // --- Tab navigation: smooth scroll with offset ---
  document.querySelectorAll('.trip-tabs__btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.trip-tabs__btn').forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      var target = document.getElementById(btn.dataset.target);
      if (!target) return;
      var topbarH = (topbar && topbar.classList.contains('visible')) ? 56 : 60;
      var offset = topbarH + (tabs ? tabs.offsetHeight : 0) + 8;
      var top = target.getBoundingClientRect().top + window.scrollY - offset;
      window.scrollTo({ top: top, behavior: 'smooth' });
    });
  });

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
  // QUOTE FORM — CONFIG-driven pricing, B2B default, SHA-256 agency code
  // ----------------------------------------------------------------
  <?php if ($has_form): ?>
  (function() {
    var maxPersons = Math.max.apply(null, CONFIG.room_types.map(function(r){ return parseInt(r.replace('X','')); }));
    var adultCount = Math.min(2, maxPersons);
    var childCount = 0;
    document.getElementById('adulti-val').textContent = adultCount;
    document.getElementById('adulti-hidden').value = adultCount;
    var isAgency   = true;
    var agencyUnlocked = false;

    var fmt = function(n) { return new Intl.NumberFormat('it-IT',{style:'currency',currency:'EUR',maximumFractionDigits:0}).format(n); };

    // -- Room type label --
    function roomLabel() {
      var n = adultCount + childCount;
      return 'X' + n;
    }

    // -- Child discount for a given age --
    function childDiscountForAge(age) {
      if (!CONFIG.child_discounts_enabled) return 0;
      for (var i = 0; i < CONFIG.child_discount_brackets.length; i++) {
        var b = CONFIG.child_discount_brackets[i];
        if (age >= b.min_age && age <= b.max_age) return b.discount;
      }
      return 0;
    }

    // -- Child ages array --
    function getChildAges() {
      var ages = [];
      document.querySelectorAll('#child-ages .qf-child-age-input').forEach(function(inp) {
        ages.push(inp.value !== '' ? parseInt(inp.value) : null);
      });
      return ages;
    }

    // -- Rebuild child age inputs --
    function rebuildChildAges() {
      if (!CONFIG.child_discounts_enabled) return;
      var container = document.getElementById('child-ages');
      if (!container) return;
      var existing = container.querySelectorAll('.qf-child-age-input');
      var oldVals = [];
      existing.forEach(function(inp){ oldVals.push(inp.value); });
      container.innerHTML = '';
      for (var i = 0; i < childCount; i++) {
        var wrap = document.createElement('div');
        wrap.className = 'qf-child-age-wrap';
        wrap.style.cssText = 'display:flex;flex-direction:column;gap:4px;';
        var lbl = document.createElement('label');
        lbl.className = 'qf-label';
        var maxBracket = 7;
        if (CONFIG.child_discount_brackets && CONFIG.child_discount_brackets.length > 0) {
          maxBracket = Math.max.apply(null, CONFIG.child_discount_brackets.map(function(b){ return b.max_age; }));
        }
        lbl.textContent = 'Età bambino ' + (i+1) + ' * (0–' + maxBracket + ' anni per sconto)';
        var inp = document.createElement('input');
        inp.type = 'number'; inp.min = 0; inp.max = 7;
        inp.className = 'qf-input qf-child-age-input';
        inp.style.cssText = 'width:130px;padding:10px 14px;';
        inp.placeholder = '0–' + maxBracket + ' anni';
        inp.name = 'eta_bambini[]';
        inp.required = true;
        if (oldVals[i] !== undefined) inp.value = oldVals[i];
        inp.addEventListener('input', updatePrice);
        wrap.appendChild(lbl);
        wrap.appendChild(inp);
        container.appendChild(wrap);
      }
    }

    // -- Pricing logic --
    function calcPricing() {
      var n  = adultCount + childCount;
      var pb = CONFIG.prezzo_base_persona;
      var lines = [];
      var subtotale = 0;

      if (n === 1) {
        lines.push({label: '1 Adulto × ' + fmt(pb), value: pb});
        subtotale += pb;
        if (CONFIG.supplemento_singola) {
          lines.push({label: '➕ Supplemento Singola', value: CONFIG.supplemento_singola});
          subtotale += CONFIG.supplemento_singola;
        }
      } else {
        var primiDue;
        if (adultCount >= 2) {
          primiDue = '2 adulti';
        } else {
          primiDue = '1 adulto + 1 bambino';
        }
        lines.push({label: 'Camera doppia (' + primiDue + ') × ' + fmt(pb), value: pb * 2});
        subtotale += pb * 2;
        if (n >= 3) {
          var p3 = pb - CONFIG.sconto_terzo_letto;
          var tipo3 = (adultCount >= 3) ? 'adulto' : 'bambino';
          lines.push({label: '➕ 3° letto (' + tipo3 + ')', value: p3});
          subtotale += p3;
        }
        if (n >= 4) {
          var p4 = pb - CONFIG.sconto_quarto_letto;
          lines.push({label: '➕ 4° Letto (Adulto/Bambino)', value: p4});
          subtotale += p4;
        }
        if (n >= 5) {
          var p5 = pb - CONFIG.sconto_quinto_letto;
          lines.push({label: '➕ 5° Letto (Adulto/Bambino)', value: p5});
          subtotale += p5;
        }
        // Child discounts
        var ages = getChildAges();
        ages.forEach(function(age, i) {
          if (age !== null && !isNaN(age)) {
            var disc = childDiscountForAge(age);
            if (disc > 0) {
              lines.push({label: '🎒 Sconto Bambino ' + (i+1) + ' (' + age + ' anni)', value: -disc});
              subtotale -= disc;
            }
          }
        });
      }

      return {lines: lines, subtotale: subtotale};
    }

    function calcCompetitor() {
      var n = adultCount + childCount;
      if (n <= 1) return CONFIG.prezzo_concorrenza_persona;
      var ct = CONFIG.prezzo_concorrenza_persona * 2;
      if (n >= 3) ct += CONFIG.prezzo_concorrenza_letti_extra;
      if (n >= 4) ct += CONFIG.prezzo_concorrenza_letti_extra;
      return ct;
    }

    function updateButtonStates() {
      var total = adultCount + childCount;
      var adultIncBtn = document.getElementById('btn-adulti-inc');
      var bambiniIncBtn = document.getElementById('btn-bambini-inc');
      if (adultIncBtn) adultIncBtn.disabled = (adultCount >= maxPersons) || (total >= maxPersons);
      if (bambiniIncBtn) bambiniIncBtn.disabled = (total >= maxPersons);
    }

    function updatePrice() {
      var n = adultCount + childCount;
      var groupErr = document.getElementById('group-error');
      var submitBtn = document.getElementById('qf-submit-btn');

      // Group size check
      if (n > maxPersons) {
        if (groupErr) { groupErr.textContent = 'Massimo ' + maxPersons + ' persone disponibili per questo viaggio.'; groupErr.style.display = 'block'; }
        if (submitBtn) submitBtn.disabled = true;
        return;
      }
      if (n === 1 && CONFIG.room_types.indexOf('X1') === -1) {
        if (groupErr) { groupErr.textContent = 'La camera singola non è disponibile per questo viaggio.'; groupErr.style.display = 'block'; }
        if (submitBtn) submitBtn.disabled = true;
        var pb = document.getElementById('price-box');
        if (pb) pb.style.display = 'none';
        return;
      }
      if (groupErr) groupErr.style.display = 'none';
      if (submitBtn) submitBtn.disabled = false;

      var priceBox = document.getElementById('price-box');
      if (priceBox) priceBox.style.display = 'block';

      var pricing = calcPricing();
      var subtotale = pricing.subtotale;
      var cbAss = document.getElementById('cb-assicurazione');
      var insChecked = cbAss && cbAss.checked;
      var insurance  = insChecked ? Math.round(subtotale * CONFIG.percentuale_assicurazione) : 0;
      var totale     = subtotale + insurance;

      // Build price lines HTML with subtotal row
      var linesHtml = '';
      pricing.lines.forEach(function(l) {
        var valStr = l.value < 0 ? ('−' + fmt(-l.value)) : fmt(l.value);
        linesHtml += '<div class="qf-price-line"><span>' + l.label + '</span><span>' + valStr + '</span></div>';
      });
      // Divider + subtotal
      linesHtml += '<div class="qf-price-line" style="border-top:2px solid rgba(255,255,255,0.4);margin-top:4px;">'
        + '<span><strong>Subtotale:</strong></span><span><strong>' + fmt(subtotale) + '</strong></span></div>';
      if (insChecked) {
        linesHtml += '<div class="qf-price-line"><span>Assicurazione ' + Math.round(CONFIG.percentuale_assicurazione * 100) + '%</span><span>+' + fmt(insurance) + '</span></div>';
      }

      var plEl = document.getElementById('price-lines');
      if (plEl) plEl.innerHTML = linesHtml;

      var peTotal = document.getElementById('pe-total');
      if (peTotal) peTotal.textContent = fmt(totale);

      // Savings
      if (CONFIG.competitor_enabled) {
        var peSavings = document.getElementById('pe-savings');
        var savings = calcCompetitor() - subtotale;
        if (peSavings) {
          if (savings > 0) {
            peSavings.className = 'qf-savings positive';
            peSavings.textContent = '✓ Risparmi ' + fmt(savings) + ' rispetto ai prezzi di mercato';
            peSavings.style.display = 'block';
          } else {
            peSavings.className = 'qf-savings neutral';
            peSavings.textContent = 'Prezzo in linea con il mercato';
            peSavings.style.display = 'block';
          }
        }
      }
      updateButtonStates();
    }
    window.updatePrice = updatePrice;

    // -- Counter logic --
    function setCount(type, val) {
      if (type === 'adulti') {
        adultCount = val;
        document.getElementById('adulti-val').textContent = adultCount;
        document.getElementById('adulti-hidden').value = adultCount;
      } else {
        childCount = val;
        document.getElementById('bambini-val').textContent = childCount;
        document.getElementById('bambini-hidden').value = childCount;
        rebuildChildAges();
      }
      updatePrice();
    }

    document.getElementById('btn-adulti-inc').addEventListener('click', function() {
      if (adultCount + childCount < maxPersons) {
        setCount('adulti', adultCount + 1);
      }
      updateButtonStates();
    });
    document.getElementById('btn-adulti-dec').addEventListener('click', function() {
      if (adultCount > 1) setCount('adulti', adultCount - 1);
      updateButtonStates();
    });
    var bInc = document.getElementById('btn-bambini-inc');
    var bDec = document.getElementById('btn-bambini-dec');
    if (bInc) bInc.addEventListener('click', function() {
      if (adultCount + childCount < maxPersons) setCount('bambini', childCount + 1);
      updateButtonStates();
    });
    if (bDec) bDec.addEventListener('click', function() {
      if (childCount > 0) setCount('bambini', childCount - 1);
      updateButtonStates();
    });

    updatePrice();
    updateButtonStates();

    // Aggiorna label bambini con max età reale da brackets
    var bambiniLabelAge = document.getElementById('bambini-label-age');
    if (bambiniLabelAge) {
      if (CONFIG.child_discounts_enabled && CONFIG.child_discount_brackets && CONFIG.child_discount_brackets.length > 0) {
        var maxB = Math.max.apply(null, CONFIG.child_discount_brackets.map(function(b){ return b.max_age; }));
        bambiniLabelAge.textContent = '(0\u2013' + maxB + ' anni per sconto)';
      } else {
        bambiniLabelAge.textContent = '(0\u201317 anni)';
      }
    }

    var cbAss2 = document.getElementById('cb-assicurazione');
    if (cbAss2) cbAss2.addEventListener('change', updatePrice);

    // -- B2B/B2C toggle --
    document.getElementById('btn-agenzia').addEventListener('click', function() {
      isAgency = true;
      this.classList.add('active');
      document.getElementById('btn-privato').classList.remove('active');
      document.getElementById('tipo-cliente-hidden').value = 'agenzia';
      document.getElementById('b2b-fields').style.display = 'block';
      document.getElementById('b2c-fields').style.display = 'none';
    });
    document.getElementById('btn-privato').addEventListener('click', function() {
      isAgency = false;
      this.classList.add('active');
      document.getElementById('btn-agenzia').classList.remove('active');
      document.getElementById('tipo-cliente-hidden').value = 'privato';
      document.getElementById('b2b-fields').style.display = 'none';
      document.getElementById('b2c-fields').style.display = 'block';
    });

    // -- Form submission --
    document.getElementById('quote-form').addEventListener('submit', function(e) {
      e.preventDefault();
      var errorDiv = document.getElementById('form-error-msg');
      errorDiv.style.display = 'none';
      var submitBtn = document.getElementById('qf-submit-btn');

      // Validate required fields by mode
      if (isAgency) {
        var nomeAg   = (document.getElementById('nomeAgenzia')||{}).value||'';
        var emailAg  = (document.getElementById('emailAgenzia')||{}).value||'';
        var telAg    = (document.getElementById('telefonoAgenzia')||{}).value||'';
        var nomeCliente = (document.getElementById('nomeCliente')||{}).value||'';
        var agencyCodeVal = (document.getElementById('codiceAgenzia')||{}).value||'';
        if (!nomeAg.trim()) { errorDiv.textContent = 'Inserisci il nome agenzia.'; errorDiv.style.display = 'block'; return; }
        if (!agencyUnlocked) {
          if (agencyCodeVal.trim()) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Verifica…';
            crypto.subtle.digest('SHA-256', new TextEncoder().encode(agencyCodeVal))
              .then(function(hashBuf) {
                var hex = Array.from(new Uint8Array(hashBuf)).map(function(b){ return b.toString(16).padStart(2,'0'); }).join('');
                if (hex === CONFIG.agency_code_hash) {
                  agencyUnlocked = true;
                  document.getElementById('quote-form').dispatchEvent(new Event('submit'));
                } else {
                  errorDiv.textContent = 'Codice agenzia non valido.';
                  errorDiv.style.display = 'block';
                  submitBtn.disabled = false;
                  submitBtn.textContent = 'Invia Richiesta di Preventivo';
                }
              });
            return;
          }
          errorDiv.textContent = 'Inserisci il codice agenzia.';
          errorDiv.style.display = 'block';
          return;
        }
        if (!emailAg.trim()) { errorDiv.textContent = 'Inserisci l\'email agenzia.'; errorDiv.style.display = 'block'; return; }
        if (!telAg.trim()) { errorDiv.textContent = 'Inserisci il telefono.'; errorDiv.style.display = 'block'; return; }
        if (!nomeCliente.trim()) { errorDiv.textContent = 'Inserisci il nome del cliente finale.'; errorDiv.style.display = 'block'; return; }
        var sendCl = document.getElementById('inviaEmailCliente');
        if (sendCl && sendCl.checked) {
          var emailCl = (document.getElementById('emailCliente')||{}).value||'';
          if (!emailCl.trim()) { errorDiv.textContent = 'Inserisci l\'email del cliente.'; errorDiv.style.display = 'block'; return; }
        }
      } else {
        var nome    = (document.getElementById('f-nome')||{}).value||'';
        var cognome = (document.getElementById('f-cognome')||{}).value||'';
        var email   = (document.getElementById('f-email')||{}).value||'';
        var tel2    = (document.getElementById('f-telefono')||{}).value||'';
        if (!nome.trim() || !cognome.trim() || !email.trim()) {
          errorDiv.textContent = 'Compila Nome, Cognome ed Email.';
          errorDiv.style.display = 'block';
          return;
        }
        if (!tel2.trim()) {
          errorDiv.textContent = 'Inserisci il numero di telefono.';
          errorDiv.style.display = 'block';
          return;
        }
      }

      // Valida età bambini se sconti abilitati
      if (CONFIG.child_discounts_enabled && childCount > 0) {
        var ageInputsAll = document.querySelectorAll('#child-ages .qf-child-age-input');
        var allAgesFilled = true;
        ageInputsAll.forEach(function(inp) {
          if (inp.value === '' || inp.value === null) allAgesFilled = false;
        });
        if (!allAgesFilled) {
          errorDiv.textContent = 'Inserisci l\'età di tutti i bambini per calcolare correttamente il preventivo.';
          errorDiv.style.display = 'block';
          return;
        }
      }

      if (!CONFIG.webhook_url) {
        errorDiv.textContent = 'Errore di configurazione: webhook non impostato.';
        errorDiv.style.display = 'block';
        return;
      }

      submitBtn.disabled = true;
      submitBtn.textContent = 'Invio in corso…';

      var pricing    = calcPricing();
      var cbAss      = document.getElementById('cb-assicurazione');
      var insChecked = cbAss && cbAss.checked;
      var insurance  = insChecked ? Math.round(pricing.subtotale * CONFIG.percentuale_assicurazione) : 0;
      var totale     = pricing.subtotale + insurance;
      var childAges  = getChildAges();
      var n          = adultCount + childCount;

      var payload = {
        tipo_cliente:             isAgency ? 'agenzia' : 'privato',
        nome_viaggio:             CONFIG.nome_viaggio,
        numero_adulti:            adultCount,
        numero_bambini:           childCount,
        eta_bambini:              childAges.filter(function(a){ return a!==null && !isNaN(a); }).join(', '),
        composizione_camera:      'X' + n,
        prezzo_base_pp:           CONFIG.prezzo_base_persona,
        supplemento_singola:      n === 1 ? CONFIG.supplemento_singola : 0,
        sconto_letti_aggiuntivi:  (n >= 3 ? CONFIG.sconto_terzo_letto : 0) + (n >= 4 ? CONFIG.sconto_quarto_letto : 0) + (n >= 5 ? CONFIG.sconto_quinto_letto : 0),
        sconto_bambini:           pricing.lines.filter(function(l){ return l.value < 0; }).reduce(function(s,l){ return s + (-l.value); }, 0),
        subtotale:                pricing.subtotale,
        assicurazione_percentuale: Math.round(CONFIG.percentuale_assicurazione * 100),
        costo_assicurazione:      insurance,
        totale_finale:            totale,
        assicurazione_inclusa:    insChecked ? 'Si' : 'No',
        note:                     (document.getElementById('f-note')||{}).value||'',
        data_preventivo:          new Date().toLocaleDateString('it-IT'),
      };

      if (isAgency) {
        payload.nome_agenzia       = (document.getElementById('nomeAgenzia')||{}).value||'';
        payload.email_agenzia      = (document.getElementById('emailAgenzia')||{}).value||'';
        payload.telefono           = (document.getElementById('telefonoAgenzia')||{}).value||'';
        payload.nome_cliente_finale= (document.getElementById('nomeCliente')||{}).value||'';
        payload.invia_al_cliente   = document.getElementById('inviaEmailCliente').checked ? 'Si' : 'No';
        payload.email_cliente      = (document.getElementById('emailCliente')||{}).value||'';
      } else {
        payload.nome     = (document.getElementById('f-nome')||{}).value||'';
        payload.cognome  = (document.getElementById('f-cognome')||{}).value||'';
        payload.email    = (document.getElementById('f-email')||{}).value||'';
        payload.telefono = (document.getElementById('f-telefono')||{}).value||'';
      }

      fetch(CONFIG.webhook_url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload)
      })
      .then(function() {
        document.getElementById('quote-form-wrap').innerHTML =
          '<div class="qf-success">' +
          '<i class="fa-solid fa-circle-check" style="font-size:2.5rem;color:#28a745;margin-bottom:1rem;display:block;"></i>' +
          '<h3 style="margin-bottom:.5rem;">Richiesta inviata!</h3>' +
          '<p>Lorenzo ti risponderà entro 24 ore. Grazie per aver scelto Viaggia col Baffo.</p>' +
          '</div>';
      })
      .catch(function() {
        errorDiv.textContent = 'Errore di rete. Controlla la connessione e riprova.';
        errorDiv.style.display = 'block';
        submitBtn.disabled = false;
        submitBtn.textContent = 'Invia Richiesta di Preventivo';
      });
    });

  })(); // end IIFE
  <?php endif; ?>
})();
</script>
