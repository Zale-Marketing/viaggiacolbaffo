</main>

<?php
$_acfg_path = DATA_DIR . 'admin-config.json';
$_foot_cfg = file_exists($_acfg_path) ? (json_decode(file_get_contents($_acfg_path), true) ?? []) : [];
$company_name    = $_foot_cfg['company_name']    ?? 'Y86 Travel';
$company_vat     = $_foot_cfg['company_vat']     ?? '';
$company_address = $_foot_cfg['company_address'] ?? '';
unset($_acfg_path, $_foot_cfg);
?>

<footer class="site-footer">
  <div class="container site-footer__grid">

    <div class="site-footer__col site-footer__col--brand">
      <a href="/" class="footer-logo" style="display:block;margin-bottom:1rem;"><img src="https://viaggiacolbaffo.com/wp-content/uploads/2025/09/Progetto-senza-titolo-2025-09-30T075103.747-e1759212405873.png" alt="Viaggia col Baffo"></a>
      <p class="site-footer__tagline">Piccoli gruppi, grandi emozioni.<br>Lorenzo con te, ogni giorno.</p>
    </div>

    <div class="site-footer__col">
      <h4 class="site-footer__heading">Naviga</h4>
      <ul class="site-footer__links">
        <li><a href="/viaggi">Viaggi</a></li>
        <li><a href="/destinazioni">Destinazioni</a></li>
        <li><a href="/agenzie">Agenzie</a></li>
        <li><a href="mailto:<?= defined('CONTACT_EMAIL') ? htmlspecialchars(CONTACT_EMAIL) : 'info@viaggiacolbaffo.com' ?>">Contatti</a></li>
      </ul>
    </div>

    <div class="site-footer__col">
      <h4 class="site-footer__heading">Contatti</h4>
      <?php
      $wa_number = defined('WHATSAPP_NUMBER') ? str_replace([' ', '+'], ['', ''], WHATSAPP_NUMBER) : '';
      $wa_display = defined('WHATSAPP_NUMBER') ? WHATSAPP_NUMBER : '+39 XXX XXXXXXX';
      ?>
      <p><a href="tel:<?= htmlspecialchars($wa_display) ?>"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($wa_display) ?></a></p>
      <?php if ($wa_number): ?>
      <p><a href="https://wa.me/<?= htmlspecialchars($wa_number) ?>" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a></p>
      <?php endif; ?>
      <p><a href="mailto:info@viaggiacolbaffo.com"><i class="fa-solid fa-envelope"></i> info@viaggiacolbaffo.com</a></p>
      <div class="site-footer__social">
        <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
        <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
      </div>
      <p class="site-footer__iata"><i class="fa-solid fa-certificate"></i> IATA Accredited Agency</p>
    </div>

  </div>

  <div class="site-footer__bottom">
    <div class="container">
      <p>
        <?php if ($company_vat): ?>P.IVA: <?= htmlspecialchars($company_vat) ?> &mdash; <?php endif; ?>
        &copy; <?= date('Y') ?> Viaggia col Baffo - <?= htmlspecialchars($company_name) ?>. Tutti i diritti riservati.
        <?php if ($company_address): ?><br><?= htmlspecialchars($company_address) ?><?php endif; ?>
      </p>
    </div>
  </div>
</footer>

<script src="/assets/js/main.js"></script>
</body>
</html>
