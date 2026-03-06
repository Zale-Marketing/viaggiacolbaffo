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
$has_form    = isset($form_config['prezzo_adulto']) && (int)($form_config['prezzo_adulto']) > 0;

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
  <span class="trip-topbar__name"><?php echo htmlspecialchars($trip['title'] ?? ''); ?></span>
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
     ITINERARY SECTION — TIMELINE
     ======================================================== -->
<section class="trip-section" id="itinerario">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Itinerario</h2>
    </div>
    <div class="timeline">
      <?php foreach (($trip['itinerary'] ?? []) as $day): ?>
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
            <p class="timeline-card__desc"><?php echo htmlspecialchars($day['description']); ?></p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

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

<?php if ($has_form): ?>
<script>
const PREZZO_ADULTO = <?= (int)($form_config['prezzo_adulto'] ?? 4350) ?>;
const SUPPLEMENTO_SINGOLA = <?= (int)($form_config['supplemento_singola'] ?? 1600) ?>;
const PREZZO_TERZO_QUARTO_LETTO = <?= (int)($form_config['prezzo_terzo_letto'] ?? 3000) ?>;
const PERCENTUALE_ASSICURAZIONE = <?= ($form_config['percentuale_assicurazione'] ?? 5) / 100 ?>;
const PREZZO_MEDIO_CONCORRENZA_PER_PERSONA = <?= (int)($form_config['prezzo_concorrenza_per_persona'] ?? 7000) ?>;
const PREZZO_TERZO_QUARTO_LETTO_CONCORRENZA = <?= (int)($form_config['prezzo_terzo_quarto_concorrenza'] ?? 5000) ?>;
const VALID_AGENCY_HASH = '<?= htmlspecialchars($form_config['agency_code_hash'] ?? 'af97d1baebca1eaae1ce418c082402e60c2529ef719983ad7c8dda6ea1f8e8ee') ?>';
const WEBHOOK_URL = '<?= htmlspecialchars($form_config['webhook_url'] ?? '') ?>';
const TRIP_TITLE = '<?= htmlspecialchars($trip['title'] ?? '') ?>';
</script>
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

      <!-- B2B/B2C toggle — default: Agenzia -->
      <div class="client-toggle" id="client-toggle" style="margin-bottom:1.5rem;">
        <button class="client-toggle__btn" data-type="privato" type="button">Privato</button>
        <button class="client-toggle__btn active" data-type="agenzia" type="button">Agenzia</button>
      </div>

      <!-- Agency code entry — visible in Agenzia mode by default -->
      <div class="form-row" id="agency-code-row">
        <label class="form-label" for="f-agency-code">Codice Agenzia *</label>
        <input class="form-input" type="password" id="f-agency-code" name="agency_code"
               placeholder="Inserisci il codice" autocomplete="off">
        <div id="agency-code-feedback" style="font-size:0.8rem;margin-top:0.35rem;"></div>
      </div>

      <div id="form-error-msg" class="form-error" style="display:none;"></div>

      <form id="quote-form" novalidate>

        <input type="hidden" name="trip_slug" value="<?php echo htmlspecialchars($trip['slug']); ?>">
        <input type="hidden" name="trip_title" value="<?php echo htmlspecialchars($trip['title']); ?>">
        <input type="hidden" name="tipo_cliente" id="tipo-cliente-hidden" value="agenzia">

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

        <!-- Adulti counter -->
        <div class="form-row">
          <label class="form-label">Adulti *</label>
          <div class="counter-input">
            <button class="counter-btn" type="button" data-counter="adulti" data-action="dec">−</button>
            <span class="counter-val" id="adulti-val">2</span>
            <button class="counter-btn" type="button" data-counter="adulti" data-action="inc">+</button>
          </div>
          <input type="hidden" name="adulti" id="adulti-hidden" value="2">
        </div>

        <!-- Bambini counter -->
        <div class="form-row">
          <label class="form-label">Bambini <span style="font-size:0.8em;color:var(--grey)">(max 4 in totale)</span></label>
          <div class="counter-input">
            <button class="counter-btn" type="button" data-counter="bambini" data-action="dec">−</button>
            <span class="counter-val" id="bambini-val">0</span>
            <button class="counter-btn" type="button" data-counter="bambini" data-action="inc">+</button>
          </div>
          <input type="hidden" name="bambini" id="bambini-hidden" value="0">
          <div class="child-ages" id="child-ages"></div>
        </div>

        <!-- Insurance checkbox -->
        <div class="form-row addon-item" style="margin-top:0.5rem;">
          <input type="checkbox" id="cb-assicurazione" name="assicurazione" value="1">
          <label class="addon-item__label" for="cb-assicurazione">Aggiungi assicurazione viaggio</label>
          <span class="addon-item__price" id="assicurazione-price-label"></span>
        </div>

        <!-- Price estimate box -->
        <div class="price-estimate" id="price-estimate">
          <div class="price-estimate__total" id="pe-total">€0</div>
          <div class="price-estimate__breakdown" id="pe-breakdown"></div>
          <div class="price-estimate__savings" id="pe-savings" style="display:none;"></div>
        </div>

        <!-- Agency fields — unlocked by valid code -->
        <div class="agency-fields" id="agency-fields" style="display:none;">
          <div class="form-row">
            <div style="background:#f0f8f0;border:1px solid #2ecc71;border-radius:6px;padding:0.75rem 1rem;margin-bottom:1rem;font-size:0.85rem;color:#1a7a40;">
              <i class="fa-solid fa-shield-halved"></i> Preventivo riservato agenzie — prezzi netto B2B garantiti.
            </div>
          </div>
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
          <!-- Send quote to client too -->
          <div class="form-row addon-item">
            <input type="checkbox" id="cb-send-cliente" name="invia_al_cliente" value="1">
            <label class="addon-item__label" for="cb-send-cliente">Invia preventivo anche al cliente</label>
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
        <a href="https://wa.me/<?php echo str_replace([' ','+'], ['',''], WHATSAPP_NUMBER); ?>?text=<?php echo urlencode('Ciao Lorenzo! Sono interessato al viaggio ' . ($trip['title'] ?? '')); ?>" target="_blank" rel="noopener">
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
  // QUOTE FORM — parameter-based pricing, B2B default, SHA-256 agency code
  // ----------------------------------------------------------------
  <?php if ($has_form): ?>
  var adultCount = 2;
  var childCount = 0;

  // -- Child age inputs --
  function rebuildChildAges() {
    var container = document.getElementById('child-ages');
    container.innerHTML = '';
    for (var i = 0; i < childCount; i++) {
      var inp = document.createElement('input');
      inp.type        = 'number';
      inp.name        = 'eta_bambini[]';
      inp.className   = 'form-input child-age-input';
      inp.placeholder = 'Età bambino ' + (i + 1);
      inp.min         = 0;
      inp.max         = 17;
      container.appendChild(inp);
    }
  }

  // -- Base price (without insurance) --
  function calcBase() {
    var totalPersons  = adultCount + childCount;
    // Special rule: 1 adult + 1 child → child pays adult price (treated as 2 adults)
    var effectiveTotal = (adultCount === 1 && childCount === 1) ? 2 : totalPersons;
    var total = 0;

    if (adultCount === 1 && childCount === 0) {
      total = PREZZO_ADULTO + SUPPLEMENTO_SINGOLA;
    } else {
      total = PREZZO_ADULTO * 2;
      if (effectiveTotal >= 3) total += PREZZO_TERZO_QUARTO_LETTO;
      if (effectiveTotal >= 4) total += PREZZO_TERZO_QUARTO_LETTO;
    }
    return total;
  }

  // -- Competitor total for savings comparison --
  function calcCompetitor() {
    var totalPersons   = adultCount + childCount;
    var effectiveTotal = (adultCount === 1 && childCount === 1) ? 2 : totalPersons;
    if (effectiveTotal <= 1) return PREZZO_MEDIO_CONCORRENZA_PER_PERSONA;
    var ct = PREZZO_MEDIO_CONCORRENZA_PER_PERSONA * 2;
    if (effectiveTotal >= 3) ct += PREZZO_TERZO_QUARTO_LETTO_CONCORRENZA;
    if (effectiveTotal >= 4) ct += PREZZO_TERZO_QUARTO_LETTO_CONCORRENZA;
    return ct;
  }

  // -- Update price display --
  function updatePrice() {
    var base       = calcBase();
    var insChecked = document.getElementById('cb-assicurazione') && document.getElementById('cb-assicurazione').checked;
    var insurance  = insChecked ? Math.round(base * PERCENTUALE_ASSICURAZIONE) : 0;
    var total      = base + insurance;

    // Build breakdown lines
    var totalPersons   = adultCount + childCount;
    var effectiveTotal = (adultCount === 1 && childCount === 1) ? 2 : totalPersons;
    var lines = [];
    if (adultCount === 1 && childCount === 0) {
      lines.push('€' + (PREZZO_ADULTO + SUPPLEMENTO_SINGOLA).toLocaleString('it-IT') + ' (adulto singolo + suppl.)');
    } else if (adultCount === 1 && childCount === 1) {
      lines.push('€' + PREZZO_ADULTO.toLocaleString('it-IT') + ' × 2 (bambino come adulto)');
    } else {
      lines.push('€' + PREZZO_ADULTO.toLocaleString('it-IT') + ' × 2 adulti');
      if (effectiveTotal >= 3) {
        var s3 = PREZZO_TERZO_QUARTO_LETTO >= 0 ? '+€' : '−€';
        lines.push(s3 + Math.abs(PREZZO_TERZO_QUARTO_LETTO).toLocaleString('it-IT') + ' (3° posto)');
      }
      if (effectiveTotal >= 4) {
        var s4 = PREZZO_TERZO_QUARTO_LETTO >= 0 ? '+€' : '−€';
        lines.push(s4 + Math.abs(PREZZO_TERZO_QUARTO_LETTO).toLocaleString('it-IT') + ' (4° posto)');
      }
    }
    if (insChecked) {
      lines.push('+€' + insurance.toLocaleString('it-IT') + ' assicurazione (' + Math.round(PERCENTUALE_ASSICURAZIONE * 100) + '%)');
    }

    var peTotal   = document.getElementById('pe-total');
    var peBreak   = document.getElementById('pe-breakdown');
    var peSavings = document.getElementById('pe-savings');
    var insLabel  = document.getElementById('assicurazione-price-label');

    if (peTotal) peTotal.textContent = '€' + total.toLocaleString('it-IT');
    if (peBreak) peBreak.textContent = lines.join('  ·  ');
    if (insLabel) insLabel.textContent = '+€' + Math.round(base * PERCENTUALE_ASSICURAZIONE).toLocaleString('it-IT');

    if (peSavings && PREZZO_MEDIO_CONCORRENZA_PER_PERSONA > 0) {
      var savings = calcCompetitor() - total;
      if (savings > 0) {
        peSavings.textContent = '✓ Risparmi €' + savings.toLocaleString('it-IT') + ' rispetto ai prezzi di mercato';
        peSavings.style.display = 'block';
      } else {
        peSavings.style.display = 'none';
      }
    }
  }

  // -- Counter buttons (max 4 total) --
  document.querySelectorAll('.counter-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var counter = btn.dataset.counter;
      var action  = btn.dataset.action;
      if (counter === 'adulti') {
        var next = adultCount + (action === 'inc' ? 1 : -1);
        if (next < 1 || next + childCount > 4) return;
        adultCount = next;
        document.getElementById('adulti-val').textContent = adultCount;
        document.getElementById('adulti-hidden').value    = adultCount;
      } else {
        var nextC = childCount + (action === 'inc' ? 1 : -1);
        if (nextC < 0 || adultCount + nextC > 4) return;
        childCount = nextC;
        document.getElementById('bambini-val').textContent = childCount;
        document.getElementById('bambini-hidden').value    = childCount;
        rebuildChildAges();
      }
      updatePrice();
    });
  });

  // Insurance toggle
  var cbAss = document.getElementById('cb-assicurazione');
  if (cbAss) cbAss.addEventListener('change', updatePrice);

  updatePrice(); // Initial render

  // -- Agency code SHA-256 validation --
  function bufToHex(buf) {
    return Array.from(new Uint8Array(buf))
      .map(function (b) { return b.toString(16).padStart(2, '0'); })
      .join('');
  }

  function validateAgencyCode(codeValue) {
    var feedback     = document.getElementById('agency-code-feedback');
    var agencyFields = document.getElementById('agency-fields');
    if (!agencyFields) return;

    if (!VALID_AGENCY_HASH) {
      agencyFields.style.display = codeValue.trim() ? 'block' : 'none';
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
      if (hex === VALID_AGENCY_HASH) {
        agencyFields.style.display = 'block';
        if (feedback) { feedback.textContent = 'Codice valido — campi agenzia sbloccati.'; feedback.style.color = '#2ecc71'; }
      } else {
        agencyFields.style.display = 'none';
        if (feedback) { feedback.textContent = 'Codice non valido.'; feedback.style.color = '#CC0031'; }
      }
    });
  }

  var agencyCodeInput = document.getElementById('f-agency-code');
  if (agencyCodeInput) {
    agencyCodeInput.addEventListener('input', function () { validateAgencyCode(agencyCodeInput.value); });
  }

  // -- B2B/B2C toggle --
  document.querySelectorAll('.client-toggle__btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.client-toggle__btn').forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      var type          = btn.dataset.type;
      var agencyCodeRow = document.getElementById('agency-code-row');
      var agencyFields  = document.getElementById('agency-fields');
      var feedback      = document.getElementById('agency-code-feedback');
      document.getElementById('tipo-cliente-hidden').value = type;

      if (type === 'agenzia') {
        if (agencyCodeRow) agencyCodeRow.style.display = 'block';
        if (agencyCodeInput) validateAgencyCode(agencyCodeInput.value);
      } else {
        if (agencyCodeRow) agencyCodeRow.style.display = 'none';
        if (agencyFields)  agencyFields.style.display  = 'none';
        if (feedback) { feedback.textContent = ''; feedback.style.color = ''; }
      }
    });
  });

  // -- Form submission → direct webhook POST --
  var quoteForm = document.getElementById('quote-form');
  if (quoteForm) {
    quoteForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var errorDiv = document.getElementById('form-error-msg');
      errorDiv.style.display = 'none';

      var nome    = document.getElementById('f-nome').value.trim();
      var cognome = document.getElementById('f-cognome').value.trim();
      var email   = document.getElementById('f-email').value.trim();
      if (!nome || !cognome || !email) {
        errorDiv.textContent = 'Compila i campi obbligatori: Nome, Cognome, Email.';
        errorDiv.style.display = 'block';
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }
      if (!WEBHOOK_URL) {
        errorDiv.textContent = 'Errore di configurazione: webhook non impostato.';
        errorDiv.style.display = 'block';
        return;
      }

      var submitBtn = quoteForm.querySelector('button[type=submit]');
      submitBtn.disabled    = true;
      submitBtn.textContent = 'Invio in corso…';

      var base       = calcBase();
      var insChecked = document.getElementById('cb-assicurazione').checked;
      var insurance  = insChecked ? Math.round(base * PERCENTUALE_ASSICURAZIONE) : 0;
      var totalPrice = base + insurance;

      var childAges = [];
      document.querySelectorAll('.child-age-input').forEach(function (inp) {
        if (inp.value) childAges.push(inp.value);
      });

      var payload = {
        trip_slug:        document.querySelector('[name=trip_slug]').value,
        trip_title:       TRIP_TITLE,
        tipo_cliente:     document.getElementById('tipo-cliente-hidden').value,
        nome:             nome,
        cognome:          cognome,
        email:            email,
        telefono:         document.getElementById('f-telefono').value.trim(),
        adulti:           adultCount,
        bambini:          childCount,
        eta_bambini:      childAges.join(', '),
        assicurazione:    insChecked ? 'Si' : 'No',
        prezzo_totale:    totalPrice,
        note:             document.getElementById('f-note').value.trim(),
      };

      var nomeAg = document.getElementById('f-nome-agenzia');
      if (nomeAg)  payload.nome_agenzia  = nomeAg.value.trim();
      var iataEl = document.getElementById('f-iata');
      if (iataEl)  payload.codice_iata   = iataEl.value.trim();
      var cittaEl = document.getElementById('f-citta');
      if (cittaEl) payload.citta         = cittaEl.value.trim();
      var commEl  = document.getElementById('f-commissione');
      if (commEl)  payload.commissione   = commEl.value.trim();
      var sendCl  = document.getElementById('cb-send-cliente');
      if (sendCl)  payload.invia_al_cliente = sendCl.checked ? 'Si' : 'No';

      fetch(WEBHOOK_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      })
      .then(function () {
        document.getElementById('quote-form-wrap').innerHTML =
          '<div class="form-success">' +
          '<i class="fa-solid fa-circle-check" style="font-size:2.5rem;color:#2ecc71;margin-bottom:1rem;display:block;"></i>' +
          '<h3 style="font-family:var(--font-heading);margin-bottom:0.5rem;">Richiesta inviata!</h3>' +
          '<p>Lorenzo ti risponderà entro 24 ore. Grazie per aver scelto Viaggia col Baffo.</p>' +
          '</div>';
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
