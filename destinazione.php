<?php
require_once __DIR__ . '/includes/config.php';
require_once ROOT . '/includes/functions.php';
require_once ROOT . '/includes/destinations-data.php';

$slug = $_GET['slug'] ?? '';
$valid_slugs = ['america', 'asia', 'europa', 'africa', 'oceania', 'medio-oriente'];
if (!$slug || !in_array($slug, $valid_slugs)) {
    header('Location: /404');
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
    <?php foreach ($dest['intro_paragraphs'] as $paragraph): ?>
      <p class="dest-intro"><?= htmlspecialchars($paragraph) ?></p>
    <?php endforeach; ?>
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
<section class="section">
  <div class="container">
    <?php if ($has_trips): ?>

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

    <?php else: ?>

      <div class="section-header">
        <h2 class="section-header__title">Unisciti alla Lista d&apos;Attesa</h2>
      </div>

      <div class="dest-waitlist">
        <h3 class="dest-waitlist__title">Nessun viaggio disponibile al momento</h3>
        <p class="dest-waitlist__sub">Lascia i tuoi dati e ti avvisiamo appena apriamo nuove partenze per <?= htmlspecialchars($dest['name']) ?>.</p>
        <form id="waitlist-form">
          <input type="hidden" name="destination_slug" value="<?= htmlspecialchars($slug) ?>">
          <input type="hidden" name="destination_name" value="<?= htmlspecialchars($dest['name']) ?>">
          <div class="form-group">
            <label for="wl-nome">Nome *</label>
            <input type="text" id="wl-nome" name="nome" required placeholder="Il tuo nome">
          </div>
          <div class="form-group">
            <label for="wl-email">Email *</label>
            <input type="email" id="wl-email" name="email" required placeholder="La tua email">
          </div>
          <div class="form-group">
            <label for="wl-telefono">Telefono</label>
            <input type="tel" id="wl-telefono" name="telefono" placeholder="Il tuo numero (opzionale)">
          </div>
          <button type="submit" class="btn btn--gold">Iscriviti alla lista d&apos;attesa</button>
        </form>
        <p id="waitlist-msg" class="form-msg" style="display:none;"></p>
      </div>

    <?php endif; ?>
  </div>
</section>

</main>

<script>
(function() {
  const form = document.getElementById('waitlist-form');
  if (!form) return;
  form.addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type=submit]');
    btn.disabled = true;
    btn.textContent = 'Invio in corso...';

    const data = new FormData(this);
    try {
      const res = await fetch('/api/submit-waitlist.php', { method: 'POST', body: data });
      const json = await res.json();
      if (json.success) {
        const msg = document.getElementById('waitlist-msg');
        msg.textContent = 'Grazie! Ti contatteremo appena disponibile.';
        msg.className = 'form-msg form-msg--success';
        msg.style.display = 'block';
        this.reset();
      } else {
        throw new Error(json.error || 'Errore');
      }
    } catch (err) {
      const msg = document.getElementById('waitlist-msg');
      msg.textContent = err.message;
      msg.className = 'form-msg form-msg--error';
      msg.style.display = 'block';
    } finally {
      btn.disabled = false;
      btn.textContent = "Iscriviti alla lista d'attesa";
    }
  });
})();
</script>

<?php require_once ROOT . '/includes/footer.php';
