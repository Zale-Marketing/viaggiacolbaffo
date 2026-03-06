<?php
require_once __DIR__ . '/includes/config.php';
require_once ROOT . '/includes/functions.php';

$page_title = 'I Nostri Viaggi — Viaggia col Baffo';
$hero_page  = true;

// Data layer
$all_trips     = array_values(array_filter(load_trips(), fn($t) => $t['published'] === true));
$all_tags_raw  = load_tags();

$continent_slugs = ['america', 'asia', 'europa', 'africa', 'oceania', 'medio-oriente'];
$continents      = array_values(array_filter($all_tags_raw, fn($t) => in_array($t['slug'], $continent_slugs)));
$theme_tags      = array_values(array_filter($all_tags_raw, fn($t) => !in_array($t['slug'], $continent_slugs)));

// URL pre-apply (single tag only — PHP $_GET['tag'] returns last value for repeated keys)
$init_continent = htmlspecialchars($_GET['continent'] ?? '');
$init_tag       = htmlspecialchars($_GET['tag'] ?? '');

require_once ROOT . '/includes/header.php';
?>

<main>

  <!-- ============================================================
       CATALOG HERO
       ============================================================ -->
  <div class="catalog-hero">
    <div class="catalog-hero__overlay"></div>
    <div class="catalog-hero__content">
      <h1 class="catalog-hero__title">I Nostri Viaggi</h1>
      <p class="catalog-hero__sub">Scopri tutti i nostri viaggi organizzati</p>
    </div>
  </div>

  <!-- ============================================================
       FILTER BAR (sticky, dual-row)
       ============================================================ -->
  <div class="filter-bar" id="filter-bar">

    <!-- Row 1: Continents (single-select) -->
    <div class="filter-bar__row">
      <button class="filter-pill <?= $init_continent === '' ? 'filter-pill--active' : '' ?>"
              data-filter-continent="">Tutti</button>
      <?php foreach ($continents as $c): ?>
        <button class="filter-pill <?= $init_continent === $c['slug'] ? 'filter-pill--active' : '' ?>"
                data-filter-continent="<?= htmlspecialchars($c['slug']) ?>">
          <?= htmlspecialchars($c['label']) ?>
        </button>
      <?php endforeach; ?>
    </div>

    <!-- Row 2: Theme tags (multi-select AND logic) -->
    <div class="filter-bar__row">
      <button class="filter-pill <?= $init_tag === '' ? 'filter-pill--active' : '' ?>"
              data-filter-tag="">Tutti</button>
      <?php foreach ($theme_tags as $t): ?>
        <button class="filter-pill <?= $init_tag === $t['slug'] ? 'filter-pill--active' : '' ?>"
                data-filter-tag="<?= htmlspecialchars($t['slug']) ?>">
          <?= htmlspecialchars($t['label']) ?>
        </button>
      <?php endforeach; ?>
    </div>

  </div><!-- /.filter-bar -->

  <!-- ============================================================
       TRIP GRID SECTION
       ============================================================ -->
  <section class="section">
    <div class="container">

      <!-- Count display (between filter bar and grid) -->
      <p class="catalog-count">
        Mostrando <span id="trip-count" class="catalog-count__number"><?= count($all_trips) ?></span> viaggi
      </p>

      <!-- Trip grid -->
      <div id="trips-grid" class="trip-grid">
        <?php foreach ($all_trips as $trip): ?>
        <div class="trip-card-wrapper"
             data-continent="<?= htmlspecialchars($trip['continent']) ?>"
             data-tags="<?= htmlspecialchars(implode(' ', $trip['tags'] ?? [])) ?>">
          <div class="trip-card">
            <img class="trip-card__image"
                 src="<?= htmlspecialchars($trip['hero_image']) ?>"
                 alt="<?= htmlspecialchars($trip['title']) ?>"
                 loading="lazy">
            <div class="trip-card__overlay"></div>
            <span class="trip-card__continent"><?= htmlspecialchars(ucfirst($trip['continent'])) ?></span>
            <span class="trip-card__status status--<?= htmlspecialchars($trip['status']) ?>">
              <?= htmlspecialchars(match($trip['status']) {
                'confermata'   => 'Confermata',
                'ultimi-posti' => 'Ultimi Posti',
                'sold-out'     => 'Sold Out',
                'programmata'  => 'Programmata',
                default        => ucfirst($trip['status'])
              }) ?>
            </span>
            <div class="trip-card__content">
              <h3 class="trip-card__title"><?= htmlspecialchars($trip['title']) ?></h3>
              <p class="trip-card__dates">
                <?= date('j M', strtotime($trip['date_start'])) ?> &ndash;
                <?= date('j M Y', strtotime($trip['date_end'])) ?>
              </p>
              <p class="trip-card__price">Da <?= number_format($trip['price_from'], 0, ',', '.') ?> &euro;</p>
              <a class="trip-card__cta btn btn--gold"
                 href="/viaggio/<?= htmlspecialchars($trip['slug']) ?>">Scopri il viaggio</a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div><!-- /#trips-grid -->

      <!-- Empty state (shown when no trips match active filters) -->
      <div id="empty-state" class="catalog-empty">
        <h2 class="catalog-empty__title">Nessun viaggio trovato</h2>
        <p class="catalog-empty__text">Nessun viaggio trovato per i tuoi filtri. Non trovate quello che cercate? Proponeteci un viaggio su misura!</p>
        <?php if (TALLY_CATALOG_URL): ?>
          <iframe src="<?= htmlspecialchars(TALLY_CATALOG_URL) ?>"
                  title="Richiedi un viaggio su misura"
                  frameborder="0"
                  marginheight="0"
                  marginwidth="0">
          </iframe>
        <?php else: ?>
          <p class="catalog-empty__cta-fallback">Scrivici su <a href="https://wa.me/<?= str_replace(['+', ' '], '', WHATSAPP_NUMBER) ?>">WhatsApp</a> per un viaggio su misura.</p>
        <?php endif; ?>
      </div><!-- /#empty-state -->

    </div><!-- /.container -->
  </section>

</main>

<?php require_once ROOT . '/includes/footer.php'; ?>

<script>
(function() {
  // State — initialized from PHP URL pre-apply
  var activeContinent = '<?= $init_continent ?>';
  var activeTags = <?= $init_tag ? json_encode([$init_tag]) : '[]' ?>;

  var wrappers = Array.from(document.querySelectorAll('.trip-card-wrapper'));
  var countEl  = document.getElementById('trip-count');
  var gridEl   = document.getElementById('trips-grid');
  var emptyEl  = document.getElementById('empty-state');

  function applyFilters() {
    var visible = 0;
    wrappers.forEach(function(w) {
      var continent = w.dataset.continent;
      var tags      = w.dataset.tags ? w.dataset.tags.split(' ') : [];

      var continentMatch = !activeContinent || continent === activeContinent;
      var tagsMatch      = activeTags.every(function(t) { return tags.indexOf(t) !== -1; });

      if (continentMatch && tagsMatch) {
        w.style.display = '';
        visible++;
      } else {
        w.style.display = 'none';
      }
    });

    // Update count with fade transition
    countEl.classList.add('count-fade');
    setTimeout(function() {
      countEl.textContent = visible;
      countEl.classList.remove('count-fade');
    }, 150);

    // Swap grid / empty state
    var hasResults = visible > 0;
    gridEl.style.display  = hasResults ? '' : 'none';
    emptyEl.style.display = hasResults ? 'none' : '';

    // Sync URL params for deep-linking
    var params = new URLSearchParams();
    if (activeContinent) params.set('continent', activeContinent);
    activeTags.forEach(function(t) { params.append('tag', t); });
    var newUrl = params.toString() ? '?' + params.toString() : window.location.pathname;
    history.replaceState(null, '', newUrl);
  }

  // Continent pills (single-select — clicking active deselects)
  document.querySelectorAll('[data-filter-continent]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var val = this.dataset.filterContinent;
      activeContinent = (activeContinent === val) ? '' : val;
      syncPillsContinent();
      applyFilters();
    });
  });

  // Tag pills (multi-select AND logic — empty string = clear all)
  document.querySelectorAll('[data-filter-tag]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var val = this.dataset.filterTag;
      if (val === '') {
        activeTags = [];
      } else {
        var idx = activeTags.indexOf(val);
        if (idx === -1) {
          activeTags.push(val);
        } else {
          activeTags.splice(idx, 1);
        }
      }
      syncPillsTags();
      applyFilters();
    });
  });

  function syncPillsContinent() {
    document.querySelectorAll('[data-filter-continent]').forEach(function(btn) {
      var isActive = btn.dataset.filterContinent === activeContinent && activeContinent !== '';
      var isTutti  = btn.dataset.filterContinent === '' && activeContinent === '';
      btn.classList.toggle('filter-pill--active', isActive || isTutti);
    });
  }

  function syncPillsTags() {
    document.querySelectorAll('[data-filter-tag]').forEach(function(btn) {
      var val      = btn.dataset.filterTag;
      var isActive = val === '' ? activeTags.length === 0 : activeTags.indexOf(val) !== -1;
      btn.classList.toggle('filter-pill--active', isActive);
    });
  }

  // Initialize — apply URL pre-filters immediately (no DOMContentLoaded needed; script is at page bottom)
  syncPillsContinent();
  syncPillsTags();
  applyFilters();
})();
</script>
