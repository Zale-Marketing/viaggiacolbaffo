<?php
require_once __DIR__ . '/includes/config.php';
require_once ROOT . '/includes/functions.php';

$page_title = 'I Nostri Viaggi — Viaggia col Baffo';
$hero_page  = true;

// Data layer
$all_trips    = array_values(array_filter(load_trips(), fn($t) => $t['published'] === true));
$all_tags_raw = load_tags();

// URL pre-apply (new param scheme)
$init_search    = htmlspecialchars($_GET['search']    ?? '');
$init_continent = htmlspecialchars($_GET['continent'] ?? '');
$init_tipo_raw  = trim($_GET['tipo']   ?? '');
$init_mese_raw  = trim($_GET['mese']   ?? '');
$init_per_raw   = trim($_GET['per']    ?? '');
$init_sort      = htmlspecialchars($_GET['sort']      ?? 'date-asc');

$init_tipo = $init_tipo_raw ? array_map('htmlspecialchars', explode(',', $init_tipo_raw)) : [];
$init_mese = $init_mese_raw ? array_map('htmlspecialchars', explode(',', $init_mese_raw)) : [];
$init_per  = $init_per_raw  ? array_map('htmlspecialchars', explode(',', $init_per_raw))  : [];

// Filter categories (from tags.json)
$continent_slugs = ['america', 'asia', 'europa', 'africa', 'oceania', 'medio-oriente'];
$continents = array_values(array_filter($all_tags_raw, fn($t) => in_array($t['slug'], $continent_slugs)));

// Hardcoded ordered options for tipo/mese/per (not all may exist in tags.json yet)
$tipo_options = [
  ['slug' => 'avventura',      'label' => 'Avventura'],
  ['slug' => 'cultura',        'label' => 'Cultura'],
  ['slug' => 'relax',          'label' => 'Relax'],
  ['slug' => 'gastronomia',    'label' => 'Gastronomia'],
  ['slug' => 'road-trip',      'label' => 'Road Trip'],
  ['slug' => 'parchi-naturali','label' => 'Parchi Naturali'],
  ['slug' => 'mare',           'label' => 'Mare'],
  ['slug' => 'neve',           'label' => 'Neve'],
  ['slug' => 'lusso',          'label' => 'Lusso'],
  ['slug' => 'economico',      'label' => 'Economico'],
];

$mese_options = [
  ['slug' => 'gennaio',   'label' => 'Gennaio',   'num' => 1],
  ['slug' => 'febbraio',  'label' => 'Febbraio',  'num' => 2],
  ['slug' => 'marzo',     'label' => 'Marzo',     'num' => 3],
  ['slug' => 'aprile',    'label' => 'Aprile',    'num' => 4],
  ['slug' => 'maggio',    'label' => 'Maggio',    'num' => 5],
  ['slug' => 'giugno',    'label' => 'Giugno',    'num' => 6],
  ['slug' => 'luglio',    'label' => 'Luglio',    'num' => 7],
  ['slug' => 'agosto',    'label' => 'Agosto',    'num' => 8],
  ['slug' => 'settembre', 'label' => 'Settembre', 'num' => 9],
  ['slug' => 'ottobre',   'label' => 'Ottobre',   'num' => 10],
  ['slug' => 'novembre',  'label' => 'Novembre',  'num' => 11],
  ['slug' => 'dicembre',  'label' => 'Dicembre',  'num' => 12],
];

$per_options = [
  ['slug' => 'coppia',   'label' => 'Per Coppia'],
  ['slug' => 'famiglia', 'label' => 'Per Famiglie'],
  ['slug' => 'single',   'label' => 'Single'],
  ['slug' => 'gruppo',   'label' => 'Piccoli Gruppi'],
  ['slug' => 'over-50',  'label' => 'Over 50'],
  ['slug' => 'giovani',  'label' => 'Giovani'],
];

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
       FILTER BAR (sticky, single row)
       ============================================================ -->
  <div class="filter-bar" id="filter-bar">

    <!-- Search -->
    <div class="filter-search" id="filter-search-wrap">
      <input type="text" id="search-trips"
             placeholder="🔍 Cerca destinazione, tema..."
             autocomplete="off"
             value="<?= $init_search ?>">
      <button class="filter-search__clear" id="search-clear" type="button" aria-label="Cancella ricerca">×</button>
    </div>

    <!-- Continente (single select / radio) -->
    <div class="filter-dropdown" id="dd-continent">
      <button class="filter-dropdown__toggle<?= $init_continent ? ' has-selection' : '' ?>"
              id="toggle-continent" type="button">
        <?php
          if ($init_continent) {
            $cont_label = '';
            foreach ($continents as $c) { if ($c['slug'] === $init_continent) { $cont_label = $c['label']; break; } }
            echo htmlspecialchars($cont_label ?: 'Continente');
          } else {
            echo 'Continente';
          }
        ?>
      </button>
      <div class="filter-dropdown__panel" id="panel-continent">
        <label>
          <input type="radio" name="continent" value="">
          Tutti
        </label>
        <?php foreach ($continents as $c): ?>
          <label>
            <input type="radio" name="continent"
                   value="<?= htmlspecialchars($c['slug']) ?>"
                   <?= $init_continent === $c['slug'] ? 'checked' : '' ?>>
            <?= htmlspecialchars($c['label']) ?>
          </label>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Tipo viaggio (multi select / checkbox) -->
    <div class="filter-dropdown" id="dd-tipo">
      <button class="filter-dropdown__toggle<?= $init_tipo ? ' has-selection' : '' ?>"
              id="toggle-tipo" type="button">
        <?= $init_tipo ? 'Tipo (' . count($init_tipo) . ')' : 'Tipo viaggio' ?>
      </button>
      <div class="filter-dropdown__panel" id="panel-tipo">
        <?php foreach ($tipo_options as $t): ?>
          <label>
            <input type="checkbox" name="tipo"
                   value="<?= htmlspecialchars($t['slug']) ?>"
                   <?= in_array($t['slug'], $init_tipo) ? 'checked' : '' ?>>
            <?= htmlspecialchars($t['label']) ?>
          </label>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Mese (multi select / checkbox) -->
    <div class="filter-dropdown" id="dd-mese">
      <button class="filter-dropdown__toggle<?= $init_mese ? ' has-selection' : '' ?>"
              id="toggle-mese" type="button">
        <?= $init_mese ? 'Mese (' . count($init_mese) . ')' : 'Mese' ?>
      </button>
      <div class="filter-dropdown__panel" id="panel-mese">
        <?php foreach ($mese_options as $m): ?>
          <label>
            <input type="checkbox" name="mese"
                   value="<?= htmlspecialchars($m['slug']) ?>"
                   <?= in_array($m['slug'], $init_mese) ? 'checked' : '' ?>>
            <?= htmlspecialchars($m['label']) ?>
          </label>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Per chi (multi select / checkbox) -->
    <div class="filter-dropdown" id="dd-per">
      <button class="filter-dropdown__toggle<?= $init_per ? ' has-selection' : '' ?>"
              id="toggle-per" type="button">
        <?= $init_per ? 'Per chi (' . count($init_per) . ')' : 'Per chi' ?>
      </button>
      <div class="filter-dropdown__panel" id="panel-per">
        <?php foreach ($per_options as $p): ?>
          <label>
            <input type="checkbox" name="per"
                   value="<?= htmlspecialchars($p['slug']) ?>"
                   <?= in_array($p['slug'], $init_per) ? 'checked' : '' ?>>
            <?= htmlspecialchars($p['label']) ?>
          </label>
        <?php endforeach; ?>
      </div>
    </div>

  </div><!-- /.filter-bar -->

  <!-- ============================================================
       SORT BAR
       ============================================================ -->
  <div class="sort-bar">
    <span class="sort-bar__label">Ordina per:</span>
    <button class="sort-pill<?= $init_sort === 'date-asc'   ? ' sort-pill--active' : '' ?>"
            data-sort="date-asc"   type="button">Data partenza ↑</button>
    <button class="sort-pill<?= $init_sort === 'date-desc'  ? ' sort-pill--active' : '' ?>"
            data-sort="date-desc"  type="button">Data partenza ↓</button>
    <button class="sort-pill<?= $init_sort === 'price-asc'  ? ' sort-pill--active' : '' ?>"
            data-sort="price-asc"  type="button">Prezzo ↑</button>
    <button class="sort-pill<?= $init_sort === 'price-desc' ? ' sort-pill--active' : '' ?>"
            data-sort="price-desc" type="button">Prezzo ↓</button>
    <button class="sort-pill<?= $init_sort === 'newest'     ? ' sort-pill--active' : '' ?>"
            data-sort="newest"     type="button">Novità</button>
  </div>

  <!-- ============================================================
       TRIP GRID SECTION
       ============================================================ -->
  <section class="section">
    <div class="container">

      <!-- Results bar -->
      <div class="results-bar">
        <p class="results-bar__count">
          Mostrando <span id="trip-count" class="count-number"><?= count($all_trips) ?></span> viaggi
        </p>
        <button class="filter-reset-btn<?= ($init_search || $init_continent || $init_tipo || $init_mese || $init_per) ? ' is-visible' : '' ?>"
                id="filter-reset" type="button">
          Reset filtri
        </button>
      </div>

      <!-- Trip grid -->
      <div id="trips-grid" class="trip-grid">
        <?php foreach ($all_trips as $idx => $trip):
          $trip_month = (int) date('n', strtotime($trip['date_start']));
          $search_text = strtolower(
            $trip['title'] . ' ' .
            $trip['continent'] . ' ' .
            ($trip['short_description'] ?? '') . ' ' .
            implode(' ', $trip['tags'] ?? [])
          );
        ?>
        <div class="trip-card-wrapper"
             data-continent="<?= htmlspecialchars($trip['continent']) ?>"
             data-tags="<?= htmlspecialchars(implode(' ', $trip['tags'] ?? [])) ?>"
             data-month="<?= $trip_month ?>"
             data-price="<?= (int)($trip['price_from'] ?? 0) ?>"
             data-date="<?= htmlspecialchars($trip['date_start']) ?>"
             data-index="<?= $idx ?>"
             data-search="<?= htmlspecialchars($search_text) ?>">
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

      <!-- Empty state -->
      <div id="empty-state" class="catalog-empty">
        <div class="catalog-empty__icon">
          <i class="fas fa-compass"></i>
        </div>
        <h3 class="catalog-empty__title">Nessun viaggio trovato</h3>
        <p class="catalog-empty__text">Non hai trovato quello che cercavi? Proponi un viaggio su misura!</p>
        <?php if (defined('TALLY_CUSTOM_URL') && TALLY_CUSTOM_URL): ?>
          <button class="catalog-empty__cta"
                  onclick="window.open('<?= htmlspecialchars(TALLY_CUSTOM_URL) ?>', '_blank')">
            Richiedi viaggio personalizzato
          </button>
          <iframe src="<?= htmlspecialchars(TALLY_CUSTOM_URL) ?>"
                  title="Richiedi un viaggio su misura"
                  frameborder="0" marginheight="0" marginwidth="0">
          </iframe>
        <?php elseif (defined('TALLY_CATALOG_URL') && TALLY_CATALOG_URL): ?>
          <button class="catalog-empty__cta"
                  onclick="window.open('<?= htmlspecialchars(TALLY_CATALOG_URL) ?>', '_blank')">
            Richiedi viaggio personalizzato
          </button>
        <?php else: ?>
          <a class="catalog-empty__cta" href="/agenzie.php">
            Richiedi viaggio personalizzato
          </a>
        <?php endif; ?>
      </div><!-- /#empty-state -->

    </div><!-- /.container -->
  </section>

</main>

<?php require_once ROOT . '/includes/footer.php'; ?>

<!-- Pass PHP init state to JS -->
<script>
window.FILTERS_INIT = {
  search:    <?= json_encode($init_search) ?>,
  continent: <?= json_encode($init_continent) ?>,
  tipo:      <?= json_encode(array_values($init_tipo)) ?>,
  mese:      <?= json_encode(array_values($init_mese)) ?>,
  per:       <?= json_encode(array_values($init_per)) ?>,
  sort:      <?= json_encode($init_sort) ?>,
  meseMap:   <?= json_encode(array_column($mese_options, 'num', 'slug')) ?>
};
</script>
<script src="/assets/js/filters.js"></script>
