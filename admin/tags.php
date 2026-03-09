<?php
/**
 * admin/tags.php — Tag management with category grouping and cascade delete
 * AJAX handlers respond to POST with JSON; page renders the full tag UI.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

session_start();
if (empty($_SESSION['admin'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }
    header('Location: login.php');
    exit;
}

// ── AJAX POST handlers ────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $action = $_POST['action'] ?? '';

    if ($action === 'add_tag') {
        $name     = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $slug     = trim($_POST['slug'] ?? '');

        if ($name === '') {
            echo json_encode(['success' => false, 'error' => 'Nome obbligatorio']);
            exit;
        }

        // Auto-generate slug from name if empty
        if ($slug === '') {
            $slug = strtolower($name);
            $slug = trim($slug);
            $slug = preg_replace('/\s+/', '-', $slug);
            // Transliterate common Italian accents
            $slug = strtr($slug, ['à'=>'a','è'=>'e','é'=>'e','ì'=>'i','ò'=>'o','ù'=>'u','ü'=>'u','ñ'=>'n']);
            $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
            $slug = trim($slug, '-');
        }

        $tags = load_tags();

        // Check slug uniqueness
        foreach ($tags as $t) {
            if ($t['slug'] === $slug) {
                echo json_encode(['success' => false, 'error' => 'Slug già esistente: ' . $slug]);
                exit;
            }
        }

        $new_tag = ['slug' => $slug, 'label' => $name, 'category' => $category];
        $tags[] = $new_tag;
        save_tags($tags);

        echo json_encode(['success' => true, 'tag' => $new_tag]);
        exit;
    }

    if ($action === 'delete_tag') {
        $slug = trim($_POST['slug'] ?? '');
        if ($slug === '') {
            echo json_encode(['success' => false, 'error' => 'Slug obbligatorio']);
            exit;
        }

        // Remove tag from tags.json
        $tags = load_tags();
        $tags = array_values(array_filter($tags, fn($t) => $t['slug'] !== $slug));
        save_tags($tags);

        // Cascade: remove this tag slug from every trip
        $trips = load_trips();
        foreach ($trips as &$trip) {
            if (isset($trip['tags']) && is_array($trip['tags'])) {
                $trip['tags'] = array_values(array_filter($trip['tags'], fn($s) => $s !== $slug));
            }
        }
        unset($trip);
        save_trips($trips);

        echo json_encode(['success' => true]);
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Azione sconosciuta']);
    exit;
}

// ── Load tags for display ─────────────────────────────────────────────────────
$tags = load_tags();

// Group by category
$category_labels = [
    'continente'   => 'Continente',
    'tipo viaggio' => 'Tipo Viaggio',
    'per chi'      => 'Per Chi',
    'mese'         => 'Mese',
];
$groups = [];
foreach ($tags as $tag) {
    $cat = $tag['category'] ?? 'altro';
    $groups[$cat][] = $tag;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tag — Admin Viaggia Col Baffo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<!-- ── Admin navigation ── -->
<nav class="admin-nav">
    <span class="admin-nav__logo">Viaggia Col Baffo</span>
    <ul class="admin-nav__links">
        <li><a href="index.php" class="admin-nav__link">Pannello</a></li>
        <li><a href="tags.php" class="admin-nav__link admin-nav__link--active">Tag</a></li>
        <li><a href="destinations.php" class="admin-nav__link">Destinazioni</a></li>
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

<!-- ── Page content ── -->
<div class="admin-wrapper">
    <div class="admin-page-header">
        <h1 class="admin-page-title">Gestione Tag</h1>
        <p class="admin-page-subtitle">Aggiungi, organizza ed elimina i tag. L'eliminazione rimuove il tag da tutti i viaggi.</p>
    </div>

    <!-- Add tag card -->
    <div class="card" style="margin-bottom:32px;" id="add-tag-card">
        <div class="card__header">
            <h2 class="card__title"><i class="fa-solid fa-tag"></i> Aggiungi Tag</h2>
        </div>
        <div class="card__body">
            <div id="add-tag-error" class="alert alert-danger" style="display:none;"></div>
            <div style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
                <div class="form-group" style="flex:1; min-width:160px; margin-bottom:0;">
                    <label class="form-label" for="new-tag-name">Nome tag</label>
                    <input type="text" id="new-tag-name" class="form-control" placeholder="es. Costa Rica" maxlength="60">
                </div>
                <div class="form-group" style="flex:1; min-width:160px; margin-bottom:0;">
                    <label class="form-label" for="new-tag-category">Categoria</label>
                    <select id="new-tag-category" class="form-control">
                        <option value="continente">Continente</option>
                        <option value="tipo viaggio">Tipo Viaggio</option>
                        <option value="per chi">Per Chi</option>
                        <option value="mese">Mese</option>
                        <option value="altro">Altro</option>
                    </select>
                </div>
                <div style="padding-bottom:2px;">
                    <button type="button" class="btn btn-primary" id="btn-add-tag">
                        <i class="fa-solid fa-plus"></i> Aggiungi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tags grouped by category -->
    <div id="tags-container">
        <?php
        $display_order = ['continente', 'tipo viaggio', 'per chi', 'mese', 'altro'];
        foreach ($display_order as $cat_key):
            $cat_tags = $groups[$cat_key] ?? [];
            $cat_label = $category_labels[$cat_key] ?? ucfirst($cat_key);
        ?>
        <div class="card tag-group" data-category="<?= htmlspecialchars($cat_key) ?>" style="margin-bottom:24px;">
            <div class="card__header">
                <h2 class="card__title"><?= htmlspecialchars($cat_label) ?></h2>
                <span class="badge"><?= count($cat_tags) ?></span>
            </div>
            <div class="card__body">
                <div class="tag-chips" id="chips-<?= htmlspecialchars(str_replace(' ', '-', $cat_key)) ?>">
                    <?php if (empty($cat_tags)): ?>
                    <span class="tag-empty">Nessun tag in questa categoria</span>
                    <?php else: ?>
                    <?php foreach ($cat_tags as $tag): ?>
                    <span class="tag-chip" data-slug="<?= htmlspecialchars($tag['slug']) ?>">
                        <?= htmlspecialchars($tag['label']) ?>
                        <button
                            type="button"
                            class="tag-chip__delete"
                            data-slug="<?= htmlspecialchars($tag['slug']) ?>"
                            data-label="<?= htmlspecialchars($tag['label']) ?>"
                            title="Elimina tag"
                            aria-label="Elimina <?= htmlspecialchars($tag['label']) ?>"
                        >&times;</button>
                    </span>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php
        // Show any categories not in the standard list
        foreach ($groups as $cat_key => $cat_tags):
            if (in_array($cat_key, $display_order)) continue;
            $cat_label = ucfirst($cat_key);
        ?>
        <div class="card tag-group" data-category="<?= htmlspecialchars($cat_key) ?>" style="margin-bottom:24px;">
            <div class="card__header">
                <h2 class="card__title"><?= htmlspecialchars($cat_label) ?></h2>
                <span class="badge"><?= count($cat_tags) ?></span>
            </div>
            <div class="card__body">
                <div class="tag-chips" id="chips-<?= htmlspecialchars(str_replace(' ', '-', $cat_key)) ?>">
                    <?php foreach ($cat_tags as $tag): ?>
                    <span class="tag-chip" data-slug="<?= htmlspecialchars($tag['slug']) ?>">
                        <?= htmlspecialchars($tag['label']) ?>
                        <button
                            type="button"
                            class="tag-chip__delete"
                            data-slug="<?= htmlspecialchars($tag['slug']) ?>"
                            data-label="<?= htmlspecialchars($tag['label']) ?>"
                            title="Elimina tag"
                            aria-label="Elimina <?= htmlspecialchars($tag['label']) ?>"
                        >&times;</button>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div><!-- /.admin-wrapper -->

<script>
(function () {
    'use strict';

    // ── Add Tag ───────────────────────────────────────────────────────────────
    document.getElementById('btn-add-tag').addEventListener('click', function () {
        const name     = document.getElementById('new-tag-name').value.trim();
        const category = document.getElementById('new-tag-category').value;
        const errEl    = document.getElementById('add-tag-error');

        if (!name) {
            errEl.textContent = 'Inserisci un nome per il tag.';
            errEl.style.display = 'block';
            return;
        }
        errEl.style.display = 'none';

        const fd = new FormData();
        fd.append('action',   'add_tag');
        fd.append('name',     name);
        fd.append('category', category);

        fetch('tags.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    errEl.textContent = data.error || 'Errore durante l\'aggiunta.';
                    errEl.style.display = 'block';
                    return;
                }
                // Append chip to the correct group
                const cat = (data.tag.category || 'altro').replace(/ /g, '-');
                const container = document.getElementById('chips-' + cat)
                               || document.getElementById('chips-altro');
                if (container) {
                    // Remove "empty" placeholder if present
                    const emptyEl = container.querySelector('.tag-empty');
                    if (emptyEl) emptyEl.remove();

                    // Update badge count
                    const group = container.closest('.tag-group');
                    if (group) {
                        const badge = group.querySelector('.badge');
                        if (badge) badge.textContent = parseInt(badge.textContent || '0') + 1;
                    }

                    const chip = buildChip(data.tag.slug, data.tag.label);
                    container.appendChild(chip);
                }
                // Clear input
                document.getElementById('new-tag-name').value = '';
            })
            .catch(() => {
                errEl.textContent = 'Errore di rete. Riprova.';
                errEl.style.display = 'block';
            });
    });

    // ── Allow Enter key on name input ────────────────────────────────────────
    document.getElementById('new-tag-name').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btn-add-tag').click();
        }
    });

    // ── Delete Tag (event delegation) ────────────────────────────────────────
    document.getElementById('tags-container').addEventListener('click', function (e) {
        const btn = e.target.closest('.tag-chip__delete');
        if (!btn) return;

        const slug  = btn.dataset.slug;
        const label = btn.dataset.label;

        if (!confirm('Elimina il tag "' + label + '" da tutti i viaggi?')) return;

        const fd = new FormData();
        fd.append('action', 'delete_tag');
        fd.append('slug',   slug);

        fetch('tags.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    alert('Errore: ' + (data.error || 'impossibile eliminare il tag.'));
                    return;
                }
                // Remove chip from DOM
                const chip = document.querySelector('.tag-chip[data-slug="' + slug + '"]');
                if (chip) {
                    const container = chip.parentElement;
                    chip.remove();

                    // Update badge count
                    const group = container ? container.closest('.tag-group') : null;
                    if (group) {
                        const badge = group.querySelector('.badge');
                        if (badge) badge.textContent = Math.max(0, parseInt(badge.textContent || '1') - 1);
                    }

                    // Show empty placeholder if no chips remain
                    if (container && container.querySelectorAll('.tag-chip').length === 0) {
                        const empty = document.createElement('span');
                        empty.className = 'tag-empty';
                        empty.textContent = 'Nessun tag in questa categoria';
                        container.appendChild(empty);
                    }
                }
            })
            .catch(() => alert('Errore di rete durante l\'eliminazione.'));
    });

    // ── Build chip element ────────────────────────────────────────────────────
    function buildChip(slug, label) {
        const span = document.createElement('span');
        span.className = 'tag-chip';
        span.dataset.slug = slug;

        const text = document.createTextNode(label + ' ');
        span.appendChild(text);

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'tag-chip__delete';
        btn.dataset.slug  = slug;
        btn.dataset.label = label;
        btn.title = 'Elimina tag';
        btn.setAttribute('aria-label', 'Elimina ' + label);
        btn.textContent = '×';
        span.appendChild(btn);

        return span;
    }
})();
</script>

</body>
</html>
