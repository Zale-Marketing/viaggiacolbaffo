<?php
require_once __DIR__ . '/includes/config.php';
require_once ROOT . '/includes/functions.php';
$page_title = 'Viaggia col Baffo — Esperienze che cambiano la vita';
$hero_page = true;

$_acfg_path = DATA_DIR . 'admin-config.json';
$_acfg_data = file_exists($_acfg_path) ? (json_decode(file_get_contents($_acfg_path), true) ?? []) : [];
$urgency_text = $_acfg_data['urgency_bar_text'] ?? 'West America Aprile 2026 — Ultimi 5 posti disponibili';
unset($_acfg_path, $_acfg_data);

require_once ROOT . '/includes/header.php';
?>

<!-- HERO SECTION (HOME-01) -->
<section class="hero">
  <div class="hero__overlay"></div>
  <div class="hero__content">
    <h1 class="hero__tagline">Viaggia col Baffo</h1>
    <p class="hero__subline">E non cambi mai più</p>
    <p class="hero__sub">Piccoli gruppi. Lorenzo sempre con te. Un'esperienza che ti cambia davvero.</p>
    <div class="hero__ctas">
      <a href="/viaggi" class="btn btn--gold">Scopri i viaggi</a>
      <a href="/agenzie" class="btn btn--outline-white">Sei un'agenzia?</a>
    </div>
  </div>
</section>

<!-- URGENCY BAR (HOME-02) -->
<div class="urgency-bar">
  <span><i class="fa-solid fa-fire"></i></span>
  <span class="urgency-bar__text"><?= htmlspecialchars($urgency_text) ?></span>
</div>

<!-- I NOSTRI VIAGGI ATTIVI (HOME-03) -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">I Nostri Viaggi Attivi</h2>
      <p class="section-header__subtitle">Piccoli gruppi, esperienze autentiche, Lorenzo con te ogni giorno.</p>
    </div>
    <?php
    $all_trips = load_trips();
    $active_trips = array_values(array_filter($all_trips, fn($t) => $t['published'] === true));
    ?>
    <div class="trips-carousel">
      <?php foreach ($active_trips as $trip): ?>
        <div class="trips-carousel__item">
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
                <?php
                  $ds = $trip['date_start'] ?? '';
                  $de = $trip['date_end'] ?? '';
                  if (!empty($ds) && !empty($de)):
                    echo date('j M', strtotime($ds)) . ' &ndash; ' . date('j M Y', strtotime($de));
                  else:
                    echo 'Date da definire';
                  endif;
                ?>
              </p>
              <p class="trip-card__price">Da <?= number_format($trip['price_from'], 0, ',', '.') ?> &euro;</p>
              <a class="trip-card__cta btn btn--gold"
                 href="/viaggio/<?= htmlspecialchars($trip['slug']) ?>">Scopri il viaggio</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div style="text-align:center;margin-top:2.5rem;">
      <a href="/viaggi" class="btn btn--gold">Vedi tutti i viaggi</a>
    </div>
  </div>
</section>

<!-- ESPLORA LE DESTINAZIONI (HOME-04) -->
<?php
$destinations = [
  ['name' => 'America',       'slug' => 'america',       'tagline' => 'Parchi, canyon e città iconiche',                    'image' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=800'],
  ['name' => 'Asia',          'slug' => 'asia',          'tagline' => 'Templi, mercati e paesaggi mozzafiato',               'image' => 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=800'],
  ['name' => 'Europa',        'slug' => 'europa',        'tagline' => 'Cultura, storia e sapori del vecchio continente',     'image' => 'https://images.unsplash.com/photo-1467269204594-9661b134dd2b?w=800'],
  ['name' => 'Africa',        'slug' => 'africa',        'tagline' => 'Savane, deserti e natura selvaggia',                  'image' => 'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?w=800'],
  ['name' => 'Oceania',       'slug' => 'oceania',       'tagline' => 'Spiagge, reef e terre remote',                       'image' => 'https://images.unsplash.com/photo-1523482580672-f109ba8cb9be?w=800'],
  ['name' => 'Medio Oriente', 'slug' => 'medio-oriente', 'tagline' => 'Antiche civiltà, dune e spezie',                     'image' => 'https://images.unsplash.com/photo-1548991879-4099e6f5b1f8?w=800'],
];
?>
<section class="section section--dark">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Esplora le Destinazioni</h2>
      <p class="section-header__subtitle">Sei continenti, infinite emozioni — con Lorenzo come guida.</p>
    </div>
    <div class="dest-grid">
      <?php foreach ($destinations as $dest): ?>
        <a href="destinazione.php?slug=<?= htmlspecialchars($dest['slug']) ?>" class="dest-card">
          <img src="<?= htmlspecialchars($dest['image']) ?>" alt="<?= htmlspecialchars($dest['name']) ?>" class="dest-card__img" loading="lazy">
          <div class="dest-card__overlay"></div>
          <div class="dest-card__content">
            <h3 class="dest-card__name"><?= htmlspecialchars($dest['name']) ?></h3>
            <p class="dest-card__tagline"><?= htmlspecialchars($dest['tagline']) ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- PERCHÉ VIAGGIARE COL BAFFO (HOME-05) -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Perché viaggiare col Baffo</h2>
      <p class="section-header__subtitle">Non solo un tour operator — una promessa personale.</p>
    </div>
    <div class="why-grid">
      <div class="why-block">
        <div class="why-block__icon"><i class="fa-solid fa-user-tie fa-2x"></i></div>
        <h3 class="why-block__title">Lorenzo sempre con te</h3>
        <p class="why-block__text">Non un tour leader qualsiasi — il fondatore in persona, ogni giorno del viaggio, a condividere ogni emozione.</p>
      </div>
      <div class="why-block">
        <div class="why-block__icon"><i class="fa-solid fa-clock-rotate-left fa-2x"></i></div>
        <h3 class="why-block__title">40 anni di esperienza</h3>
        <p class="why-block__text">Dal 1986, Y86 Travel porta italiani nel mondo con passione autentica e una conoscenza profonda di ogni destinazione.</p>
      </div>
      <div class="why-block">
        <div class="why-block__icon"><i class="fa-solid fa-bag-shopping fa-2x"></i></div>
        <h3 class="why-block__title">Tutto incluso, davvero</h3>
        <p class="why-block__text">Voli, hotel selezionati, trasporti, ingressi — zero sorprese sul conto finale. Il prezzo che vedi è il prezzo che paghi.</p>
      </div>
      <div class="why-block">
        <div class="why-block__icon"><i class="fa-solid fa-headset fa-2x"></i></div>
        <h3 class="why-block__title">Assistenza H24</h3>
        <p class="why-block__text">In viaggio, Lorenzo e il team sono sempre raggiungibili, ovunque nel mondo, per qualsiasi necessità.</p>
      </div>
    </div>
  </div>
</section>

<!-- CHI È IL BAFFO (HOME-06) -->
<section class="section section--dark">
  <div class="container">
    <div class="founder-grid">
      <div>
        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800"
             alt="Lorenzo — il fondatore di Viaggia col Baffo"
             class="founder-portrait"
             loading="lazy">
      </div>
      <div>
        <div class="section-header" style="text-align:left;margin-bottom:1.5rem;">
          <h2 class="section-header__title">Chi è il Baffo</h2>
        </div>
        <p>Lorenzo non è una guida turistica. È il fondatore, l'ideatore e il compagno di viaggio che sarà con te dal primo giorno all'ultimo — con la stessa passione con cui ha attraversato tutti i 48 stati americani.</p>
        <p style="margin-top:1rem;">Dal 1986 con Y86 Travel, ha dedicato la sua vita a portare gli italiani alla scoperta del mondo in modo autentico. IATA accreditato, con decenni di relazioni costruite sul campo.</p>
        <div class="founder-stats">
          <div class="founder-stat">
            <span class="founder-stat__number">48</span>
            <span class="founder-stat__label">Stati USA visitati</span>
          </div>
          <div class="founder-stat">
            <span class="founder-stat__number">1986</span>
            <span class="founder-stat__label">Anno di fondazione</span>
          </div>
          <div class="founder-stat">
            <span class="founder-stat__number">100%</span>
            <span class="founder-stat__label">Presenza personale</span>
          </div>
        </div>
        <p style="margin-top:1rem;font-size:0.9rem;color:var(--grey);"><i class="fa-solid fa-certificate" style="color:var(--gold);margin-right:0.4rem;"></i> IATA Accredited Travel Agent</p>
      </div>
    </div>
  </div>
</section>

<!-- COSA DICONO DI NOI (HOME-07) -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Cosa dicono di noi</h2>
      <p class="section-header__subtitle">Le parole di chi ha viaggiato col Baffo.</p>
    </div>
    <div class="testimonials-grid">
      <div class="testimonial-card">
        <div class="testimonial-card__stars">
          <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
        </div>
        <p class="testimonial-card__text">"Non avevo mai fatto un viaggio così. Lorenzo conosce ogni angolo come casa sua. Tornerò sicuramente con lui. Indimenticabile."</p>
        <div class="testimonial-card__author">
          <strong>Maria R.</strong>
          <span>West America Aprile 2026</span>
        </div>
      </div>
      <div class="testimonial-card">
        <div class="testimonial-card__stars">
          <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
        </div>
        <p class="testimonial-card__text">"40 anni di esperienza si sentono ad ogni tappa. Lorenzo ha anticipato ogni nostra domanda e risolto ogni piccolo intoppo prima che ce ne accorgessimo."</p>
        <div class="testimonial-card__author">
          <strong>Gianluca P.</strong>
          <span>Giappone Classico 2025</span>
        </div>
      </div>
      <div class="testimonial-card">
        <div class="testimonial-card__stars">
          <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
        </div>
        <p class="testimonial-card__text">"Partiti come turisti, tornati come viaggiatori. La differenza è Lorenzo: una persona vera, non una guida di professione."</p>
        <div class="testimonial-card__author">
          <strong>Francesca e Luca D.</strong>
          <span>West America Aprile 2026</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- B2B BANNER (HOME-08) -->
<section class="section section--dark">
  <div class="b2b-banner">
    <div class="b2b-banner__inner">
      <div>
        <h2 class="b2b-banner__title">Sei un'agenzia di viaggi?</h2>
        <p class="b2b-banner__sub">Commissioni fino al 12%, catalogo pronto da vendere, garanzia scritta che i tuoi clienti restano tuoi. Costruiamo qualcosa insieme.</p>
      </div>
      <a href="/agenzie" class="btn btn--gold">Scopri il programma</a>
    </div>
  </div>
</section>

<?php require_once ROOT . '/includes/footer.php'; ?>
