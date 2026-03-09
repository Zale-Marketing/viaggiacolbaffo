<?php
/**
 * admin/index.php — Trip management dashboard
 *
 * Session-guarded. Shows stats bar, draggable trip table,
 * publish toggle (AJAX), soft-delete with modal, trash section,
 * and logout. All AJAX actions return JSON.
 */
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: /admin/login.php');
    exit;
}

require_once __DIR__ . '/../includes/config.php';
require_once ROOT . '/includes/functions.php';

// ── Logout ────────────────────────────────────────────────────────────────────
if (($_GET['action'] ?? '') === 'logout' || ($_POST['action'] ?? '') === 'logout') {
    session_destroy();
    header('Location: /admin/login.php');
    exit;
}

// ── AJAX handlers (check before any output) ───────────────────────────────────
// The reorder action sends a JSON body (Content-Type: application/json);
// all other AJAX actions use form-encoded POST. Detect both.
$ajax_action = $_POST['action'] ?? '';
if ($ajax_action === '') {
    $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
    if (strpos($content_type, 'application/json') !== false) {
        $raw = file_get_contents('php://input');
        $decoded = json_decode($raw, true);
        $ajax_action = $decoded['action'] ?? '';
        // Store decoded data for the reorder handler below
        $_json_body = $decoded;
    }
}

if (in_array($ajax_action, ['toggle_published', 'soft_delete', 'restore', 'empty_trash', 'reorder'], true)) {
    header('Content-Type: application/json; charset=utf-8');

    $trips = load_trips();

    if ($ajax_action === 'toggle_published') {
        $slug = $_POST['slug'] ?? '';
        foreach ($trips as &$trip) {
            if ($trip['slug'] === $slug) {
                $trip['published'] = !(bool)($trip['published'] ?? false);
                save_trips($trips);
                echo json_encode(['success' => true, 'published' => $trip['published']]);
                exit;
            }
        }
        echo json_encode(['success' => false, 'error' => 'Trip not found']);
        exit;
    }

    if ($ajax_action === 'soft_delete') {
        $slug = $_POST['slug'] ?? '';
        foreach ($trips as &$trip) {
            if ($trip['slug'] === $slug) {
                $trip['deleted'] = true;
                save_trips($trips);
                echo json_encode(['success' => true]);
                exit;
            }
        }
        echo json_encode(['success' => false, 'error' => 'Trip not found']);
        exit;
    }

    if ($ajax_action === 'restore') {
        $slug = $_POST['slug'] ?? '';
        foreach ($trips as &$trip) {
            if ($trip['slug'] === $slug) {
                $trip['deleted'] = false;
                save_trips($trips);
                echo json_encode(['success' => true]);
                exit;
            }
        }
        echo json_encode(['success' => false, 'error' => 'Trip not found']);
        exit;
    }

    if ($ajax_action === 'empty_trash') {
        $trips = array_values(array_filter($trips, fn($t) => !($t['deleted'] ?? false)));
        save_trips($trips);
        echo json_encode(['success' => true]);
        exit;
    }

    if ($ajax_action === 'reorder') {
        // Data may already be decoded from the JSON-body detection above
        if (isset($_json_body)) {
            $data = $_json_body;
        } else {
            $body = file_get_contents('php://input');
            $data = json_decode($body, true);
        }
        $slugs = $data['slugs'] ?? [];
        $order = array_flip($slugs);   // slug → position index
        foreach ($trips as &$trip) {
            if (isset($order[$trip['slug']])) {
                $trip['position'] = $order[$trip['slug']];
            }
        }
        save_trips($trips);
        echo json_encode(['success' => true]);
        exit;
    }
}

// ── PHP data layer ────────────────────────────────────────────────────────────
$all_trips = load_trips();

$active_trips = array_values(array_filter($all_trips, fn($t) => !($t['deleted'] ?? false)));
$trash_trips  = array_values(array_filter($all_trips, fn($t) => ($t['deleted'] ?? false)));

// Sort active trips by position (treat missing as 999)
usort($active_trips, fn($a, $b) => ($a['position'] ?? 999) <=> ($b['position'] ?? 999));

$total     = count($active_trips);
$published = count(array_filter($active_trips, fn($t) => ($t['published'] ?? false) === true));
$draft     = $total - $published;

// ── Status label helper ───────────────────────────────────────────────────────
function status_label(string $status): string {
    return match($status) {
        'confermata'   => 'Confermata',
        'ultimi-posti' => 'Ultimi posti',
        'sold-out'     => 'Sold out',
        'programmata'  => 'Programmata',
        default        => ucfirst($status),
    };
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pannello — Viaggia col Baffo Admin</title>

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Admin stylesheet -->
  <link rel="stylesheet" href="/admin/admin.css">

</head>
<body>

<!-- ── Admin navigation ─────────────────────────────────────────────────────── -->
<nav class="admin-nav">
  <span class="admin-nav__logo">
    <span class="logo-icon"><i class="fa-solid fa-compass"></i></span>
    Viaggia col Baffo
  </span>

  <ul class="admin-nav__links">
    <li><a href="/admin/" class="active">Pannello</a></li>
    <li><a href="/admin/settings.php">Impostazioni</a></li>
    <li><a href="/admin/tags.php">Tag</a></li>
    <li><a href="/admin/destinations.php">Destinazioni</a></li>
    <li><a href="/" target="_blank" class="admin-nav__visit">Vai al sito <i class="fa-solid fa-arrow-up-right-from-square fa-xs"></i></a></li>
  </ul>

  <div class="admin-nav__actions">
    <form method="POST" action="/admin/">
      <input type="hidden" name="action" value="logout">
      <button type="submit" class="admin-nav__logout">
        <i class="fa-solid fa-right-from-bracket"></i>
        Esci
      </button>
    </form>
  </div>
</nav>

<!-- ── Main page ───────────────────────────────────────────────────────────── -->
<div class="admin-page">

  <!-- Page header -->
  <div class="admin-page__header">
    <div>
      <h1 class="admin-page__title">Pannello di controllo</h1>
      <p class="admin-page__subtitle">Gestisci i tuoi viaggi, pubblicazioni e contenuti.</p>
    </div>
    <a href="/admin/edit-trip.php?new=1" class="btn btn-cta">
      <i class="fa-solid fa-plus"></i>
      Crea Nuovo Viaggio
    </a>
  </div>

  <!-- Stats bar -->
  <div class="admin-stats">
    <div class="admin-stat-card">
      <div class="admin-stat-card__number"><?php echo $total; ?></div>
      <div class="admin-stat-card__label">Viaggi Totali</div>
    </div>
    <div class="admin-stat-card admin-stat-card--red">
      <div class="admin-stat-card__number"><?php echo $published; ?></div>
      <div class="admin-stat-card__label">Pubblicati</div>
    </div>
    <div class="admin-stat-card admin-stat-card--green">
      <div class="admin-stat-card__number"><?php echo $draft; ?></div>
      <div class="admin-stat-card__label">Bozze</div>
    </div>
  </div>

  <!-- Active trips table -->
  <?php if (count($active_trips) === 0): ?>
    <div class="admin-card">
      <div class="admin-empty">
        <div class="admin-empty__icon"><i class="fa-regular fa-map"></i></div>
        <div class="admin-empty__title">Nessun viaggio ancora</div>
        <div class="admin-empty__text">Crea il tuo primo viaggio per iniziare.</div>
        <a href="/admin/edit-trip.php?new=1" class="btn btn-cta">
          <i class="fa-solid fa-plus"></i>
          Crea Nuovo Viaggio
        </a>
      </div>
    </div>
  <?php else: ?>
    <div class="admin-table-wrapper" style="margin-bottom: 32px;">
      <table class="admin-table" id="trips-table">
        <thead>
          <tr>
            <th style="width:32px;"></th>
            <th>Titolo</th>
            <th>Continente</th>
            <th>Tag</th>
            <th>Stato</th>
            <th>Azioni</th>
          </tr>
        </thead>
        <tbody id="trips-tbody">
          <?php foreach ($active_trips as $trip):
            $slug         = htmlspecialchars($trip['slug'] ?? '');
            $title        = htmlspecialchars($trip['title'] ?? '(senza titolo)');
            $continent    = htmlspecialchars(ucfirst($trip['continent'] ?? ''));
            $is_published = ($trip['published'] ?? false) === true;
            $preview_tok  = htmlspecialchars($trip['preview_token'] ?? '');
            $status       = htmlspecialchars(status_label($trip['status'] ?? ''));
          ?>
          <tr class="trip-row" draggable="true" data-slug="<?php echo $slug; ?>">
            <!-- Drag handle -->
            <td>
              <span class="drag-handle" title="Trascina per riordinare">
                <i class="fa-solid fa-grip-vertical"></i>
              </span>
            </td>

            <!-- Title -->
            <td>
              <strong><?php echo $title; ?></strong>
            </td>

            <!-- Continent -->
            <td><?php echo $continent; ?></td>

            <!-- Tags -->
            <td>
              <div class="tag-chips">
                <?php foreach (($trip['tags'] ?? []) as $tag): ?>
                  <span class="tag-chip"><?php echo htmlspecialchars($tag); ?></span>
                <?php endforeach; ?>
              </div>
            </td>

            <!-- Status + publish toggle -->
            <td>
              <span
                class="pill <?php echo $is_published ? 'pill-published' : 'pill-draft'; ?> pill-toggle"
                data-slug="<?php echo $slug; ?>"
                data-published="<?php echo $is_published ? '1' : '0'; ?>"
                title="Clicca per cambiare stato"
              >
                <?php if ($is_published): ?>
                  <i class="fa-solid fa-circle-dot"></i> Pubblicato
                <?php else: ?>
                  <i class="fa-regular fa-circle"></i> Bozza
                <?php endif; ?>
              </span>
              <span class="pill pill-inactive" style="margin-left:4px; font-size:10px;"><?php echo $status; ?></span>
            </td>

            <!-- Actions -->
            <td style="white-space:nowrap;">
              <a href="/admin/edit-trip.php?slug=<?php echo $slug; ?>" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-pen"></i> Modifica
              </a>
              <?php if ($preview_tok !== ''): ?>
              <a href="/viaggio/<?php echo $slug; ?>?preview=<?php echo $preview_tok; ?>" target="_blank" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-eye"></i> Anteprima
              </a>
              <?php endif; ?>
              <button
                class="btn btn-danger btn-sm btn-delete"
                data-slug="<?php echo $slug; ?>"
                data-published="<?php echo $is_published ? '1' : '0'; ?>"
                data-title="<?php echo $title; ?>"
              >
                <i class="fa-solid fa-trash"></i> Elimina
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <!-- Cestino (trash) section — only if there are deleted trips -->
  <?php if (count($trash_trips) > 0): ?>
    <div class="admin-card" id="cestino-section">
      <div class="trash-header">
        <div class="trash-title">
          <i class="fa-solid fa-trash-can"></i>
          Cestino (<?php echo count($trash_trips); ?>)
        </div>
        <button class="btn btn-danger btn-sm" id="btn-empty-trash">
          <i class="fa-solid fa-dumpster-fire"></i>
          Svuota Cestino
        </button>
      </div>

      <div class="admin-table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Titolo</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody id="trash-tbody">
            <?php foreach ($trash_trips as $trip):
              $t_slug  = htmlspecialchars($trip['slug'] ?? '');
              $t_title = htmlspecialchars($trip['title'] ?? '(senza titolo)');
            ?>
            <tr class="trash-row" data-slug="<?php echo $t_slug; ?>">
              <td><strong><?php echo $t_title; ?></strong></td>
              <td style="white-space:nowrap;">
                <button class="btn btn-secondary btn-sm btn-restore" data-slug="<?php echo $t_slug; ?>" data-title="<?php echo $t_title; ?>">
                  <i class="fa-solid fa-rotate-left"></i> Ripristina
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>

</div><!-- /.admin-page -->

<!-- ── Delete modal ────────────────────────────────────────────────────────── -->
<div class="modal-overlay" id="delete-modal">
  <div class="modal">
    <div class="modal__header">
      <span class="modal__title" id="delete-modal-title">Elimina viaggio</span>
      <button class="modal__close" id="delete-modal-close"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal__body">
      <p id="delete-modal-body" style="font-size:14px; color: var(--text);">Sicuro di voler eliminare questo viaggio?</p>
    </div>
    <div class="modal__footer">
      <button class="btn btn-secondary" id="delete-modal-cancel">Annulla</button>
      <button class="btn btn-danger" id="delete-modal-confirm">
        <i class="fa-solid fa-trash"></i>
        Elimina
      </button>
    </div>
  </div>
</div>

<!-- ── Toast notification ───────────────────────────────────────────────────── -->
<div id="toast"></div>

<!-- ── JavaScript ──────────────────────────────────────────────────────────── -->
<script>
(function () {
  'use strict';

  // ── Toast helper ──────────────────────────────────────────────────────────
  var toastEl = document.getElementById('toast');
  var toastTimer = null;
  function showToast(msg, isError) {
    toastEl.innerHTML = (isError ? '<i class="fa-solid fa-circle-xmark"></i> ' : '<i class="fa-solid fa-circle-check"></i> ') + msg;
    toastEl.className = isError ? 'toast-error show' : 'toast-success show';
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () { toastEl.classList.remove('show'); }, 2500);
  }

  // ── Generic AJAX POST ─────────────────────────────────────────────────────
  function ajaxPost(params, onSuccess, onError) {
    var body = new URLSearchParams(params);
    fetch('/admin/', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString()
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (data.success) { onSuccess(data); }
      else { onError(data.error || 'Errore'); }
    })
    .catch(function (e) { onError(e.message); });
  }

  // ── Publish toggle pills ──────────────────────────────────────────────────
  document.querySelectorAll('.pill-toggle').forEach(function (pill) {
    pill.addEventListener('click', function () {
      var slug = pill.dataset.slug;
      ajaxPost(
        { action: 'toggle_published', slug: slug },
        function (data) {
          var pub = data.published;
          pill.dataset.published = pub ? '1' : '0';
          // Update pill appearance
          if (pub) {
            pill.className = 'pill pill-published pill-toggle';
            pill.innerHTML = '<i class="fa-solid fa-circle-dot"></i> Pubblicato';
          } else {
            pill.className = 'pill pill-draft pill-toggle';
            pill.innerHTML = '<i class="fa-regular fa-circle"></i> Bozza';
          }
          // Sync delete button data attribute
          var row = pill.closest('tr');
          if (row) {
            var delBtn = row.querySelector('.btn-delete');
            if (delBtn) delBtn.dataset.published = pub ? '1' : '0';
          }
          showToast(pub ? 'Pubblicato' : 'Impostato come bozza');
        },
        function (err) { showToast('Errore: ' + err, true); }
      );
    });
  });

  // ── Delete modal ──────────────────────────────────────────────────────────
  var deleteModal    = document.getElementById('delete-modal');
  var deleteModalTitle  = document.getElementById('delete-modal-title');
  var deleteModalBody   = document.getElementById('delete-modal-body');
  var deleteModalClose  = document.getElementById('delete-modal-close');
  var deleteModalCancel = document.getElementById('delete-modal-cancel');
  var deleteModalConfirm = document.getElementById('delete-modal-confirm');
  var pendingDeleteSlug = null;

  function openDeleteModal(slug, title, isPublished) {
    pendingDeleteSlug = slug;
    deleteModalTitle.textContent = 'Elimina "' + title + '"';
    if (isPublished) {
      deleteModalBody.innerHTML =
        '<strong style="color:var(--danger)">Questo viaggio è pubblicato.</strong><br>' +
        'Renderlo prima bozza, oppure eliminarlo direttamente nel Cestino?';
      deleteModalConfirm.textContent = 'Elimina comunque';
    } else {
      deleteModalBody.textContent = 'Il viaggio andrà nel Cestino. Puoi ripristinarlo in seguito.';
      deleteModalConfirm.innerHTML = '<i class="fa-solid fa-trash"></i> Elimina';
    }
    deleteModal.classList.add('open');
  }

  function closeDeleteModal() {
    deleteModal.classList.remove('open');
    pendingDeleteSlug = null;
  }

  deleteModalClose.addEventListener('click', closeDeleteModal);
  deleteModalCancel.addEventListener('click', closeDeleteModal);
  deleteModal.addEventListener('click', function (e) {
    if (e.target === deleteModal) closeDeleteModal();
  });

  deleteModalConfirm.addEventListener('click', function () {
    if (!pendingDeleteSlug) return;
    var slug = pendingDeleteSlug;
    closeDeleteModal();
    ajaxPost(
      { action: 'soft_delete', slug: slug },
      function () {
        // Remove row from table
        var row = document.querySelector('tr.trip-row[data-slug="' + slug + '"]');
        if (row) row.remove();
        showToast('Viaggio spostato nel Cestino');
        // Reload to refresh stats and trash section
        setTimeout(function () { location.reload(); }, 800);
      },
      function (err) { showToast('Errore: ' + err, true); }
    );
  });

  // Wire up delete buttons
  document.querySelectorAll('.btn-delete').forEach(function (btn) {
    btn.addEventListener('click', function () {
      openDeleteModal(btn.dataset.slug, btn.dataset.title, btn.dataset.published === '1');
    });
  });

  // ── Restore buttons (trash section) ──────────────────────────────────────
  document.querySelectorAll('.btn-restore').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var slug  = btn.dataset.slug;
      var title = btn.dataset.title;
      ajaxPost(
        { action: 'restore', slug: slug },
        function () {
          showToast('"' + title + '" ripristinato');
          setTimeout(function () { location.reload(); }, 800);
        },
        function (err) { showToast('Errore: ' + err, true); }
      );
    });
  });

  // ── Empty trash ───────────────────────────────────────────────────────────
  var emptyTrashBtn = document.getElementById('btn-empty-trash');
  if (emptyTrashBtn) {
    emptyTrashBtn.addEventListener('click', function () {
      if (!confirm('Svuotare il cestino? Questa azione è irreversibile.')) return;
      ajaxPost(
        { action: 'empty_trash' },
        function () {
          showToast('Cestino svuotato');
          setTimeout(function () { location.reload(); }, 800);
        },
        function (err) { showToast('Errore: ' + err, true); }
      );
    });
  }

  // ── Drag-and-drop reorder ─────────────────────────────────────────────────
  var tbody = document.getElementById('trips-tbody');
  if (!tbody) return;   // no active trips — nothing to reorder

  var dragSrc = null;
  var reorderTimer = null;

  function getSlugOrder() {
    var slugs = [];
    tbody.querySelectorAll('tr.trip-row').forEach(function (row) {
      slugs.push(row.dataset.slug);
    });
    return slugs;
  }

  function renumberRows() {
    // Visual-only renumber — positions saved via AJAX
  }

  function saveReorder() {
    clearTimeout(reorderTimer);
    reorderTimer = setTimeout(function () {
      var slugs = getSlugOrder();
      fetch('/admin/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'reorder', slugs: slugs })
      })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data.success) showToast('Ordine salvato');
        else showToast('Errore nel salvataggio', true);
      })
      .catch(function (e) { showToast('Errore: ' + e.message, true); });
    }, 400);
  }

  tbody.querySelectorAll('tr.trip-row').forEach(function (row) {
    row.addEventListener('dragstart', function (e) {
      dragSrc = row;
      row.classList.add('dragging');
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/plain', row.dataset.slug);
    });

    row.addEventListener('dragend', function () {
      row.classList.remove('dragging');
      tbody.querySelectorAll('tr.trip-row').forEach(function (r) {
        r.classList.remove('drag-over');
      });
    });

    row.addEventListener('dragover', function (e) {
      e.preventDefault();
      e.dataTransfer.dropEffect = 'move';
      // Highlight target
      tbody.querySelectorAll('tr.trip-row').forEach(function (r) {
        r.classList.remove('drag-over');
      });
      if (row !== dragSrc) row.classList.add('drag-over');
    });

    row.addEventListener('drop', function (e) {
      e.preventDefault();
      if (dragSrc && dragSrc !== row) {
        // Insert dragSrc before or after row depending on mouse position
        var rect = row.getBoundingClientRect();
        var midY = rect.top + rect.height / 2;
        if (e.clientY < midY) {
          tbody.insertBefore(dragSrc, row);
        } else {
          tbody.insertBefore(dragSrc, row.nextSibling);
        }
        row.classList.remove('drag-over');
        renumberRows();
        saveReorder();
      }
    });
  });

  // Handle AJAX reorder — read JSON body on server side
  // (The fetch for reorder sends JSON body; PHP reads php://input)

})();
</script>

</body>
</html>
