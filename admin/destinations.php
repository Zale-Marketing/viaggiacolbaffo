<?php
/**
 * admin/destinations.php — Destination content editor
 * List mode: 6 tiles with thumbnail + Modifica link.
 * Edit mode (?slug=): full edit form for name, hero, intro, practical info, see_also, curiosità.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$destinations = load_destinations();
$slug         = trim($_GET['slug'] ?? '');
$valid_slugs  = ['america', 'asia', 'europa', 'africa', 'oceania', 'medio-oriente'];
$mode         = ($slug !== '' && in_array($slug, $valid_slugs)) ? 'edit' : 'list';
$saved        = $mode === 'edit' && isset($_GET['saved']) && $_GET['saved'] === '1';

// ── POST handler (edit mode save) ─────────────────────────────────────────────
if ($mode === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $dest = $destinations[$slug] ?? [];

    // hero image
    $dest['hero_image'] = trim($_POST['hero_image'] ?? '');

    // intro paragraphs (3 fixed)
    $dest['intro_paragraphs'] = [
        trim($_POST['intro_1'] ?? ''),
        trim($_POST['intro_2'] ?? ''),
        trim($_POST['intro_3'] ?? ''),
    ];

    // practical_info: 5 rows
    $pinfo_icons   = $_POST['pinfo_icon']  ?? [];
    $pinfo_labels  = $_POST['pinfo_label'] ?? [];
    $pinfo_values  = $_POST['pinfo_value'] ?? [];
    $dest['practical_info'] = [];
    for ($i = 0; $i < 5; $i++) {
        $dest['practical_info'][] = [
            'icon'  => trim($pinfo_icons[$i]  ?? ''),
            'label' => trim($pinfo_labels[$i] ?? ''),
            'value' => trim($pinfo_values[$i] ?? ''),
        ];
    }

    // see_also: 4 rows
    $see_names  = $_POST['see_name']  ?? [];
    $see_images = $_POST['see_image'] ?? [];
    $see_descs  = $_POST['see_desc']  ?? [];
    $dest['see_also'] = [];
    for ($i = 0; $i < 4; $i++) {
        $dest['see_also'][] = [
            'name'        => trim($see_names[$i]  ?? ''),
            'image'       => trim($see_images[$i] ?? ''),
            'description' => trim($see_descs[$i]  ?? ''),
        ];
    }

    // curiosità: 3 rows
    $cur_icons   = $_POST['cur_icon']  ?? [];
    $cur_titles  = $_POST['cur_title'] ?? [];
    $cur_texts   = $_POST['cur_text']  ?? [];
    $dest['curiosita'] = [];
    for ($i = 0; $i < 3; $i++) {
        $dest['curiosita'][] = [
            'icon'  => trim($cur_icons[$i]  ?? ''),
            'title' => trim($cur_titles[$i] ?? ''),
            'text'  => trim($cur_texts[$i]  ?? ''),
        ];
    }

    // Preserve 'name' field (6 fixed slugs — name does not change via this form)
    // $dest['name'] is already present from loaded data.

    $destinations[$slug] = $dest;
    save_destinations($destinations);

    header("Location: destinations.php?slug={$slug}&saved=1");
    exit;
}

// ── Helpers ───────────────────────────────────────────────────────────────────
$dest_data = ($mode === 'edit') ? ($destinations[$slug] ?? []) : [];

$slug_names = [
    'america'      => 'America',
    'asia'         => 'Asia',
    'europa'       => 'Europa',
    'africa'       => 'Africa',
    'oceania'      => 'Oceania',
    'medio-oriente'=> 'Medio Oriente',
];

function hval(array $arr, string $key, int $index = -1): string {
    if ($index >= 0) {
        $val = $arr[$key][$index] ?? '';
    } else {
        $val = $arr[$key] ?? '';
    }
    return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $mode === 'edit' ? 'Modifica ' . htmlspecialchars($dest_data['name'] ?? $slug) . ' — ' : '' ?>Destinazioni — Admin Viaggia Col Baffo</title>
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
        <li><a href="destinations.php" class="admin-nav__link admin-nav__link--active">Destinazioni</a></li>
        <li><a href="settings.php" class="admin-nav__link">Impostazioni</a></li>
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

<div class="admin-wrapper">

<?php if ($mode === 'list'): ?>
<!-- ════════════════════════════════════════════════════════════
     LIST MODE — 6 destination tiles
     ════════════════════════════════════════════════════════════ -->
    <div class="admin-page-header">
        <h1 class="admin-page-title">Destinazioni</h1>
        <p class="admin-page-subtitle">Modifica il contenuto editoriale delle 6 destinazioni.</p>
    </div>

    <div class="destinations-grid">
        <?php foreach ($valid_slugs as $s):
            $d    = $destinations[$s] ?? [];
            $name = $d['name'] ?? $slug_names[$s] ?? $s;
            $hero = $d['hero_image'] ?? '';
        ?>
        <div class="dest-admin-card card">
            <?php if ($hero): ?>
            <img
                src="<?= htmlspecialchars($hero) ?>"
                alt="<?= htmlspecialchars($name) ?>"
                class="dest-admin-card__thumb"
                loading="lazy"
            >
            <?php else: ?>
            <div class="dest-admin-card__thumb dest-admin-card__thumb--placeholder">
                <i class="fa-solid fa-image" style="font-size:2rem;color:var(--text-muted);"></i>
            </div>
            <?php endif; ?>
            <div class="dest-admin-card__body">
                <h3 class="dest-admin-card__name"><?= htmlspecialchars($name) ?></h3>
                <a href="destinations.php?slug=<?= urlencode($s) ?>" class="btn btn-primary btn-sm" style="margin-top:8px;">
                    <i class="fa-solid fa-pen-to-square"></i> Modifica
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

<?php else: ?>
<!-- ════════════════════════════════════════════════════════════
     EDIT MODE — per-slug form
     ════════════════════════════════════════════════════════════ -->
    <div style="margin-bottom:16px;">
        <a href="destinations.php" class="admin-back-link">
            <i class="fa-solid fa-arrow-left"></i> Tutte le Destinazioni
        </a>
    </div>

    <div class="admin-page-header">
        <h1 class="admin-page-title">Modifica Destinazione — <?= htmlspecialchars($dest_data['name'] ?? $slug_names[$slug] ?? $slug) ?></h1>
    </div>

    <?php if ($saved): ?>
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i> Salvato con successo.
    </div>
    <?php endif; ?>

    <form method="POST" action="destinations.php?slug=<?= urlencode($slug) ?>">

        <!-- Hero Image -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card__header">
                <h2 class="card__title"><i class="fa-solid fa-image"></i> Immagine Hero</h2>
            </div>
            <div class="card__body">
                <div class="form-group">
                    <label class="form-label" for="hero_image">URL immagine hero</label>
                    <input
                        type="text"
                        id="hero_image"
                        name="hero_image"
                        class="form-control"
                        value="<?= hval($dest_data, 'hero_image') ?>"
                        placeholder="https://images.unsplash.com/..."
                    >
                </div>
                <div id="hero-preview-wrap" style="margin-top:12px;">
                    <?php if (!empty($dest_data['hero_image'])): ?>
                    <img
                        id="hero-preview"
                        src="<?= hval($dest_data, 'hero_image') ?>"
                        alt="Preview hero"
                        style="max-height:180px; border-radius:var(--radius); border:1px solid var(--border);"
                    >
                    <?php else: ?>
                    <img id="hero-preview" src="" alt="Preview hero" style="display:none; max-height:180px; border-radius:var(--radius); border:1px solid var(--border);">
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Intro paragraphs -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card__header">
                <h2 class="card__title"><i class="fa-solid fa-align-left"></i> Testi Introduttivi</h2>
            </div>
            <div class="card__body">
                <?php for ($i = 1; $i <= 3; $i++):
                    $val = htmlspecialchars($dest_data['intro_paragraphs'][$i-1] ?? '', ENT_QUOTES, 'UTF-8');
                ?>
                <div class="form-group">
                    <label class="form-label" for="intro_<?= $i ?>">Paragrafo <?= $i ?></label>
                    <textarea
                        id="intro_<?= $i ?>"
                        name="intro_<?= $i ?>"
                        class="form-control"
                        rows="4"
                    ><?= $val ?></textarea>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Practical Info -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card__header">
                <h2 class="card__title"><i class="fa-solid fa-circle-info"></i> Informazioni Pratiche</h2>
            </div>
            <div class="card__body">
                <p class="form-hint" style="margin-bottom:16px;">5 voci — icona Font Awesome (es. <code>fa-solid fa-coins</code>), etichetta, valore.</p>
                <?php for ($i = 0; $i < 5; $i++):
                    $row = $dest_data['practical_info'][$i] ?? ['icon'=>'','label'=>'','value'=>''];
                ?>
                <div style="display:grid; grid-template-columns:220px 1fr 1fr; gap:12px; margin-bottom:12px;">
                    <div class="form-group" style="margin-bottom:0;">
                        <?php if ($i === 0): ?>
                        <label class="form-label">Icona FA</label>
                        <?php endif; ?>
                        <input type="text" name="pinfo_icon[]" class="form-control" value="<?= htmlspecialchars($row['icon'], ENT_QUOTES) ?>" placeholder="fa-solid fa-coins">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <?php if ($i === 0): ?>
                        <label class="form-label">Etichetta</label>
                        <?php endif; ?>
                        <input type="text" name="pinfo_label[]" class="form-control" value="<?= htmlspecialchars($row['label'], ENT_QUOTES) ?>" placeholder="Valuta">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <?php if ($i === 0): ?>
                        <label class="form-label">Valore</label>
                        <?php endif; ?>
                        <input type="text" name="pinfo_value[]" class="form-control" value="<?= htmlspecialchars($row['value'], ENT_QUOTES) ?>" placeholder="Euro (EUR)">
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Cosa Vedere (see_also) -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card__header">
                <h2 class="card__title"><i class="fa-solid fa-binoculars"></i> Cosa Vedere</h2>
            </div>
            <div class="card__body">
                <p class="form-hint" style="margin-bottom:16px;">4 luoghi da visitare — nome, URL immagine, descrizione breve.</p>
                <?php for ($i = 0; $i < 4; $i++):
                    $row = $dest_data['see_also'][$i] ?? ['name'=>'','image'=>'','description'=>''];
                ?>
                <div class="card" style="margin-bottom:16px; background:var(--bg);">
                    <div class="card__body">
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">Nome luogo</label>
                                <input type="text" name="see_name[]" class="form-control" value="<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>" placeholder="New York">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">URL immagine</label>
                                <input type="text" name="see_image[]" class="form-control" value="<?= htmlspecialchars($row['image'], ENT_QUOTES) ?>" placeholder="https://images.unsplash.com/...">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top:12px; margin-bottom:0;">
                            <label class="form-label">Descrizione</label>
                            <textarea name="see_desc[]" class="form-control" rows="2" placeholder="Breve descrizione del luogo..."><?= htmlspecialchars($row['description'], ENT_QUOTES) ?></textarea>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Curiosità -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card__header">
                <h2 class="card__title"><i class="fa-solid fa-lightbulb"></i> Curiosità</h2>
            </div>
            <div class="card__body">
                <p class="form-hint" style="margin-bottom:16px;">3 curiosità — icona Font Awesome, titolo, testo.</p>
                <?php for ($i = 0; $i < 3; $i++):
                    $row = $dest_data['curiosita'][$i] ?? ['icon'=>'','title'=>'','text'=>''];
                ?>
                <div class="card" style="margin-bottom:16px; background:var(--bg);">
                    <div class="card__body">
                        <div style="display:grid; grid-template-columns:220px 1fr; gap:12px; margin-bottom:12px;">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">Icona FA</label>
                                <input type="text" name="cur_icon[]" class="form-control" value="<?= htmlspecialchars($row['icon'], ENT_QUOTES) ?>" placeholder="fa-solid fa-mountain">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">Titolo</label>
                                <input type="text" name="cur_title[]" class="form-control" value="<?= htmlspecialchars($row['title'], ENT_QUOTES) ?>" placeholder="Titolo della curiosità">
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Testo</label>
                            <textarea name="cur_text[]" class="form-control" rows="3"><?= htmlspecialchars($row['text'], ENT_QUOTES) ?></textarea>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Save button -->
        <div style="padding-bottom:40px;">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk"></i> Salva Destinazione
            </button>
        </div>

    </form>

<?php endif; ?>

</div><!-- /.admin-wrapper -->

<script>
(function () {
    'use strict';

    <?php if ($mode === 'edit'): ?>
    // ── Live hero image preview ───────────────────────────────────────────────
    var heroInput   = document.getElementById('hero_image');
    var heroPreview = document.getElementById('hero-preview');

    if (heroInput && heroPreview) {
        heroInput.addEventListener('input', function () {
            var url = this.value.trim();
            if (url) {
                heroPreview.src = url;
                heroPreview.style.display = 'block';
            } else {
                heroPreview.style.display = 'none';
                heroPreview.src = '';
            }
        });
    }
    <?php endif; ?>
})();
</script>

<style>
/* ── Destinations admin supplemental styles ── */
.destinations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 20px;
}

.dest-admin-card {
    overflow: hidden;
}

.dest-admin-card__thumb {
    width: 100%;
    height: 140px;
    object-fit: cover;
    display: block;
}

.dest-admin-card__thumb--placeholder {
    width: 100%;
    height: 140px;
    background: var(--bg);
    display: flex;
    align-items: center;
    justify-content: center;
    border-bottom: 1px solid var(--border);
}

.dest-admin-card__body {
    padding: 16px;
}

.dest-admin-card__name {
    font-size: 16px;
    font-weight: 600;
    color: var(--text);
}

.admin-back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--text-muted);
    font-size: 13px;
    font-weight: 500;
    transition: color 0.15s;
}

.admin-back-link:hover {
    color: var(--text);
}
</style>

</body>
</html>
