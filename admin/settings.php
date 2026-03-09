<?php
/**
 * admin/settings.php — Site configuration editor
 * Saves all settings to data/admin-config.json using the flock pattern.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$config_file = DATA_DIR . 'admin-config.json';
$error   = '';
$success = isset($_GET['saved']) && $_GET['saved'] === '1';

// ── Load current config ───────────────────────────────────────────────────────
function load_admin_config(): array {
    global $config_file;
    if (!file_exists($config_file)) return [];
    return json_decode(file_get_contents($config_file), true) ?? [];
}

$config = load_admin_config();

// ── POST handler ──────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read existing config or start fresh
    $new_config = $config;

    // Merge all submitted fields (except password which is handled separately)
    $text_fields = [
        'anthropic_api_key',
        'default_webhook_url',
        'waitlist_webhook_url',
        'b2b_webhook_url',
        'whatsapp_number',
        'tally_catalog_url',
        'tally_b2b_url',
        'urgency_bar_text',
        'company_name',
        'company_vat',
        'company_address',
    ];

    foreach ($text_fields as $field) {
        $new_config[$field] = $_POST[$field] ?? '';
    }

    // Password: only update if non-empty
    if (!empty($_POST['admin_password'])) {
        $new_config['admin_password'] = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);
    }
    // If empty string submitted → keep existing password unchanged
    // (already preserved since we started from $config)

    // Write using flock pattern
    $json = json_encode($new_config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $fp   = fopen($config_file, 'w');
    if ($fp) {
        flock($fp, LOCK_EX);
        fwrite($fp, $json);
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    header('Location: settings.php?saved=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impostazioni — Admin Viaggia Col Baffo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<!-- ── Admin navigation ── -->
<nav class="admin-nav">
    <span class="admin-nav__logo"><span class="logo-icon"><i class="fa-solid fa-compass"></i></span> Viaggia col Baffo</span>
    <ul class="admin-nav__links">
        <li><a href="index.php" class="admin-nav__link">Pannello</a></li>
        <li><a href="tags.php" class="admin-nav__link">Tag</a></li>
        <li><a href="destinations.php" class="admin-nav__link">Destinazioni</a></li>
        <li><a href="settings.php" class="admin-nav__link admin-nav__link--active">Impostazioni</a></li>
    </ul>
    <div class="admin-nav__actions">
        <a href="../index.php" target="_blank" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Vai al sito
        </a>
        <a href="logout.php" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>
</nav>

<!-- ── Page content ── -->
<div class="admin-wrapper">
    <div class="admin-page-header">
        <h1 class="admin-page-title">Impostazioni</h1>
        <p class="admin-page-subtitle">Configurazione generale del sito</p>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i> Impostazioni salvate.
    </div>
    <?php endif; ?>

    <form method="POST" action="settings.php">

        <!-- Section 1: Sicurezza -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card__header">
                <h2 class="card__title"><i class="fa-solid fa-lock"></i> Sicurezza</h2>
            </div>
            <div class="card__body">
                <div class="form-group">
                    <label class="form-label" for="admin_password">Password admin</label>
                    <input
                        type="password"
                        id="admin_password"
                        name="admin_password"
                        class="form-control"
                        placeholder="Lascia vuoto per non modificare"
                        autocomplete="new-password"
                    >
                    <p class="form-hint">La password attuale è quella in config.php finché non ne imposti una qui.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Contenuto Sito -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card__header">
                <h2 class="card__title"><i class="fa-solid fa-pen-to-square"></i> Contenuto Sito</h2>
            </div>
            <div class="card__body">
                <div class="form-group">
                    <label class="form-label" for="urgency_bar_text">Testo barra urgenza</label>
                    <input
                        type="text"
                        id="urgency_bar_text"
                        name="urgency_bar_text"
                        class="form-control"
                        value="<?= htmlspecialchars($config['urgency_bar_text'] ?? '') ?>"
                        placeholder="Testo mostrato sotto l'hero in homepage"
                    >
                    <p class="form-hint">Mostrato nella barra rossa sotto l'hero in homepage.</p>
                </div>
                <div class="form-group">
                    <label class="form-label" for="company_name">Nome azienda</label>
                    <input
                        type="text"
                        id="company_name"
                        name="company_name"
                        class="form-control"
                        value="<?= htmlspecialchars($config['company_name'] ?? '') ?>"
                    >
                </div>
                <div class="form-group">
                    <label class="form-label" for="company_vat">P.IVA</label>
                    <input
                        type="text"
                        id="company_vat"
                        name="company_vat"
                        class="form-control"
                        value="<?= htmlspecialchars($config['company_vat'] ?? '') ?>"
                    >
                </div>
                <div class="form-group">
                    <label class="form-label" for="company_address">Indirizzo</label>
                    <input
                        type="text"
                        id="company_address"
                        name="company_address"
                        class="form-control"
                        value="<?= htmlspecialchars($config['company_address'] ?? '') ?>"
                    >
                </div>
            </div>
        </div>

        <!-- Section 3: Webhook URL -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card__header">
                <h2 class="card__title"><i class="fa-solid fa-webhook"></i> Webhook URL</h2>
            </div>
            <div class="card__body">
                <div class="form-group">
                    <label class="form-label" for="default_webhook_url">Webhook preventivo (default)</label>
                    <input
                        type="text"
                        id="default_webhook_url"
                        name="default_webhook_url"
                        class="form-control"
                        value="<?= htmlspecialchars($config['default_webhook_url'] ?? '') ?>"
                        placeholder="https://connect.pabbly.com/workflow/..."
                    >
                    <p class="form-hint">Destinazione delle richieste di preventivo (sovrascrivibile per singolo viaggio).</p>
                </div>
                <div class="form-group">
                    <label class="form-label" for="waitlist_webhook_url">Webhook waitlist</label>
                    <input
                        type="text"
                        id="waitlist_webhook_url"
                        name="waitlist_webhook_url"
                        class="form-control"
                        value="<?= htmlspecialchars($config['waitlist_webhook_url'] ?? '') ?>"
                        placeholder="https://connect.pabbly.com/workflow/..."
                    >
                </div>
                <div class="form-group">
                    <label class="form-label" for="b2b_webhook_url">Webhook partner/B2B</label>
                    <input
                        type="text"
                        id="b2b_webhook_url"
                        name="b2b_webhook_url"
                        class="form-control"
                        value="<?= htmlspecialchars($config['b2b_webhook_url'] ?? '') ?>"
                        placeholder="https://connect.pabbly.com/workflow/..."
                    >
                </div>
            </div>
        </div>

        <!-- Section 4: WhatsApp e Form -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card__header">
                <h2 class="card__title"><i class="fa-brands fa-whatsapp"></i> WhatsApp e Form</h2>
            </div>
            <div class="card__body">
                <div class="form-group">
                    <label class="form-label" for="whatsapp_number">Numero WhatsApp</label>
                    <input
                        type="text"
                        id="whatsapp_number"
                        name="whatsapp_number"
                        class="form-control"
                        value="<?= htmlspecialchars($config['whatsapp_number'] ?? '') ?>"
                        placeholder="+39 333 1234567"
                    >
                </div>
                <div class="form-group">
                    <label class="form-label" for="tally_catalog_url">Tally URL catalogo</label>
                    <input
                        type="text"
                        id="tally_catalog_url"
                        name="tally_catalog_url"
                        class="form-control"
                        value="<?= htmlspecialchars($config['tally_catalog_url'] ?? '') ?>"
                        placeholder="https://tally.so/r/..."
                    >
                </div>
                <div class="form-group">
                    <label class="form-label" for="tally_b2b_url">Tally URL B2B</label>
                    <input
                        type="text"
                        id="tally_b2b_url"
                        name="tally_b2b_url"
                        class="form-control"
                        value="<?= htmlspecialchars($config['tally_b2b_url'] ?? '') ?>"
                        placeholder="https://tally.so/r/..."
                    >
                </div>
            </div>
        </div>

        <!-- Section 5: AI Form Generator -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card__header">
                <h2 class="card__title"><i class="fa-solid fa-robot"></i> AI Form Generator</h2>
            </div>
            <div class="card__body">
                <div class="form-group">
                    <label class="form-label" for="anthropic_api_key">Anthropic API Key</label>
                    <input
                        type="password"
                        id="anthropic_api_key"
                        name="anthropic_api_key"
                        class="form-control"
                        value="<?= htmlspecialchars($config['anthropic_api_key'] ?? '') ?>"
                        autocomplete="off"
                    >
                    <p class="form-hint">Usato per la generazione automatica del Form Config con Claude AI.</p>
                </div>
            </div>
        </div>

        <!-- Save button -->
        <div style="padding-bottom:40px;">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk"></i> Salva Impostazioni
            </button>
        </div>

    </form>
</div>

</body>
</html>
