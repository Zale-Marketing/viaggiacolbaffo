<?php
require_once __DIR__ . '/includes/config.php';
require_once ROOT . '/includes/functions.php';

$page_title = 'I Nostri Viaggi — Viaggia col Baffo';
$hero_page  = true;

// Data layer
$all_trips    = array_values(array_filter(load_trips(), fn($t) => $t['published'] === true));
$all_tags_raw = load_tags();

// Categorize tags into 4 filter groups
$continent_slugs   = ['america', 'asia', 'europa', 'africa', 'oceania', 'medio-oriente'];
$travel_type_slugs = ['road-trip', 'avventura', 'cultura', 'gastronomia', 'parchi-naturali', 'relax'];
$period_slugs      = ['aprile', 'maggio', 'giugno', 'settembre', 'ottobre', 'primavera'];
$group_type_slugs  = ['coppia', 'famiglia', 'gruppo'];

$continents   = array_values(array_filter($all_tags_raw, fn($t) => in_array($t['slug'], $continent_slugs)));
$travel_types = array_values(array_filter($all_tags_raw, fn($t) => in_array($t['slug'], $travel_type_slugs)));
$periods      = array_values(array_filter($all_tags_raw, fn($t) => in_array($t['slug'], $period_slugs)));
$group_types  = array_values(array_filter($all_tags_raw, fn($t) => in_array($t['slug'], $group_type_slugs)));

// URL pre-apply
$init_continent = htmlspecialchars($_GET['continent'] ?? '');
$init_type      = htmlspecialchars($_GET['type']      ?? '');
$init_period    = htmlspecialchars($_GET['period']    ?? '');
$init_group     = htmlspecialchars($_GET['group']     ?? '');

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
       FILTER BAR (sticky, single row of dropdowns)
       ============================================================ -->
  <div class="filter-bar" id="filter-bar">
    <div class="filter-bar__dropdowns">

      <div class="filter-dropdown">
        <label for="filter-continent">Destinazione</label>
        <select id="filter-continent">
          <option value="">Tutte le destinazioni</option>
          <?php foreach ($continents as $c): ?>
            <option value="<?= htmlspecialchars($c['slug']) ?>"
              <?= $init_continent === $c['slug'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['label']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="filter-dropdown">
        <label for="filter-type">Tipo di viaggio</label>
        <select id="filter-type">
          <option value="">Tutti i tipi</option>
          <?php foreach ($travel_types as $t): ?>
            <option value="<?= htmlspecialchars($t['slug']) ?>"
              <?= $init_type === $t['slug'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($t['label']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="filter-dropdown">
        <label for="filter-period">Periodo</label>
        <select id="filter-period">
          <option value="">Tutti i periodi</option>
          <?php foreach ($periods as $p): ?>
            <option value="<?= htmlspecialchars($p['slug']) ?>"
              <?= $init_period === $p['slug'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($p['label']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="filter-dropdown">
        <label for="filter-group">Per chi</label>
        <select id="filter-group">
          <option value="">Tutti</option>
          <?php foreach ($group_types as $g): ?>
            <option value="<?= htmlspecialchars($g['slug']) ?>"
              <?= $init_group === $g['slug'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($g['label']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <button class="filter-reset-btn" id="filter-reset" type="button">Azzera filtri</button>

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
  var selContinent = document.getElementById('filter-continent');
  var selType      = document.getElementById('filter-type');
  var selPeriod    = document.getElementById('filter-period');
  var selGroup     = document.getElementById('filter-group');
  var resetBtn     = document.getElementById('filter-reset');

  var wrappers = Array.from(document.querySelectorAll('.trip-card-wrapper'));
  var countEl  = document.getElementById('trip-count');
  var gridEl   = document.getElementById('trips-grid');
  var emptyEl  = document.getElementById('empty-state');

  function applyFilters() {
    var continent = selContinent.value;
    var type      = selType.value;
    var period    = selPeriod.value;
    var group     = selGroup.value;

    var visible = 0;
    wrappers.forEach(function(w) {
      var wContinent = w.dataset.continent;
      var wTags      = w.dataset.tags ? w.dataset.tags.split(' ') : [];

      var m1 = !continent || wContinent === continent;
      var m2 = !type      || wTags.indexOf(type)   !== -1;
      var m3 = !period    || wTags.indexOf(period)  !== -1;
      var m4 = !group     || wTags.indexOf(group)   !== -1;

      if (m1 && m2 && m3 && m4) {
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

    // Toggle grid / empty state
    var hasResults = visible > 0;
    gridEl.style.display  = hasResults ? '' : 'none';
    emptyEl.style.display = hasResults ? 'none' : 'block';

    // Highlight active dropdowns
    [selContinent, selType, selPeriod, selGroup].forEach(function(sel) {
      sel.classList.toggle('filter-active', sel.value !== '');
    });

    // Sync URL for deep-linking
    var params = new URLSearchParams();
    if (continent) params.set('continent', continent);
    if (type)      params.set('type',      type);
    if (period)    params.set('period',    period);
    if (group)     params.set('group',     group);
    var newUrl = params.toString() ? '?' + params.toString() : window.location.pathname;
    history.replaceState(null, '', newUrl);
  }

  // Bind dropdowns
  [selContinent, selType, selPeriod, selGroup].forEach(function(sel) {
    sel.addEventListener('change', applyFilters);
  });

  // Reset button
  resetBtn.addEventListener('click', function() {
    selContinent.value = '';
    selType.value      = '';
    selPeriod.value    = '';
    selGroup.value     = '';
    applyFilters();
  });

  // Initialize — apply URL pre-filters immediately
  applyFilters();
})();
</script>
