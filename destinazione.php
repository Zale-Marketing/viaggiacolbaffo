<?php
require_once __DIR__ . '/includes/config.php';
require_once ROOT . '/includes/functions.php';
$destinations = load_destinations();

$slug = $_GET['slug'] ?? '';
$valid_slugs = ['america', 'asia', 'europa', 'africa', 'oceania', 'medio-oriente'];
if (!$slug || !in_array($slug, $valid_slugs)) {
    http_response_code(404);
    $page_title = 'Pagina non trovata — Viaggia Col Baffo';
    require_once ROOT . '/includes/header.php';
    echo '<main style="text-align:center;padding:6rem 1rem;">';
    echo '<h1 style="font-family:\'Playfair Display\',serif;font-size:2.5rem;color:#000744;margin-bottom:1rem;">Pagina non trovata</h1>';
    echo '<p style="color:#555;margin-bottom:2rem;">La destinazione che cerchi non esiste.</p>';
    echo '<a href="/" class="btn btn--gold">Torna alla home</a>';
    echo '</main>';
    require_once ROOT . '/includes/footer.php';
    exit;
}

$dest = $destinations[$slug];
$page_title = $dest['name'] . ' — Viaggia Col Baffo';
$meta_description = substr($dest['intro_paragraphs'][0], 0, 160);

// Trips for this continent (published only)
$continent_trips = array_values(array_filter(
    get_trips_by_continent($slug),
    fn($t) => $t['published'] === true
));
$has_trips = count($continent_trips) > 0;

$hero_page = true;
require_once ROOT . '/includes/header.php';
?>

<main>

<!-- ========================================================
     HERO
     ======================================================== -->
<section class="dest-hero">
  <img class="dest-hero__img"
       src="<?= htmlspecialchars($dest['hero_image']) ?>"
       alt="<?= htmlspecialchars($dest['name']) ?>"
       loading="eager">
  <div class="dest-hero__overlay"></div>
  <div class="dest-hero__content">
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="dest-hero__breadcrumb">
          <li><a href="/" style="color:inherit;text-decoration:none">Home</a></li>
          <li>Destinazioni</li>
          <li><?= htmlspecialchars($dest['name']) ?></li>
        </ol>
      </nav>
      <h1 class="dest-hero__title"><?= htmlspecialchars($dest['name']) ?></h1>
    </div>
  </div>
</section>

<!-- ========================================================
     INTRO EDITORIALE
     ======================================================== -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Scopri <?= htmlspecialchars($dest['name']) ?></h2>
    </div>
    <div style="max-width:800px;margin:0 auto;">
      <?php foreach ($dest['intro_paragraphs'] as $paragraph): ?>
        <p class="dest-intro" style="text-align:left;"><?= htmlspecialchars($paragraph) ?></p>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ========================================================
     INFORMAZIONI PRATICHE
     ======================================================== -->
<section class="section section--dark">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Informazioni Pratiche</h2>
    </div>
    <div class="dest-info-grid">
      <?php foreach ($dest['practical_info'] as $box): ?>
        <div class="dest-info-box">
          <i class="<?= htmlspecialchars($box['icon']) ?> dest-info-box__icon"></i>
          <span class="dest-info-box__label"><?= htmlspecialchars($box['label']) ?></span>
          <span class="dest-info-box__value"><?= htmlspecialchars($box['value']) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ========================================================
     COSA VEDERE
     ======================================================== -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Cosa Vedere</h2>
    </div>
    <div class="dest-cosa-grid">
      <?php foreach ($dest['see_also'] as $place): ?>
        <a href="viaggi.php?continent=<?= htmlspecialchars($slug) ?>" class="dest-cosa-card">
          <img class="dest-cosa-card__img"
               src="<?= htmlspecialchars($place['image']) ?>"
               alt="<?= htmlspecialchars($place['name']) ?>"
               loading="lazy">
          <div class="dest-cosa-card__body">
            <div class="dest-cosa-card__name"><?= htmlspecialchars($place['name']) ?></div>
            <div class="dest-cosa-card__desc"><?= nl2br(htmlspecialchars($place['description'])) ?></div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ========================================================
     CURIOSITA
     ======================================================== -->
<section class="section section--dark">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Curiosit&agrave;</h2>
    </div>
    <div class="dest-curiosita-grid">
      <?php foreach ($dest['curiosita'] as $fact): ?>
        <div class="dest-curiosita-card">
          <i class="<?= htmlspecialchars($fact['icon']) ?> dest-curiosita-card__icon"></i>
          <div class="dest-curiosita-card__title"><?= htmlspecialchars($fact['title']) ?></div>
          <p class="dest-curiosita-card__text"><?= htmlspecialchars($fact['text']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ========================================================
     VIAGGI O WAITLIST
     ======================================================== -->
<?php if ($has_trips): ?>
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">I Nostri Viaggi in <?= htmlspecialchars($dest['name']) ?></h2>
    </div>
    <div class="trip-grid">
      <?php foreach ($continent_trips as $trip): ?>
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
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php else: ?>
<section class="b2b-form-section" style="padding:3rem 0;">
  <style>
    .waitlist-card {
      background: linear-gradient(135deg, #000744 0%, #000a66 100%);
      border-radius: 16px;
      padding: 3rem;
      max-width: 600px;
      margin: 0 auto;
      box-shadow: 0 20px 60px rgba(0,7,68,0.4);
    }
    .waitlist-card__title {
      font-family: 'Playfair Display', serif;
      color: #fff;
      text-align: center;
      font-size: 1.8rem;
      margin: 1rem 0 0.75rem;
    }
    .waitlist-card__sub {
      color: rgba(255,255,255,0.8);
      text-align: center;
      margin-bottom: 2rem;
      line-height: 1.6;
    }
    .waitlist-card .wl-label {
      display: block;
      color: rgba(255,255,255,0.7);
      font-size: 0.85rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 0.4rem;
    }
    .waitlist-card .wl-input {
      background: rgba(255,255,255,0.08);
      border: 1px solid rgba(255,255,255,0.2);
      color: #fff;
      border-radius: 8px;
      padding: 12px 16px;
      width: 100%;
      box-sizing: border-box;
      margin-bottom: 1rem;
      font-size: 1rem;
    }
    .waitlist-card .wl-input::placeholder { color: rgba(255,255,255,0.5); }
    .waitlist-card .wl-input:focus {
      border-color: #CC0031;
      outline: none;
    }
    .waitlist-card .wl-btn {
      background: #CC0031;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 14px 32px;
      width: 100%;
      font-size: 1rem;
      font-weight: 700;
      cursor: pointer;
      transition: background 0.2s, transform 0.2s;
    }
    .waitlist-card .wl-btn:hover {
      background: #a80028;
      transform: translateY(-2px);
    }
    .waitlist-card .wl-success {
      text-align: center;
      color: #fff;
      font-size: 1.1rem;
      padding: 1rem 0;
    }
    .waitlist-card .wl-success i {
      display: block;
      font-size: 3rem;
      color: #CC0031;
      margin-bottom: 1rem;
    }
  </style>

  <div class="waitlist-card">
    <i class="fas fa-map-marked-alt" style="font-size:3rem; color:#CC0031; margin-bottom:1rem; display:block; text-align:center"></i>
    <h2 class="waitlist-card__title">Nessun viaggio attivo per questa destinazione</h2>
    <p class="waitlist-card__sub">Vuoi che organizziamo qualcosa di speciale per te? Lasciaci i tuoi dati e ti contatteremo.</p>

    <form id="waitlist-form">
      <input type="hidden" name="destination_slug" value="<?= htmlspecialchars($slug) ?>">
      <input type="hidden" name="destination_name" value="<?= htmlspecialchars($dest['name']) ?>">

      <label class="wl-label" for="wl-nome">Nome</label>
      <input class="wl-input" type="text" id="wl-nome" name="nome" required placeholder="Il tuo nome">

      <label class="wl-label" for="wl-email">Email</label>
      <input class="wl-input" type="email" id="wl-email" name="email" required placeholder="La tua email">

      <label class="wl-label" for="wl-telefono">Telefono</label>
      <input class="wl-input" type="tel" id="wl-telefono" name="telefono" placeholder="Il tuo numero (opzionale)">

      <button type="submit" class="wl-btn">Iscriviti alla lista d&apos;attesa</button>
    </form>

    <div id="waitlist-success" class="wl-success" style="display:none;">
      <i class="fas fa-check-circle"></i>
      Perfetto! Ti contatteremo presto 🎉
    </div>
  </div>
</section>
<?php endif; ?>

</main>

<script>
(function() {
  const form = document.getElementById('waitlist-form');
  if (!form) return;
  form.addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = this.querySelector('.wl-btn');
    btn.disabled = true;
    btn.textContent = 'Invio in corso...';

    const data = new FormData(this);
    try {
      const res = await fetch('/api/submit-waitlist.php', { method: 'POST', body: data });
      const json = await res.json();
      if (json.success) {
        form.style.display = 'none';
        const success = document.getElementById('waitlist-success');
        if (success) success.style.display = 'block';
      } else {
        throw new Error(json.error || 'Errore');
      }
    } catch (err) {
      btn.disabled = false;
      btn.textContent = "Iscriviti alla lista d'attesa";
      alert('Errore: ' + err.message);
    }
  });
})();
</script>

<?php require_once ROOT . '/includes/footer.php';
