<?php
require_once __DIR__ . '/includes/config.php';
require_once ROOT . '/includes/functions.php';
require_once ROOT . '/includes/destinations-data.php';

$page_title      = 'Destinazioni — Viaggia Col Baffo';
$meta_description = 'Scopri le destinazioni di Viaggia Col Baffo: America, Asia, Europa, Africa, Oceania e Medio Oriente. Viaggi di gruppo con Lorenzo.';

$hero_page = false;
require_once ROOT . '/includes/header.php';
?>

<section class="section" style="padding-top:8rem">
  <div class="container">
    <div class="section-header">
      <h1 class="section-header__title">Le Nostre Destinazioni</h1>
      <p class="section-header__subtitle">Scegli la tua prossima avventura</p>
    </div>

    <div class="destinazioni-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;">
      <?php foreach ($destinations as $slug => $dest): ?>
        <a href="/destinazione/<?= htmlspecialchars($slug) ?>" class="dest-cosa-card">
          <img
            class="dest-cosa-card__img"
            src="<?= htmlspecialchars($dest['hero_image']) ?>"
            alt="<?= htmlspecialchars($dest['name']) ?>"
            loading="lazy"
            style="aspect-ratio:16/9">
          <div class="dest-cosa-card__body">
            <span class="dest-cosa-card__name"><?= htmlspecialchars($dest['name']) ?></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>

  </div>
</section>

<style>
  @media (max-width: 900px) {
    .destinazioni-grid { grid-template-columns: repeat(2, 1fr) !important; }
  }
  @media (max-width: 560px) {
    .destinazioni-grid { grid-template-columns: 1fr !important; }
  }
</style>

<?php require_once ROOT . '/includes/footer.php';
