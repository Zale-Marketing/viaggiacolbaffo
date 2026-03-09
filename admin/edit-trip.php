<?php
/**
 * admin/edit-trip.php — Full trip edit form
 * Tabs: Info Base | Media | Contenuto | Itinerario | Form Config (placeholder)
 */

session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// ── Auth guard ────────────────────────────────────────────────────────────────
if (empty($_SESSION['admin'])) {
    header('Location: /admin/login.php');
    exit;
}

// ── Mode detection ────────────────────────────────────────────────────────────
$is_new  = isset($_GET['new']);
$slug_param = $_GET['slug'] ?? null;

if ($is_new) {
    $trip = [
        'slug'              => '',
        'title'             => '',
        'continent'         => 'europa',
        'status'            => 'programmata',
        'published'         => false,
        'deleted'           => false,
        'position'          => 999,
        'preview_token'     => bin2hex(random_bytes(16)),
        'commission_rate'   => 10,
        'date_start'        => '',
        'date_end'          => '',
        'duration'          => '',
        'price_from'        => 0,
        'hero_image'        => '',
        'gallery'           => [],
        'short_description' => '',
        'full_description'  => '',
        'itinerary'         => [],
        'included'          => [],
        'excluded'          => [],
        'tags'              => [],
        'accompagnatore'    => null,
        'volo'              => null,
        'hotel'             => [],
        'webhook_url'       => '',
        'form_config'       => [],
    ];
} elseif ($slug_param) {
    $trip = get_trip_by_slug($slug_param);
    if ($trip === null) {
        header('Location: /admin/');
        exit;
    }
} else {
    header('Location: /admin/');
    exit;
}

// ── Slug locked after first publish ──────────────────────────────────────────
$slug_locked = !$is_new && ($trip['published'] ?? false);

// ── PHP slug generator (mirrors JS generateSlug) ─────────────────────────────
function php_generate_slug(string $title): string {
    $slug = strtolower($title);
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug);
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = trim($slug);
    $slug = preg_replace('/\s+/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return $slug;
}

// ── POST handler ──────────────────────────────────────────────────────────────
$errors = [];
$saved  = isset($_GET['saved']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'draft';

    // AJAX: action=save_form_config
    if ($action === 'save_form_config') {
        $slug = $_POST['slug'] ?? '';
        $room_types_raw = $_POST['room_types'] ?? '[]';
        $room_types = json_decode($room_types_raw, true);
        if (!is_array($room_types)) $room_types = [];
        $brackets_raw = $_POST['child_discount_brackets'] ?? '[]';
        $brackets = json_decode($brackets_raw, true);
        if (!is_array($brackets)) $brackets = [];
        $form_config = [
            'webhook_url'                => trim($_POST['webhook_url'] ?? ''),
            'prezzo_base_persona'        => (int)($_POST['prezzo_base_persona'] ?? 0),
            'room_types'                 => $room_types,
            'supplemento_singola'        => (int)($_POST['supplemento_singola'] ?? 0),
            'sconto_terzo_letto'         => (int)($_POST['sconto_terzo_letto'] ?? 0),
            'sconto_quarto_letto'        => (int)($_POST['sconto_quarto_letto'] ?? 0),
            'sconto_quinto_letto'        => (int)($_POST['sconto_quinto_letto'] ?? 0),
            'child_discounts_enabled'    => ($_POST['child_discounts_enabled'] ?? '0') === '1',
            'child_discount_brackets'    => $brackets,
            'insurance_enabled'          => ($_POST['insurance_enabled'] ?? '0') === '1',
            'percentuale_assicurazione'  => (float)($_POST['percentuale_assicurazione'] ?? 5),
            'competitor_enabled'         => ($_POST['competitor_enabled'] ?? '0') === '1',
            'prezzo_concorrenza_persona' => (int)($_POST['prezzo_concorrenza_persona'] ?? 0),
            'prezzo_concorrenza_letti_extra' => (int)($_POST['prezzo_concorrenza_letti_extra'] ?? 0),
            'agency_code_hash'           => trim($_POST['agency_code_hash'] ?? ''),
        ];
        $trips = load_trips();
        foreach ($trips as &$t) {
            if ($t['slug'] === $slug) { $t['form_config'] = $form_config; break; }
        }
        unset($t);
        save_trips($trips);
        echo json_encode(['success' => true]);
        exit;
    }

    // AJAX: action=regenerate_token
    if ($action === 'regenerate_token') {
        $slug = $_POST['slug'] ?? '';
        $new_token = bin2hex(random_bytes(16));
        $trips = load_trips();
        foreach ($trips as &$t) {
            if ($t['slug'] === $slug) { $t['preview_token'] = $new_token; break; }
        }
        unset($t);
        save_trips($trips);
        echo json_encode(['success' => true, 'token' => $new_token]);
        exit;
    }

    // Slug
    if ($slug_locked) {
        $new_slug = $trip['slug'];
    } else {
        $submitted_slug = trim($_POST['slug'] ?? '');
        $new_slug = $submitted_slug !== '' ? php_generate_slug($submitted_slug) : php_generate_slug($_POST['title'] ?? '');
    }
    if ($new_slug === '') {
        $errors[] = 'Lo slug non può essere vuoto.';
    }

    // Preview token
    $preview_token = $trip['preview_token'] ?? '';
    if ($preview_token === '') {
        $preview_token = bin2hex(random_bytes(16));
    }

    // Published flag
    $published = ($action === 'publish');

    // Gallery: one URL per line → array
    $gallery_raw = trim($_POST['gallery'] ?? '');
    $gallery = $gallery_raw !== ''
        ? array_values(array_filter(array_map('trim', explode("\n", $gallery_raw))))
        : [];

    // Itinerary
    $itin_titles    = $_POST['itinerary_title']    ?? [];
    $itin_descs     = $_POST['itinerary_desc']     ?? [];
    $itin_locations = $_POST['itinerary_location'] ?? [];
    $itin_dates     = $_POST['itinerary_date']     ?? [];
    $itin_images    = $_POST['itinerary_image']    ?? [];
    $itinerary      = [];
    foreach ($itin_titles as $i => $title_val) {
        $title_val = trim($title_val);
        $desc_val  = trim($itin_descs[$i] ?? '');
        if ($title_val !== '' || $desc_val !== '') {
            $itinerary[] = [
                'day'         => $i + 1,
                'title'       => $title_val,
                'location'    => trim($itin_locations[$i] ?? ''),
                'date'        => trim($itin_dates[$i] ?? ''),
                'description' => $desc_val,
                'image_url'   => trim($itin_images[$i] ?? ''),
            ];
        }
    }

    // Included / Excluded
    $included_raw = trim($_POST['included'] ?? '');
    $included = $included_raw !== ''
        ? array_values(array_filter(array_map('trim', explode("\n", $included_raw))))
        : [];
    $excluded_raw = trim($_POST['excluded'] ?? '');
    $excluded = $excluded_raw !== ''
        ? array_values(array_filter(array_map('trim', explode("\n", $excluded_raw))))
        : [];

    // Tags
    $tags_json = $_POST['tags_json'] ?? '[]';
    $tags = json_decode($tags_json, true);
    if (!is_array($tags)) $tags = [];

    // ── Accompagnatore ────────────────────────────────────────────
    $acc_nome = trim($_POST['accompagnatore_nome'] ?? '');
    $new_accompagnatore = $acc_nome !== '' ? [
        'nome'      => $acc_nome,
        'titolo'    => trim($_POST['accompagnatore_titolo'] ?? ''),
        'bio'       => trim($_POST['accompagnatore_bio'] ?? ''),
        'foto'      => trim($_POST['accompagnatore_foto'] ?? ''),
        'whatsapp'  => trim($_POST['accompagnatore_whatsapp'] ?? ''),
        'instagram' => trim($_POST['accompagnatore_instagram'] ?? ''),
    ] : ($trip['accompagnatore'] ?? null);

    // ── Volo ──────────────────────────────────────────────────────
    $volo_incluso = isset($_POST['volo_incluso']);
    if ($volo_incluso) {
        $new_volo = [
            'incluso' => true,
            'andata' => [
                'data'               => trim($_POST['volo_andata_data'] ?? ''),
                'compagnia'          => trim($_POST['volo_andata_compagnia'] ?? ''),
                'partenza_aeroporto' => trim($_POST['volo_andata_partenza'] ?? ''),
                'arrivo_aeroporto'   => trim($_POST['volo_andata_arrivo'] ?? ''),
                'numero_volo'        => trim($_POST['volo_andata_numero'] ?? ''),
                'orario_partenza'    => trim($_POST['volo_andata_orario_partenza'] ?? ''),
                'scalo'              => trim($_POST['volo_andata_scalo'] ?? ''),
            ],
            'ritorno' => [
                'data'               => trim($_POST['volo_ritorno_data'] ?? ''),
                'compagnia'          => trim($_POST['volo_ritorno_compagnia'] ?? ''),
                'partenza_aeroporto' => trim($_POST['volo_ritorno_partenza'] ?? ''),
                'arrivo_aeroporto'   => trim($_POST['volo_ritorno_arrivo'] ?? ''),
                'numero_volo'        => trim($_POST['volo_ritorno_numero'] ?? ''),
                'orario_partenza'    => trim($_POST['volo_ritorno_orario_partenza'] ?? ''),
                'scalo'              => trim($_POST['volo_ritorno_scalo'] ?? ''),
            ],
        ];
    } else {
        $new_volo = $trip['volo'] ?? ['incluso' => false];
    }

    // ── Hotel ─────────────────────────────────────────────────────
    $hotel_citta       = $_POST['hotel_citta']       ?? [];
    $hotel_nome        = $_POST['hotel_nome']        ?? [];
    $hotel_stelle      = $_POST['hotel_stelle']      ?? [];
    $hotel_notti       = $_POST['hotel_notti']       ?? [];
    $hotel_descrizione = $_POST['hotel_descrizione'] ?? [];
    $hotel_foto        = $_POST['hotel_foto']        ?? [];
    $hotel_indirizzo   = $_POST['hotel_indirizzo']   ?? [];
    $new_hotel = [];
    foreach ($hotel_citta as $i => $citta) {
        if (trim($citta) === '' && trim($hotel_nome[$i] ?? '') === '') continue;
        $new_hotel[] = [
            'citta'            => trim($citta),
            'nome'             => trim($hotel_nome[$i] ?? ''),
            'stelle'           => (int)($hotel_stelle[$i] ?? 4),
            'notti'            => (int)($hotel_notti[$i] ?? 1),
            'descrizione'      => trim($hotel_descrizione[$i] ?? ''),
            'image_url'        => trim($hotel_foto[$i] ?? ''),
            'indirizzo'        => trim($hotel_indirizzo[$i] ?? ''),
            'inclusa_colazione'=> isset($_POST['hotel_colazione_' . $i]),
        ];
    }
    if (empty($new_hotel) && !isset($_POST['hotel_citta'])) {
        $new_hotel = $trip['hotel'] ?? [];
    }

    // Assemble trip data
    $trip_data = [
        'slug'              => $new_slug,
        'title'             => trim($_POST['title'] ?? ''),
        'continent'         => $_POST['continent'] ?? 'europa',
        'status'            => $_POST['status'] ?? 'programmata',
        'published'         => $published,
        'deleted'           => $trip['deleted'] ?? false,
        'position'          => $trip['position'] ?? 0,
        'preview_token'     => $preview_token,
        'commission_rate'   => (float)($_POST['commission_rate'] ?? 10),
        'date_start'        => $_POST['start_date'] ?? '',
        'date_end'          => $_POST['end_date'] ?? '',
        'duration'          => trim($_POST['duration'] ?? ''),
        'price_from'        => (int)($_POST['price_from'] ?? 0),
        'hero_image'        => trim($_POST['hero_image'] ?? ''),
        'gallery'           => $gallery,
        'short_description' => trim($_POST['short_description'] ?? ''),
        'full_description'  => trim($_POST['full_description'] ?? ''),
        'itinerary'         => $itinerary,
        'included'          => $included,
        'excluded'          => $excluded,
        'tags'              => $tags,
        'form_config'       => $trip['form_config'] ?? [],
        'accompagnatore'    => $new_accompagnatore,
        'volo'              => $new_volo,
        'hotel'             => $new_hotel,
    ];

    if (empty($errors)) {
        $all_trips = load_trips();
        $found = false;
        foreach ($all_trips as &$t) {
            if ($t['slug'] === ($is_new ? $new_slug : ($slug_param ?? $new_slug))) {
                $t = $trip_data;
                $found = true;
                break;
            }
        }
        unset($t);
        if (!$found) {
            $all_trips[] = $trip_data;
        }
        save_trips($all_trips);
        $active_tab = preg_replace('/[^a-z]/', '', $_POST['active_tab'] ?? '');
        header('Location: /admin/edit-trip.php?slug=' . urlencode($new_slug) . '&saved=1' . ($active_tab ? '&tab=' . $active_tab : ''));
        exit;
    }
    // If errors: re-populate $trip with submitted values so form retains input
    $trip = $trip_data;
    $trip['slug'] = $_POST['slug'] ?? $trip['slug'];
    $slug_locked = !$is_new && ($trip_data['published']);
}

// ── Tags data ─────────────────────────────────────────────────────────────────
$all_tags = load_tags();
$tags_by_category = [];
foreach ($all_tags as $tag) {
    $cat = $tag['category'] ?? 'altro';
    $tags_by_category[$cat][] = $tag;
}

// Selected tags set
$selected_tags = array_flip($trip['tags'] ?? []);

// Gallery for textarea
$gallery_text = implode("\n", $trip['gallery'] ?? []);

// Included / excluded for textarea
$included_text = implode("\n", $trip['included'] ?? []);
$excluded_text = implode("\n", $trip['excluded'] ?? []);

// Preview URL
$preview_token = $trip['preview_token'] ?? '';
$preview_url   = $preview_token !== '' ? '/viaggio.php?slug=' . htmlspecialchars($trip['slug']) . '&preview=' . $preview_token : '';
$preview_token_val = $trip['preview_token'] ?? '';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_new ? 'Nuovo Viaggio' : ('Modifica: ' . htmlspecialchars($trip['title'])) ?> — Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/admin/admin.css">
    <style>
        /* ── Edit form specific styles ── */
        .edit-page {
            padding-bottom: 100px; /* space for sticky footer */
        }
        .page-header {
            padding: 20px 24px 0;
            max-width: 960px;
            margin: 0 auto;
        }
        .page-header h1 {
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
        }
        .page-header .breadcrumb {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }
        .page-header .breadcrumb a:hover { text-decoration: underline; }

        .edit-container {
            max-width: 960px;
            margin: 0 auto;
            padding: 20px 24px;
        }

        /* ── Alert / errors ── */
        .alert-error {
            background: #fff5f5;
            border: 1px solid #feb2b2;
            border-radius: var(--radius);
            padding: 12px 16px;
            color: var(--danger);
            margin-bottom: 16px;
            font-size: 13px;
        }
        .alert-success {
            background: #f0fff4;
            border: 1px solid #9ae6b4;
            border-radius: var(--radius);
            padding: 12px 16px;
            color: var(--success);
            margin-bottom: 16px;
            font-size: 13px;
        }

        /* ── Tab nav ── */
        .tab-nav {
            display: flex;
            gap: 4px;
            border-bottom: 2px solid var(--border);
            margin-bottom: 24px;
            overflow-x: auto;
        }
        .tab-btn {
            background: none;
            border: none;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-muted);
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            white-space: nowrap;
            transition: color 0.15s, border-color 0.15s;
        }
        .tab-btn:hover { color: var(--text); }
        .tab-btn.active {
            color: var(--navy);
            border-bottom-color: #CC0031;
        }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }

        /* ── Form layout ── */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .form-grid.thirds {
            grid-template-columns: 1fr 1fr 1fr;
        }
        .form-col-span-2 { grid-column: span 2; }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
        }
        .form-group label .muted {
            font-weight: 400;
            color: var(--text-muted);
            font-size: 12px;
        }
        .form-group input[type="text"],
        .form-group input[type="url"],
        .form-group input[type="number"],
        .form-group input[type="date"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 14px;
            font-family: inherit;
            color: var(--text);
            background: var(--white);
            transition: border-color 0.15s;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #000744;
            box-shadow: 0 0 0 3px rgba(0,7,68,0.1);
        }
        .form-group input[readonly] {
            background: #f8f8f8;
            color: var(--text-muted);
            cursor: default;
        }
        .form-group textarea {
            resize: vertical;
        }
        .field-hint {
            font-size: 12px;
            color: var(--text-muted);
        }
        .section-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text);
            margin: 24px 0 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid var(--border);
        }
        .section-title:first-child { margin-top: 0; }

        /* ── Slug field ── */
        .slug-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .slug-row input { flex: 1; }
        .slug-locked-badge {
            font-size: 11px;
            color: var(--text-muted);
            background: #f0f0f0;
            padding: 3px 8px;
            border-radius: 12px;
            white-space: nowrap;
        }

        /* ── Duration display ── */
        .duration-display {
            padding: 9px 12px;
            background: #f8f8f8;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 14px;
            color: var(--text-muted);
        }

        /* ── Preview token ── */
        .token-box {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #f8f8f8;
            border: 1px solid var(--border);
            border-radius: var(--radius);
        }
        .token-box code {
            font-size: 12px;
            color: var(--text-muted);
            font-family: monospace;
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
            font-weight: 500;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: var(--white);
            cursor: pointer;
            color: var(--text);
            white-space: nowrap;
        }
        .btn-small:hover { background: #f0f0f0; }

        /* ── Tags ── */
        .tags-section { margin-top: 8px; }
        .tag-category-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 12px 0 6px;
        }
        .tag-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .tag-pill {
            padding: 5px 12px;
            border-radius: 20px;
            border: 1.5px solid #000744;
            background: transparent;
            color: #000744;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
            font-family: inherit;
        }
        .tag-pill.selected {
            background: #000744;
            color: var(--white);
        }
        .custom-tag-row {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }
        .custom-tag-row input {
            flex: 1;
            padding: 7px 10px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 13px;
            font-family: inherit;
        }
        .custom-tag-row input:focus {
            outline: none;
            border-color: #000744;
        }

        /* ── Media tab ── */
        .hero-preview-wrap {
            margin-top: 10px;
        }
        #hero-preview {
            max-height: 200px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            object-fit: cover;
            width: 100%;
            display: none;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-top: 10px;
        }
        .gallery-thumb {
            position: relative;
            aspect-ratio: 16/9;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid var(--border);
        }
        .gallery-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .gallery-thumb-remove {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 20px;
            height: 20px;
            background: rgba(0,0,0,0.6);
            border: none;
            border-radius: 50%;
            color: #fff;
            font-size: 11px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        /* ── Char counter ── */
        .char-counter {
            font-size: 12px;
            color: var(--text-muted);
            text-align: right;
        }
        .char-counter.warning { color: var(--danger); }

        /* ── Itinerary builder ── */
        .itinerary-row {
            display: grid;
            grid-template-columns: 28px 40px 1fr auto auto auto;
            gap: 8px;
            align-items: start;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 12px;
            margin-bottom: 8px;
            cursor: grab;
        }
        .itinerary-row.dragging {
            opacity: 0.5;
            cursor: grabbing;
        }
        .drag-handle {
            color: var(--text-muted);
            font-size: 16px;
            cursor: grab;
            padding-top: 2px;
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }
        .day-num {
            background: #000744;
            color: var(--white);
            font-weight: 700;
            font-size: 12px;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .itinerary-fields {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .itinerary-fields input,
        .itinerary-fields textarea {
            width: 100%;
            padding: 7px 10px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 13px;
            font-family: inherit;
            color: var(--text);
        }
        .itinerary-fields input:focus,
        .itinerary-fields textarea:focus {
            outline: none;
            border-color: #000744;
        }
        .btn-icon {
            background: none;
            border: 1px solid var(--border);
            border-radius: 6px;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 12px;
            flex-shrink: 0;
        }
        .btn-icon:hover { background: #f0f0f0; color: var(--text); }
        .btn-icon.btn-danger-icon { color: var(--danger); }
        .btn-icon.btn-danger-icon:hover { background: #fff5f5; border-color: #feb2b2; }
        .itinerary-actions {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        /* ── Sticky footer ── */
        .sticky-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 200;
            background: var(--white);
            border-top: 1px solid var(--border);
            box-shadow: 0 -2px 8px rgba(0,0,0,0.08);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .footer-title-preview {
            flex: 1;
            font-size: 13px;
            color: var(--text-muted);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .footer-actions {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }
        .btn-preview {
            padding: 8px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--white);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            color: var(--text);
            font-family: inherit;
        }
        .btn-preview:hover:not(:disabled) { background: #f0f0f0; }
        .btn-preview:disabled { opacity: 0.5; cursor: default; }
        .btn-draft {
            padding: 8px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--white);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            color: var(--text);
            font-family: inherit;
        }
        .btn-draft:hover { background: #f0f0f0; }
        .btn-publish {
            padding: 8px 20px;
            border: none;
            border-radius: var(--radius);
            background: #CC0031;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            color: var(--white);
            font-family: inherit;
        }
        .btn-publish:hover { background: #a80028; }

        /* ── Form config placeholder ── */
        .coming-soon-box {
            text-align: center;
            padding: 60px 24px;
            color: var(--text-muted);
        }
        .coming-soon-box i {
            font-size: 48px;
            margin-bottom: 16px;
            display: block;
            opacity: 0.4;
        }
        .coming-soon-box h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text);
        }

        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-col-span-2 { grid-column: span 1; }
            .gallery-grid { grid-template-columns: repeat(2, 1fr); }
            .itinerary-row {
                grid-template-columns: 28px 36px 1fr;
            }
        }
    </style>
</head>
<body>

<!-- ── Admin navigation ── -->
<nav class="admin-nav">
    <span class="admin-nav__logo"><span class="logo-icon"><i class="fa-solid fa-compass"></i></span> Viaggia col Baffo</span>
    <ul class="admin-nav__links">
        <li><a href="/admin/" class="admin-nav__link">Pannello</a></li>
        <li><a href="/admin/settings.php" class="admin-nav__link">Impostazioni</a></li>
        <li><a href="/admin/tags.php" class="admin-nav__link">Tag</a></li>
    </ul>
    <div class="admin-nav__right">
        <a href="/" target="_blank" class="admin-nav__link">Vai al sito <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:11px;"></i></a>
        <a href="/admin/logout.php" class="admin-nav__link" style="color:var(--danger);">Logout</a>
    </div>
</nav>

<div class="edit-page">
    <div class="page-header">
        <div class="breadcrumb"><a href="/admin/">Pannello</a> / <?= $is_new ? 'Nuovo Viaggio' : htmlspecialchars($trip['title']) ?></div>
        <h1><?= $is_new ? 'Nuovo Viaggio' : 'Modifica Viaggio' ?></h1>
    </div>

    <form method="post" id="edit-form" action="<?= $is_new ? '/admin/edit-trip.php?new=1' : '/admin/edit-trip.php?slug=' . urlencode($slug_param ?? $trip['slug']) ?>">
    <input type="hidden" id="active_tab_field" name="active_tab" value="">
    <input type="hidden" name="action" id="form-action-hidden" value="draft">

    <div class="edit-container">

        <?php if ($saved): ?>
        <div class="alert-success"><i class="fa-solid fa-circle-check"></i> Salvato con successo.</div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
        <div class="alert-error">
            <strong>Errori:</strong>
            <ul style="margin:4px 0 0 16px;">
                <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- ── Tab navigation ── -->
        <nav class="tab-nav" id="tab-nav">
            <button type="button" class="tab-btn" data-tab="info">Info Base</button>
            <button type="button" class="tab-btn" data-tab="media">Media</button>
            <button type="button" class="tab-btn" data-tab="content">Contenuto</button>
            <button type="button" class="tab-btn" data-tab="itinerario">Itinerario</button>
            <button type="button" class="tab-btn" data-tab="formconfig">Form Config</button>
            <button type="button" class="tab-btn" data-tab="accompagnatore">Accompagnatore</button>
            <button type="button" class="tab-btn" data-tab="volo">Volo</button>
            <button type="button" class="tab-btn" data-tab="hotel">Hotel</button>
        </nav>

        <!-- ══════════════════════════════════════════════════ -->
        <!-- TAB: Info Base                                     -->
        <!-- ══════════════════════════════════════════════════ -->
        <div class="tab-panel" id="tab-info">

            <h3 class="section-title">Dati principali</h3>
            <div class="form-group form-col-span-2" style="margin-bottom:16px;">
                <label for="title">Titolo</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($trip['title'] ?? '') ?>" placeholder="Es. West America Aprile 2026" required>
            </div>

            <div class="form-group" style="margin-bottom:16px;">
                <label for="slug">Slug (URL) <?= $slug_locked ? '<span class="slug-locked-badge"><i class="fa-solid fa-lock" style="font-size:9px;"></i> Bloccato dopo prima pubblicazione</span>' : '' ?></label>
                <div class="slug-row">
                    <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($trip['slug'] ?? '') ?>"
                        placeholder="es. west-america-aprile-2026"
                        <?= $slug_locked ? 'readonly' : '' ?>>
                </div>
                <span class="field-hint">Usato nell'URL: /viaggio/<strong id="slug-preview"><?= htmlspecialchars($trip['slug'] ?? '') ?></strong></span>
            </div>

            <div class="form-grid" style="margin-bottom:16px;">
                <div class="form-group">
                    <label for="continent">Continente</label>
                    <select id="continent" name="continent">
                        <?php foreach (['america' => 'America', 'asia' => 'Asia', 'europa' => 'Europa', 'africa' => 'Africa', 'oceania' => 'Oceania', 'medio-oriente' => 'Medio Oriente'] as $val => $lbl): ?>
                        <option value="<?= $val ?>" <?= ($trip['continent'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Stato disponibilità</label>
                    <select id="status" name="status">
                        <?php foreach (['confermata' => 'Confermata', 'ultimi-posti' => 'Ultimi posti', 'sold-out' => 'Sold out', 'programmata' => 'Programmata'] as $val => $lbl): ?>
                        <option value="<?= $val ?>" <?= ($trip['status'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <h3 class="section-title">Date e prezzi</h3>
            <div class="form-grid thirds" style="margin-bottom:16px;">
                <div class="form-group">
                    <label for="start_date">Data inizio</label>
                    <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($trip['date_start'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="end_date">Data fine</label>
                    <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($trip['date_end'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Durata <span class="muted">(calcolata automaticamente)</span></label>
                    <div class="duration-display" id="duration-display"><?= htmlspecialchars($trip['duration'] ?? '—') ?></div>
                    <input type="hidden" name="duration" id="duration-input" value="<?= htmlspecialchars($trip['duration'] ?? '') ?>">
                </div>
            </div>

            <div class="form-grid" style="margin-bottom:16px;">
                <div class="form-group">
                    <label for="price_from">Prezzo da (€)</label>
                    <input type="number" id="price_from" name="price_from" min="0" step="10" value="<?= (int)($trip['price_from'] ?? 0) ?>">
                </div>
                <div class="form-group">
                    <label for="commission_rate">Commissione agenzie (%) <span class="muted">— solo admin, non mostrata al pubblico</span></label>
                    <input type="number" id="commission_rate" name="commission_rate" min="0" max="30" step="0.5" value="<?= (float)($trip['commission_rate'] ?? 10) ?>">
                </div>
            </div>

            <?php if (!$is_new && $preview_token !== ''): ?>
            <h3 class="section-title">Anteprima</h3>
            <div class="form-group" style="margin-bottom:16px;">
                <label>Token anteprima</label>
                <div class="token-box">
                    <code id="token-display"><?= substr($preview_token, 0, 16) ?>...</code>
                    <button type="button" class="btn-small" id="regen-token-btn" onclick="regenToken()">
                        <i class="fa-solid fa-rotate"></i> Rigenera
                    </button>
                </div>
                <span class="field-hint">URL anteprima: <a href="<?= htmlspecialchars($preview_url) ?>" target="_blank" style="color:#000744;"><?= htmlspecialchars($preview_url) ?></a></span>
            </div>
            <?php endif; ?>

            <h3 class="section-title">Tag</h3>
            <div class="tags-section">
                <?php foreach ($tags_by_category as $cat => $cat_tags): ?>
                <div class="tag-category-label"><?= htmlspecialchars(ucfirst($cat)) ?></div>
                <div class="tag-pills" id="tag-pills-<?= htmlspecialchars(str_replace(' ', '-', $cat)) ?>">
                    <?php foreach ($cat_tags as $tag): ?>
                    <button type="button"
                        class="tag-pill<?= isset($selected_tags[$tag['slug']]) ? ' selected' : '' ?>"
                        data-slug="<?= htmlspecialchars($tag['slug']) ?>"
                        onclick="toggleTag(this)">
                        <?= htmlspecialchars($tag['label']) ?>
                    </button>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>

                <div class="custom-tag-row">
                    <input type="text" id="custom-tag-input" placeholder="Tag personalizzato...">
                    <button type="button" class="btn-small" onclick="addCustomTag()"><i class="fa-solid fa-plus"></i> Aggiungi</button>
                </div>

                <input type="hidden" name="tags_json" id="tags-json" value="<?= htmlspecialchars(json_encode(array_keys($selected_tags))) ?>">
            </div>

        </div><!-- /tab-info -->

        <!-- ══════════════════════════════════════════════════ -->
        <!-- TAB: Media                                          -->
        <!-- ══════════════════════════════════════════════════ -->
        <div class="tab-panel" id="tab-media">

            <h3 class="section-title">Immagine hero</h3>
            <div class="form-group" style="margin-bottom:16px;">
                <label for="hero_image">URL immagine hero</label>
                <input type="url" id="hero_image" name="hero_image"
                    value="<?= htmlspecialchars($trip['hero_image'] ?? '') ?>"
                    placeholder="https://images.unsplash.com/..."
                    oninput="updateHeroPreview(this.value)">
            </div>
            <div class="hero-preview-wrap">
                <img id="hero-preview"
                    src="<?= htmlspecialchars($trip['hero_image'] ?? '') ?>"
                    alt="Anteprima hero"
                    style="<?= !empty($trip['hero_image']) ? 'display:block;' : 'display:none;' ?>">
            </div>

            <h3 class="section-title" style="margin-top:24px;">Galleria</h3>
            <div class="form-group" style="margin-bottom:8px;">
                <label for="gallery">URL galleria <span class="muted">una URL per riga</span></label>
                <textarea id="gallery" name="gallery" rows="6"
                    placeholder="https://images.unsplash.com/photo-1...&#10;https://images.unsplash.com/photo-2..."
                    oninput="updateGalleryPreview(this.value)"><?= htmlspecialchars($gallery_text) ?></textarea>
            </div>
            <div class="gallery-grid" id="gallery-grid">
                <?php foreach ($trip['gallery'] ?? [] as $img_url): ?>
                <?php if (trim($img_url)): ?>
                <div class="gallery-thumb" data-url="<?= htmlspecialchars($img_url) ?>">
                    <img src="<?= htmlspecialchars($img_url) ?>" alt="gallery" loading="lazy">
                    <button type="button" class="gallery-thumb-remove" onclick="removeGalleryUrl(this)" title="Rimuovi"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>

        </div><!-- /tab-media -->

        <!-- ══════════════════════════════════════════════════ -->
        <!-- TAB: Contenuto                                     -->
        <!-- ══════════════════════════════════════════════════ -->
        <div class="tab-panel" id="tab-content">

            <h3 class="section-title">Descrizioni</h3>
            <div class="form-group" style="margin-bottom:16px;">
                <label for="short_description">Descrizione breve <span class="muted">(max 160 caratteri)</span></label>
                <textarea id="short_description" name="short_description" rows="3"
                    maxlength="160"
                    placeholder="Un breve testo che appare nelle card e nelle anteprime..."
                    oninput="updateShortDescCounter(this)"><?= htmlspecialchars($trip['short_description'] ?? '') ?></textarea>
                <div class="char-counter" id="short-desc-counter">
                    <?php $sd_len = strlen($trip['short_description'] ?? ''); ?>
                    <span id="short-desc-remaining"><?= 160 - $sd_len ?></span> / 160 caratteri rimanenti
                </div>
            </div>

            <div class="form-group" style="margin-bottom:24px;">
                <label for="full_description">Descrizione completa</label>
                <textarea id="full_description" name="full_description" rows="10"
                    placeholder="Descrizione lunga del viaggio, mostrata nella pagina dettaglio..."><?= htmlspecialchars($trip['full_description'] ?? '') ?></textarea>
            </div>

            <h3 class="section-title">Incluso / Escluso</h3>
            <div class="form-grid" style="margin-bottom:16px;">
                <div class="form-group">
                    <label for="included">Cosa è incluso <span class="muted">(una voce per riga)</span></label>
                    <textarea id="included" name="included" rows="8"
                        placeholder="Voli internazionali&#10;Hotel 4 stelle&#10;Transfer aeroporto"><?= htmlspecialchars($included_text) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="excluded">Cosa non è incluso <span class="muted">(una voce per riga)</span></label>
                    <textarea id="excluded" name="excluded" rows="8"
                        placeholder="Visti&#10;Mance&#10;Spese personali"><?= htmlspecialchars($excluded_text) ?></textarea>
                </div>
            </div>

        </div><!-- /tab-content -->

        <!-- ══════════════════════════════════════════════════ -->
        <!-- TAB: Itinerario                                    -->
        <!-- ══════════════════════════════════════════════════ -->
        <div class="tab-panel" id="tab-itinerario">

            <h3 class="section-title">Giorni del viaggio</h3>
            <div id="itinerary-rows">
                <?php foreach ($trip['itinerary'] ?? [] as $i => $day): ?>
                <div class="itinerary-row" draggable="true">
                    <span class="drag-handle"><i class="fa-solid fa-grip-vertical"></i></span>
                    <span class="day-num"><?= $i + 1 ?></span>
                    <div class="itinerary-fields">
                        <input type="text" name="itinerary_title[]"
                            value="<?= htmlspecialchars($day['title'] ?? '') ?>"
                            placeholder="Titolo giorno">
                        <input type="text" name="itinerary_location[]"
                            value="<?= htmlspecialchars($day['location'] ?? '') ?>"
                            placeholder="Città / Luogo (es. Las Vegas, NV)">
                        <input type="text" name="itinerary_date[]"
                            value="<?= htmlspecialchars($day['date'] ?? '') ?>"
                            placeholder="Data (es. 5 Aprile 2026)">
                        <input type="url" name="itinerary_image[]"
                            value="<?= htmlspecialchars($day['image_url'] ?? '') ?>"
                            placeholder="URL Immagine giorno">
                        <textarea name="itinerary_desc[]" rows="3"
                            placeholder="Descrizione..."><?= htmlspecialchars($day['description'] ?? '') ?></textarea>
                    </div>
                    <div class="itinerary-actions">
                        <button type="button" class="btn-icon" onclick="moveRow(this,-1)" title="Su"><i class="fa-solid fa-chevron-up"></i></button>
                        <button type="button" class="btn-icon" onclick="moveRow(this,1)" title="Giu"><i class="fa-solid fa-chevron-down"></i></button>
                        <button type="button" class="btn-icon btn-danger-icon" onclick="removeRow(this)" title="Elimina"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <button type="button" class="btn-small" onclick="addItineraryRow()" style="margin-top:12px; padding: 8px 16px;">
                <i class="fa-solid fa-plus"></i> Aggiungi Giorno
            </button>

        </div><!-- /tab-itinerario -->

        <!-- ══════════════════════════════════════════════════ -->
        <!-- TAB: Form Config                                   -->
        <!-- ══════════════════════════════════════════════════ -->
        <?php
        $fc = $trip['form_config'] ?? [];
        $fc_room_types = $fc['room_types'] ?? [];
        $fc_brackets   = $fc['child_discount_brackets'] ?? [];
        ?>
        <div class="tab-panel" id="tab-formconfig">

          <!-- SECTION A: Trip Info -->
          <div class="card" style="margin-bottom:1.5rem;">
            <h3 style="margin-bottom:1.25rem;">A — Informazioni Viaggio</h3>
            <div class="form-group">
              <label for="fc-webhook">Webhook URL (Pabbly) <span style="color:#cc0031">*</span></label>
              <input type="text" id="fc-webhook"
                     value="<?= htmlspecialchars($fc['webhook_url'] ?? '') ?>"
                     placeholder="https://connect.pabbly.com/...">
            </div>
            <div class="form-group">
              <label for="fc-prezzo-base">Prezzo base p.p. (€) — camera doppia <span style="color:#cc0031">*</span></label>
              <input type="number" id="fc-prezzo-base" min="0" step="1"
                     value="<?= (int)($fc['prezzo_base_persona'] ?? 0) ?>"
                     placeholder="es. 4350">
            </div>
          </div>

          <!-- SECTION B: Room Types -->
          <div class="card" style="margin-bottom:1.5rem;">
            <h3 style="margin-bottom:0.5rem;">B — Tipologie Camera</h3>
            <p style="color:var(--text-muted);font-size:0.875rem;margin-bottom:1rem;">Seleziona le tipologie disponibili. Ogni tipo attivato mostra un campo di configurazione.</p>
            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:1.25rem;">
              <?php foreach (['X1','X2','X3','X4','X5'] as $rt): ?>
              <button type="button" class="room-pill <?= in_array($rt, $fc_room_types) ? 'active' : '' ?>"
                      data-room="<?= $rt ?>" onclick="toggleRoom(this)">
                <?= $rt ?>
              </button>
              <?php endforeach; ?>
            </div>
            <div id="room-config-panels">
              <div class="room-panel" id="rp-X1" style="display:<?= in_array('X1',$fc_room_types)?'block':'none' ?>;">
                <div class="form-group">
                  <label for="fc-suppl-singola">X1 — Supplemento Singola (€)</label>
                  <input type="number" id="fc-suppl-singola" min="0" step="1"
                         value="<?= (int)($fc['supplemento_singola'] ?? 0) ?>" placeholder="es. 1600">
                  <small>Aggiunto al prezzo base per camera singola</small>
                </div>
              </div>
              <div class="room-panel" id="rp-X3" style="display:<?= in_array('X3',$fc_room_types)?'block':'none' ?>;">
                <div class="form-group">
                  <label for="fc-sconto-3">X3 — Sconto 3° letto (€)</label>
                  <input type="number" id="fc-sconto-3" min="0" step="1"
                         value="<?= (int)($fc['sconto_terzo_letto'] ?? 0) ?>" placeholder="es. 1350">
                  <small>Sottratto dal prezzo base per la 3ª persona</small>
                </div>
              </div>
              <div class="room-panel" id="rp-X4" style="display:<?= in_array('X4',$fc_room_types)?'block':'none' ?>;">
                <div class="form-group">
                  <label for="fc-sconto-4">X4 — Sconto 4° letto (€)</label>
                  <input type="number" id="fc-sconto-4" min="0" step="1"
                         value="<?= (int)($fc['sconto_quarto_letto'] ?? 0) ?>" placeholder="es. 1350">
                  <small>Sottratto dal prezzo base per la 4ª persona</small>
                </div>
              </div>
              <div class="room-panel" id="rp-X5" style="display:<?= in_array('X5',$fc_room_types)?'block':'none' ?>;">
                <div class="form-group">
                  <label for="fc-sconto-5">X5 — Sconto 5° letto (€)</label>
                  <input type="number" id="fc-sconto-5" min="0" step="1"
                         value="<?= (int)($fc['sconto_quinto_letto'] ?? 0) ?>" placeholder="es. 0">
                  <small>Sottratto dal prezzo base per la 5ª persona</small>
                </div>
              </div>
            </div>
          </div>

          <!-- SECTION C: Child Discounts -->
          <div class="card" style="margin-bottom:1.5rem;">
            <h3 style="margin-bottom:0.75rem;">C — Sconti Bambini</h3>
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:1rem;">
              <label class="toggle-switch">
                <input type="checkbox" id="fc-child-enabled"
                       <?= !empty($fc['child_discounts_enabled']) ? 'checked' : '' ?>
                       onchange="toggleSection('child-brackets-panel',this.checked)">
                <span class="toggle-slider"></span>
              </label>
              <span>Abilita sconti bambini</span>
            </div>
            <div id="child-brackets-panel" style="display:<?= !empty($fc['child_discounts_enabled'])?'block':'none' ?>;">
              <p style="font-size:0.875rem;color:var(--text-muted);margin-bottom:0.75rem;">I bambini contano nei posti letto (X2/X3/X4/X5). Aggiungi fasce di età con lo sconto applicato.</p>
              <div id="fc-brackets-list">
                <?php foreach ($fc_brackets as $i => $b): ?>
                <div class="bracket-row" style="display:flex;gap:10px;align-items:center;margin-bottom:8px;">
                  <input type="number" class="br-min" min="0" max="7" value="<?= (int)$b['min_age'] ?>" placeholder="Min" style="width:70px;">
                  <span>–</span>
                  <input type="number" class="br-max" min="0" max="7" value="<?= (int)$b['max_age'] ?>" placeholder="Max" style="width:70px;">
                  <span>anni: sconto</span>
                  <input type="number" class="br-discount" min="0" value="<?= (int)$b['discount'] ?>" placeholder="€" style="width:90px;">
                  <button type="button" onclick="this.parentElement.remove()" style="color:#cc0031;background:none;border:none;cursor:pointer;font-size:1.2rem;">×</button>
                </div>
                <?php endforeach; ?>
              </div>
              <button type="button" class="btn-secondary" style="margin-top:8px;" onclick="addBracket()">
                <i class="fa-solid fa-plus"></i> Aggiungi fascia
              </button>
            </div>
          </div>

          <!-- SECTION D: Insurance -->
          <div class="card" style="margin-bottom:1.5rem;">
            <h3 style="margin-bottom:0.75rem;">D — Assicurazione</h3>
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:1rem;">
              <label class="toggle-switch">
                <input type="checkbox" id="fc-insurance-enabled"
                       <?= !empty($fc['insurance_enabled']) ? 'checked' : '' ?>
                       onchange="toggleSection('insurance-panel',this.checked)">
                <span class="toggle-slider"></span>
              </label>
              <span>Abilita opzione assicurazione</span>
            </div>
            <div id="insurance-panel" style="display:<?= !empty($fc['insurance_enabled'])?'block':'none' ?>;">
              <div class="form-group">
                <label for="fc-assicurazione">Percentuale assicurazione (%)</label>
                <input type="number" id="fc-assicurazione" min="0" max="20" step="0.5"
                       value="<?= (float)($fc['percentuale_assicurazione'] ?? 5) ?>" placeholder="5">
              </div>
            </div>
          </div>

          <!-- SECTION E: Competitor -->
          <div class="card" style="margin-bottom:1.5rem;">
            <h3 style="margin-bottom:0.75rem;">E — Confronto Concorrenza</h3>
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:1rem;">
              <label class="toggle-switch">
                <input type="checkbox" id="fc-competitor-enabled"
                       <?= !empty($fc['competitor_enabled']) ? 'checked' : '' ?>
                       onchange="toggleSection('competitor-panel',this.checked)">
                <span class="toggle-slider"></span>
              </label>
              <span>Mostra risparmio vs concorrenza</span>
            </div>
            <div id="competitor-panel" style="display:<?= !empty($fc['competitor_enabled'])?'block':'none' ?>;">
              <div class="form-grid-2">
                <div class="form-group">
                  <label for="fc-concorrenza-pp">Prezzo concorrenza p.p. (€)</label>
                  <input type="number" id="fc-concorrenza-pp" min="0" step="1"
                         value="<?= (int)($fc['prezzo_concorrenza_persona'] ?? 0) ?>" placeholder="es. 7000">
                </div>
                <div class="form-group">
                  <label for="fc-concorrenza-extra">Concorrenza letti extra (€)</label>
                  <input type="number" id="fc-concorrenza-extra" min="0" step="1"
                         value="<?= (int)($fc['prezzo_concorrenza_letti_extra'] ?? 0) ?>" placeholder="es. 5000">
                </div>
              </div>
            </div>
          </div>

          <!-- SECTION F: Agency Code -->
          <div class="card" style="margin-bottom:1.5rem;">
            <h3 style="margin-bottom:0.75rem;">F — Codice Agenzia</h3>
            <div class="form-group">
              <label for="fc-agency-plain">Codice agenzia (testo)</label>
              <input type="text" id="fc-agency-plain" placeholder="es. 8823" autocomplete="off"
                     value="<?php
                       $saved_hash = $fc['agency_code_hash'] ?? '';
                       echo ($saved_hash === 'af97d1baebca1eaae1ce418c082402e60c2529ef719983ad7c8dda6ea1f8e8ee' || $saved_hash === '') ? '8823' : '';
                     ?>">
              <small>L'hash viene calcolato automaticamente al salvataggio</small>
            </div>
            <input type="hidden" id="fc-agency-hash"
                   value="<?= htmlspecialchars($fc['agency_code_hash'] ?? 'af97d1baebca1eaae1ce418c082402e60c2529ef719983ad7c8dda6ea1f8e8ee') ?>">
          </div>

          <!-- Save -->
          <div style="display:flex;gap:12px;align-items:center;margin-bottom:2rem;">
            <button type="button" class="btn btn-primary" onclick="saveFormConfig()">
              <i class="fa-solid fa-save"></i> Salva Configurazione Form
            </button>
            <span id="save-fc-msg" style="display:none; color:var(--success);">
              <i class="fa-solid fa-check"></i> Salvato
            </span>
          </div>

        </div><!-- /tab-formconfig -->

<?php
$acc        = $trip['accompagnatore'] ?? [];
$volo_data  = $trip['volo'] ?? [];
$hotels_data = $trip['hotel'] ?? [];
?>

<!-- ══ TAB: ACCOMPAGNATORE ══════════════════════════════════════ -->
<div class="tab-panel" id="tab-accompagnatore">
  <h3 class="section-title">Accompagnatore del Viaggio</h3>
  <p style="color:#888;font-size:13px;margin-bottom:20px;">Lascia vuoto se il viaggio non ha un accompagnatore dedicato.</p>
  <div class="form-group" style="margin-bottom:16px;">
    <label>Nome Accompagnatore</label>
    <input type="text" name="accompagnatore_nome" value="<?= htmlspecialchars($acc['nome'] ?? '') ?>" placeholder="es. Lorenzo D'Alessandro">
  </div>
  <div class="form-group" style="margin-bottom:16px;">
    <label>Titolo / Ruolo</label>
    <input type="text" name="accompagnatore_titolo" value="<?= htmlspecialchars($acc['titolo'] ?? '') ?>" placeholder="es. Il Baffo — Fondatore e Accompagnatore">
  </div>
  <div class="form-group" style="margin-bottom:16px;">
    <label>Biografia</label>
    <textarea name="accompagnatore_bio" rows="4" placeholder="Breve bio..."><?= htmlspecialchars($acc['bio'] ?? '') ?></textarea>
  </div>
  <div class="form-group" style="margin-bottom:8px;">
    <label>Foto URL</label>
    <input type="url" name="accompagnatore_foto" id="acc-foto-url"
           value="<?= htmlspecialchars($acc['foto'] ?? '') ?>"
           placeholder="https://..."
           oninput="var i=document.getElementById('acc-foto-preview');i.src=this.value;i.style.display=this.value?'block':'none';">
  </div>
  <img id="acc-foto-preview"
       src="<?= htmlspecialchars($acc['foto'] ?? '') ?>"
       style="<?= !empty($acc['foto']) ? 'display:block;' : 'display:none;' ?>max-height:120px;border-radius:50%;border:2px solid #ddd;margin-bottom:16px;">
  <div class="form-group" style="margin-bottom:16px;">
    <label>WhatsApp</label>
    <input type="text" name="accompagnatore_whatsapp" value="<?= htmlspecialchars($acc['whatsapp'] ?? '') ?>" placeholder="es. +39 333 1234567">
  </div>
  <div class="form-group" style="margin-bottom:24px;">
    <label>Instagram (handle senza @)</label>
    <input type="text" name="accompagnatore_instagram" value="<?= htmlspecialchars($acc['instagram'] ?? '') ?>" placeholder="es. lorenzobaffo">
  </div>
</div><!-- /tab-accompagnatore -->

<!-- ══ TAB: VOLO ════════════════════════════════════════════════ -->
<div class="tab-panel" id="tab-volo">
  <h3 class="section-title">Dettagli Volo</h3>
  <div class="form-group" style="margin-bottom:20px;">
    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
      <input type="checkbox" name="volo_incluso" id="volo_incluso" value="1"
             <?= !empty($volo_data['incluso']) ? 'checked' : '' ?>
             onchange="document.getElementById('volo-details-panel').style.display=this.checked?'block':'none'">
      <span>Volo incluso nel prezzo</span>
    </label>
  </div>
  <div id="volo-details-panel" style="display:<?= !empty($volo_data['incluso']) ? 'block' : 'none' ?>;">
    <?php $va = $volo_data['andata'] ?? []; $vr = $volo_data['ritorno'] ?? []; ?>
    <h3 class="section-title">Volo Andata</h3>
    <div class="form-grid" style="margin-bottom:16px;">
      <div class="form-group"><label>Data</label><input type="text" name="volo_andata_data" value="<?= htmlspecialchars($va['data'] ?? '') ?>" placeholder="es. 17 Aprile 2026"></div>
      <div class="form-group"><label>Compagnia</label><input type="text" name="volo_andata_compagnia" value="<?= htmlspecialchars($va['compagnia'] ?? '') ?>" placeholder="es. Lufthansa"></div>
    </div>
    <div class="form-grid" style="margin-bottom:16px;">
      <div class="form-group"><label>Aeroporto Partenza</label><input type="text" name="volo_andata_partenza" value="<?= htmlspecialchars($va['partenza_aeroporto'] ?? '') ?>" placeholder="es. Milano Malpensa (MXP)"></div>
      <div class="form-group"><label>Aeroporto Arrivo</label><input type="text" name="volo_andata_arrivo" value="<?= htmlspecialchars($va['arrivo_aeroporto'] ?? '') ?>" placeholder="es. Los Angeles (LAX)"></div>
    </div>
    <div class="form-grid" style="margin-bottom:16px;">
      <div class="form-group"><label>Numero Volo</label><input type="text" name="volo_andata_numero" value="<?= htmlspecialchars($va['numero_volo'] ?? '') ?>" placeholder="es. LH 234"></div>
      <div class="form-group"><label>Orario Partenza</label><input type="text" name="volo_andata_orario_partenza" value="<?= htmlspecialchars($va['orario_partenza'] ?? '') ?>" placeholder="es. 10:30"></div>
    </div>
    <div class="form-group" style="margin-bottom:24px;"><label>Scalo (opzionale)</label><input type="text" name="volo_andata_scalo" value="<?= htmlspecialchars($va['scalo'] ?? '') ?>" placeholder="es. Frankfurt (FRA) — 2h layover"></div>
    <h3 class="section-title">Volo Ritorno</h3>
    <div class="form-grid" style="margin-bottom:16px;">
      <div class="form-group"><label>Data</label><input type="text" name="volo_ritorno_data" value="<?= htmlspecialchars($vr['data'] ?? '') ?>" placeholder="es. 1 Maggio 2026"></div>
      <div class="form-group"><label>Compagnia</label><input type="text" name="volo_ritorno_compagnia" value="<?= htmlspecialchars($vr['compagnia'] ?? '') ?>" placeholder="es. Lufthansa"></div>
    </div>
    <div class="form-grid" style="margin-bottom:16px;">
      <div class="form-group"><label>Aeroporto Partenza</label><input type="text" name="volo_ritorno_partenza" value="<?= htmlspecialchars($vr['partenza_aeroporto'] ?? '') ?>" placeholder="es. San Francisco (SFO)"></div>
      <div class="form-group"><label>Aeroporto Arrivo</label><input type="text" name="volo_ritorno_arrivo" value="<?= htmlspecialchars($vr['arrivo_aeroporto'] ?? '') ?>" placeholder="es. Milano Malpensa (MXP)"></div>
    </div>
    <div class="form-grid" style="margin-bottom:16px;">
      <div class="form-group"><label>Numero Volo</label><input type="text" name="volo_ritorno_numero" value="<?= htmlspecialchars($vr['numero_volo'] ?? '') ?>" placeholder="es. LH 456"></div>
      <div class="form-group"><label>Orario Partenza</label><input type="text" name="volo_ritorno_orario_partenza" value="<?= htmlspecialchars($vr['orario_partenza'] ?? '') ?>" placeholder="es. 16:20"></div>
    </div>
    <div class="form-group" style="margin-bottom:24px;"><label>Scalo (opzionale)</label><input type="text" name="volo_ritorno_scalo" value="<?= htmlspecialchars($vr['scalo'] ?? '') ?>" placeholder="es. Frankfurt (FRA) — 1h 45min layover"></div>
  </div>
</div><!-- /tab-volo -->

<!-- ══ TAB: HOTEL ════════════════════════════════════════════════ -->
<div class="tab-panel" id="tab-hotel">
  <h3 class="section-title">Alloggi</h3>
  <div id="hotels-rows">
    <?php foreach ($hotels_data as $hi => $h): ?>
    <div class="hotel-admin-row" style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px;margin-bottom:12px;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <strong style="font-size:14px;color:#000744;">Hotel <?= $hi + 1 ?></strong>
        <button type="button" class="btn-icon btn-danger-icon" onclick="this.closest('.hotel-admin-row').remove()" title="Rimuovi"><i class="fa-solid fa-trash"></i></button>
      </div>
      <div class="form-grid" style="margin-bottom:12px;">
        <div class="form-group"><label>Città</label><input type="text" name="hotel_citta[]" value="<?= htmlspecialchars($h['citta'] ?? '') ?>" placeholder="es. Los Angeles"></div>
        <div class="form-group"><label>Nome Hotel</label><input type="text" name="hotel_nome[]" value="<?= htmlspecialchars($h['nome'] ?? '') ?>" placeholder="es. Hotel Santa Monica"></div>
      </div>
      <div class="form-grid" style="margin-bottom:12px;">
        <div class="form-group"><label>Stelle</label><select name="hotel_stelle[]"><?php for($s=1;$s<=5;$s++): ?><option value="<?= $s ?>" <?= ($h['stelle']??4)==$s?'selected':'' ?>><?= $s ?> ★</option><?php endfor; ?></select></div>
        <div class="form-group"><label>Notti</label><input type="number" name="hotel_notti[]" min="1" value="<?= (int)($h['notti'] ?? 1) ?>"></div>
      </div>
      <div class="form-group" style="margin-bottom:12px;"><label>Indirizzo</label><input type="text" name="hotel_indirizzo[]" value="<?= htmlspecialchars($h['indirizzo'] ?? '') ?>" placeholder="es. Santa Monica, CA"></div>
      <div class="form-group" style="margin-bottom:12px;"><label>Descrizione</label><textarea name="hotel_descrizione[]" rows="2"><?= htmlspecialchars($h['descrizione'] ?? '') ?></textarea></div>
      <div class="form-group" style="margin-bottom:12px;"><label>Foto URL</label><input type="url" name="hotel_foto[]" value="<?= htmlspecialchars($h['image_url'] ?? '') ?>" placeholder="https://..."></div>
      <div class="form-group" style="margin-bottom:0;"><label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:400;"><input type="checkbox" name="hotel_colazione_<?= $hi ?>[]" value="1" <?= !empty($h['inclusa_colazione'])?'checked':'' ?>> Colazione inclusa</label></div>
    </div>
    <?php endforeach; ?>
  </div>
  <button type="button" class="btn-small" onclick="addHotelRow()" style="margin-top:12px;padding:8px 16px;">
    <i class="fa-solid fa-plus"></i> Aggiungi Hotel
  </button>
</div><!-- /tab-hotel -->

        <style>
        .room-pill {
          padding: 8px 18px;
          border: 2px solid #ccc;
          border-radius: 20px;
          background: white;
          color: #666;
          font-weight: 600;
          cursor: pointer;
          transition: all .15s;
        }
        .room-pill.active {
          background: #000744;
          border-color: #000744;
          color: white;
        }
        .toggle-switch { position:relative; display:inline-block; width:44px; height:24px; }
        .toggle-switch input { opacity:0; width:0; height:0; }
        .toggle-slider {
          position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0;
          background:#ccc; border-radius:24px; transition:.3s;
        }
        .toggle-slider:before {
          position:absolute; content:''; height:18px; width:18px;
          left:3px; bottom:3px; background:white; border-radius:50%; transition:.3s;
        }
        .toggle-switch input:checked + .toggle-slider { background:#000744; }
        .toggle-switch input:checked + .toggle-slider:before { transform:translateX(20px); }
        .bracket-row input[type="number"] { padding: 7px 10px; border: 1px solid var(--border); border-radius: var(--radius); font-size: 13px; font-family: inherit; color: var(--text); background: var(--white); }
        .bracket-row input[type="number"]:focus { outline: none; border-color: #000744; box-shadow: 0 0 0 3px rgba(0,7,68,0.1); }
        .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        </style>

    </div><!-- /edit-container -->

    <!-- ── Sticky save footer ── -->
    <div class="sticky-footer">
        <div class="footer-title-preview">
            <?= $is_new ? '<em>Nuovo viaggio</em>' : htmlspecialchars($trip['title']) ?>
        </div>
        <div class="footer-actions">
                <button type="button" class="btn-preview" onclick="openPreview()" <?= $is_new ? 'disabled' : '' ?>>
                <i class="fa-solid fa-eye"></i> Anteprima
            </button>
            <button type="button" class="btn-draft" onclick="submitWithFormConfig('draft')">
                <i class="fa-regular fa-floppy-disk"></i> Salva Bozza
            </button>
            <button type="button" class="btn-publish" onclick="submitWithFormConfig('publish')">
                <i class="fa-solid fa-rocket"></i> Pubblica
            </button>
        </div>
    </div>

    </form>
</div><!-- /edit-page -->

<script>
// ─────────────────────────────────────────────────────────────────────────────
// JS constants from PHP
// ─────────────────────────────────────────────────────────────────────────────
const slugLocked = <?= json_encode($slug_locked) ?>;
const isNew      = <?= json_encode($is_new) ?>;
let previewToken = '<?= htmlspecialchars($preview_token_val) ?>';
const tripSlug   = '<?= htmlspecialchars($trip['slug'] ?? '') ?>';
const currentFormConfig = <?= json_encode($trip['form_config'] ?? [], JSON_UNESCAPED_UNICODE) ?>;

// ─────────────────────────────────────────────────────────────────────────────
// Tab switching (with localStorage persistence)
// ─────────────────────────────────────────────────────────────────────────────
function switchTab(tabId) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    const panel = document.getElementById('tab-' + tabId);
    if (panel) panel.classList.add('active');
    document.querySelectorAll('.tab-btn[data-tab="' + tabId + '"]').forEach(b => b.classList.add('active'));
    try { localStorage.setItem('edit_trip_tab', tabId); } catch(e) {}
    const f = document.getElementById('active_tab_field'); if (f) f.value = tabId;
}

document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => switchTab(btn.dataset.tab));
});

// Restore last active tab (URL param ?tab= takes priority over localStorage)
(function() {
    const validTabs = ['info', 'media', 'content', 'itinerario', 'formconfig', 'accompagnatore', 'volo', 'hotel'];
    const urlTab = new URLSearchParams(window.location.search).get('tab');
    let last = 'info';
    try { last = localStorage.getItem('edit_trip_tab') || 'info'; } catch(e) {}
    if (urlTab && validTabs.includes(urlTab)) last = urlTab;
    if (!validTabs.includes(last)) last = 'info';
    switchTab(last);
})();

// ─────────────────────────────────────────────────────────────────────────────
// Slug auto-generation
// ─────────────────────────────────────────────────────────────────────────────
function generateSlug(title) {
    return title.toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9\s-]/g, '').trim().replace(/\s+/g, '-')
        .replace(/-+/g, '-');
}

const titleInput = document.getElementById('title');
const slugInput  = document.getElementById('slug');
const slugPreview = document.getElementById('slug-preview');

if (titleInput && slugInput && !slugLocked) {
    titleInput.addEventListener('blur', function() {
        if (slugInput.value.trim() === '' && this.value.trim() !== '') {
            const s = generateSlug(this.value.trim());
            slugInput.value = s;
            if (slugPreview) slugPreview.textContent = s;
        }
    });
    slugInput.addEventListener('input', function() {
        if (slugPreview) slugPreview.textContent = this.value;
    });
}

// ─────────────────────────────────────────────────────────────────────────────
// Duration auto-calculation
// ─────────────────────────────────────────────────────────────────────────────
function updateDuration() {
    const start = document.getElementById('start_date').value;
    const end   = document.getElementById('end_date').value;
    const display = document.getElementById('duration-display');
    const hidden  = document.getElementById('duration-input');
    if (start && end) {
        const diff = Math.round((new Date(end) - new Date(start)) / (1000*60*60*24)) + 1;
        if (diff > 0) {
            const text = diff + (diff === 1 ? ' giorno' : ' giorni');
            display.textContent = text;
            hidden.value = text;
            return;
        }
    }
    display.textContent = '—';
    hidden.value = '';
}
document.getElementById('start_date').addEventListener('change', updateDuration);
document.getElementById('end_date').addEventListener('change', updateDuration);

// ─────────────────────────────────────────────────────────────────────────────
// Hero image preview
// ─────────────────────────────────────────────────────────────────────────────
function updateHeroPreview(url) {
    const img = document.getElementById('hero-preview');
    if (!img) return;
    if (url && url.trim()) {
        img.src = url.trim();
        img.style.display = 'block';
    } else {
        img.src = '';
        img.style.display = 'none';
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Gallery URL preview grid
// ─────────────────────────────────────────────────────────────────────────────
function updateGalleryPreview(value) {
    const grid = document.getElementById('gallery-grid');
    if (!grid) return;
    grid.innerHTML = '';
    const urls = value.split('\n').map(u => u.trim()).filter(Boolean);
    urls.forEach(url => {
        const div = document.createElement('div');
        div.className = 'gallery-thumb';
        div.dataset.url = url;
        div.innerHTML = `
            <img src="${escHtml(url)}" alt="gallery" loading="lazy">
            <button type="button" class="gallery-thumb-remove" onclick="removeGalleryUrl(this)" title="Rimuovi"><i class="fa-solid fa-xmark"></i></button>`;
        grid.appendChild(div);
    });
}

function removeGalleryUrl(btn) {
    const thumb = btn.closest('.gallery-thumb');
    const url   = thumb.dataset.url;
    const ta    = document.getElementById('gallery');
    const lines = ta.value.split('\n').filter(l => l.trim() !== url.trim());
    ta.value = lines.join('\n');
    thumb.remove();
}

function escHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// ─────────────────────────────────────────────────────────────────────────────
// Short description char counter
// ─────────────────────────────────────────────────────────────────────────────
function updateShortDescCounter(ta) {
    const remaining = 160 - ta.value.length;
    const el = document.getElementById('short-desc-remaining');
    const counter = document.getElementById('short-desc-counter');
    if (el) el.textContent = remaining;
    if (counter) {
        counter.classList.toggle('warning', remaining < 20);
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Tags chip selector
// ─────────────────────────────────────────────────────────────────────────────
function getSelectedTags() {
    return [...document.querySelectorAll('.tag-pill.selected')].map(p => p.dataset.slug);
}

function updateTagsJson() {
    document.getElementById('tags-json').value = JSON.stringify(getSelectedTags());
}

function toggleTag(btn) {
    btn.classList.toggle('selected');
    updateTagsJson();
}

function addCustomTag() {
    const input = document.getElementById('custom-tag-input');
    const label = input.value.trim();
    if (!label) return;
    const slug = generateSlug(label);
    if (!slug) { input.value = ''; return; }

    // Check if already exists
    const existing = document.querySelector('.tag-pill[data-slug="' + slug + '"]');
    if (existing) {
        existing.classList.add('selected');
        updateTagsJson();
        input.value = '';
        return;
    }

    // Create new pill in a custom category section
    let customSection = document.getElementById('tag-pills-custom');
    if (!customSection) {
        const wrap = document.createElement('div');
        wrap.innerHTML = '<div class="tag-category-label">Personalizzati</div><div class="tag-pills" id="tag-pills-custom"></div>';
        document.querySelector('.tags-section').insertBefore(wrap, document.querySelector('.custom-tag-row'));
        customSection = document.getElementById('tag-pills-custom');
    }

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'tag-pill selected';
    btn.dataset.slug = slug;
    btn.textContent = label;
    btn.onclick = function() { toggleTag(this); };
    customSection.appendChild(btn);
    updateTagsJson();
    input.value = '';
}

// ─────────────────────────────────────────────────────────────────────────────
// Token regeneration
// ─────────────────────────────────────────────────────────────────────────────
function regenToken() {
    regenerateToken();
}

function regenerateToken() {
    if (!confirm('Rigenera token? I link di anteprima precedenti non funzioneranno più.')) return;
    fetch(window.location.href, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({action: 'regenerate_token', slug: tripSlug})
    }).then(r => r.json()).then(data => {
        if (data.success) {
            previewToken = data.token;
            const display = document.getElementById('token-display');
            if (display) display.textContent = data.token.substring(0, 16) + '...';
        } else {
            alert('Errore durante la rigenerazione del token.');
        }
    }).catch(() => alert('Errore di rete.'));
}

// ─────────────────────────────────────────────────────────────────────────────
// Itinerary drag-and-drop
// ─────────────────────────────────────────────────────────────────────────────
let dragSrc = null;

function initDrag() {
    document.querySelectorAll('#itinerary-rows .itinerary-row').forEach(row => {
        row.addEventListener('dragstart', e => {
            dragSrc = row;
            row.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });
        row.addEventListener('dragend', () => row.classList.remove('dragging'));
        row.addEventListener('dragover', e => { e.preventDefault(); e.dataTransfer.dropEffect = 'move'; });
        row.addEventListener('drop', e => {
            e.preventDefault();
            if (dragSrc && dragSrc !== row) {
                const container = row.parentNode;
                const rows = [...container.querySelectorAll('.itinerary-row')];
                const srcIdx = rows.indexOf(dragSrc);
                const tgtIdx = rows.indexOf(row);
                container.insertBefore(dragSrc, srcIdx < tgtIdx ? row.nextSibling : row);
                renumberItinerary();
            }
        });
    });
}

function renumberItinerary() {
    document.querySelectorAll('#itinerary-rows .itinerary-row .day-num').forEach((el, i) => {
        el.textContent = i + 1;
    });
}

function moveRow(btn, dir) {
    const row = btn.closest('.itinerary-row');
    const container = row.parentNode;
    if (dir === -1 && row.previousElementSibling) container.insertBefore(row, row.previousElementSibling);
    if (dir === 1 && row.nextElementSibling) container.insertBefore(row.nextElementSibling, row);
    renumberItinerary();
}

function removeRow(btn) {
    btn.closest('.itinerary-row').remove();
    renumberItinerary();
}

function addItineraryRow() {
    const container = document.getElementById('itinerary-rows');
    const n = container.querySelectorAll('.itinerary-row').length + 1;
    const div = document.createElement('div');
    div.className = 'itinerary-row';
    div.setAttribute('draggable', 'true');
    div.innerHTML = `
        <span class="drag-handle"><i class="fa-solid fa-grip-vertical"></i></span>
        <span class="day-num">${n}</span>
        <div class="itinerary-fields">
            <input type="text" name="itinerary_title[]" placeholder="Titolo giorno">
            <input type="text" name="itinerary_location[]" placeholder="Città / Luogo (es. Las Vegas, NV)">
            <input type="text" name="itinerary_date[]" placeholder="Data (es. 5 Aprile 2026)">
            <input type="url" name="itinerary_image[]" placeholder="URL Immagine giorno">
            <textarea name="itinerary_desc[]" rows="3" placeholder="Descrizione..."></textarea>
        </div>
        <div class="itinerary-actions">
            <button type="button" class="btn-icon" onclick="moveRow(this,-1)" title="Su"><i class="fa-solid fa-chevron-up"></i></button>
            <button type="button" class="btn-icon" onclick="moveRow(this,1)" title="Giu"><i class="fa-solid fa-chevron-down"></i></button>
            <button type="button" class="btn-icon btn-danger-icon" onclick="removeRow(this)" title="Elimina"><i class="fa-solid fa-trash"></i></button>
        </div>`;
    container.appendChild(div);
    initDrag(); // re-bind drag events to new row
}

// ─────────────────────────────────────────────────────────────────────────────
// Hotel admin rows
// ─────────────────────────────────────────────────────────────────────────────
function addHotelRow() {
    var hi = document.getElementById('hotels-rows').querySelectorAll('.hotel-admin-row').length;
    var div = document.createElement('div');
    div.className = 'hotel-admin-row';
    div.style.cssText = 'background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px;margin-bottom:12px;';
    div.innerHTML =
        '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;"><strong style="font-size:14px;color:#000744;">Hotel ' + (hi + 1) + '</strong><button type="button" class="btn-icon btn-danger-icon" onclick="this.closest(\'.hotel-admin-row\').remove()" title="Rimuovi"><i class="fa-solid fa-trash"></i></button></div>'
      + '<div class="form-grid" style="margin-bottom:12px;"><div class="form-group"><label>Città</label><input type="text" name="hotel_citta[]" placeholder="es. Roma"></div><div class="form-group"><label>Nome Hotel</label><input type="text" name="hotel_nome[]" placeholder="es. Hotel De Russie"></div></div>'
      + '<div class="form-grid" style="margin-bottom:12px;"><div class="form-group"><label>Stelle</label><select name="hotel_stelle[]"><option value="1">1 ★</option><option value="2">2 ★</option><option value="3">3 ★</option><option value="4" selected>4 ★</option><option value="5">5 ★</option></select></div><div class="form-group"><label>Notti</label><input type="number" name="hotel_notti[]" min="1" value="1"></div></div>'
      + '<div class="form-group" style="margin-bottom:12px;"><label>Indirizzo</label><input type="text" name="hotel_indirizzo[]" placeholder="es. Via Veneto, Roma"></div>'
      + '<div class="form-group" style="margin-bottom:12px;"><label>Descrizione</label><textarea name="hotel_descrizione[]" rows="2"></textarea></div>'
      + '<div class="form-group" style="margin-bottom:12px;"><label>Foto URL</label><input type="url" name="hotel_foto[]" placeholder="https://..."></div>'
      + '<div class="form-group" style="margin-bottom:0;"><label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:400;"><input type="checkbox" name="hotel_colazione_new[]" value="1"> Colazione inclusa</label></div>';
    document.getElementById('hotels-rows').appendChild(div);
}

// ─────────────────────────────────────────────────────────────────────────────
// Preview URL opener
// ─────────────────────────────────────────────────────────────────────────────
function openPreview() {
    if (!tripSlug || !previewToken) { alert('Salva il viaggio prima di visualizzare l\'anteprima.'); return; }
    window.open('/viaggio.php?slug=' + tripSlug + '&preview=' + previewToken, '_blank');
}

// ─────────────────────────────────────────────────────────────────────────────
// Form Config — AI suggests numeric params, user saves via individual fields
// ─────────────────────────────────────────────────────────────────────────────
function generateAI() {
    const desc = document.getElementById('ai_description').value.trim();
    if (!desc) { alert('Inserisci una descrizione del viaggio.'); return; }
    document.getElementById('ai-loading').style.display = 'inline';
    document.getElementById('btn-generate-ai').disabled = true;
    fetch('/api/generate-form.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({description: desc})
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('ai-loading').style.display = 'none';
        document.getElementById('btn-generate-ai').disabled = false;
        if (data.params) {
            const p = data.params;
            const set = (id, val) => { if (val !== undefined && document.getElementById(id)) document.getElementById(id).value = val; };
            set('fc-prezzo-adulto',   p.prezzo_adulto);
            set('fc-suppl-singola',   p.supplemento_singola);
            set('fc-terzo-letto',     p.prezzo_terzo_letto);
            set('fc-quarto-letto',    p.prezzo_quarto_letto);
            set('fc-concorrenza-pp',  p.prezzo_concorrenza_per_persona);
            set('fc-concorrenza-34',  p.prezzo_terzo_quarto_concorrenza);
            set('fc-assicurazione',   p.percentuale_assicurazione);
            alert('Parametri suggeriti caricati. Verifica i valori e clicca "Salva Parametri".');
        } else {
            alert('Errore nella generazione. Riprova.');
        }
    })
    .catch(() => {
        document.getElementById('ai-loading').style.display = 'none';
        document.getElementById('btn-generate-ai').disabled = false;
        alert('Errore di connessione.');
    });
}

// ─── Form Config helpers ─────────────────────────────────────────────────────
let activeRoomTypes = <?= json_encode($fc_room_types ?? []) ?>;

function toggleRoom(btn) {
    const room = btn.dataset.room;
    const panel = document.getElementById('rp-' + room);
    const willActivate = !btn.classList.contains('active');
    if (willActivate) {
        btn.classList.add('active');
        if (!activeRoomTypes.includes(room)) activeRoomTypes.push(room);
        activeRoomTypes.sort();
        if (panel) panel.style.display = 'block';
    } else {
        btn.classList.remove('active');
        activeRoomTypes = activeRoomTypes.filter(r => r !== room);
        if (panel) panel.style.display = 'none';
    }
}

// Re-apply panel visibility on load based on activeRoomTypes
document.addEventListener('DOMContentLoaded', function() {
    ['X1','X2','X3','X4','X5'].forEach(function(room) {
        const btn   = document.querySelector('.room-pill[data-room="' + room + '"]');
        const panel = document.getElementById('rp-' + room);
        const isActive = activeRoomTypes.includes(room);
        if (btn) { btn.classList.toggle('active', isActive); }
        if (panel) { panel.style.display = isActive ? 'block' : 'none'; }
    });
});

function toggleSection(panelId, show) {
    const el = document.getElementById(panelId);
    if (el) el.style.display = show ? 'block' : 'none';
}

function addBracket() {
    const list = document.getElementById('fc-brackets-list');
    const row = document.createElement('div');
    row.className = 'bracket-row';
    row.style.cssText = 'display:flex;gap:10px;align-items:center;margin-bottom:8px;';
    row.innerHTML = '<input type="number" class="br-min" min="0" max="7" placeholder="Min" style="width:70px;">'
        + '<span>–</span>'
        + '<input type="number" class="br-max" min="0" max="7" placeholder="Max" style="width:70px;">'
        + '<span>anni: sconto</span>'
        + '<input type="number" class="br-discount" min="0" placeholder="€" style="width:90px;">'
        + '<button type="button" onclick="this.parentElement.remove()" style="color:#cc0031;background:none;border:none;cursor:pointer;font-size:1.2rem;">×</button>';
    list.appendChild(row);
}

// SHA-256 hash preview for agency code
(function() {
    const plain = document.getElementById('fc-agency-plain');
    const hashField = document.getElementById('fc-agency-hash');
    const preview = document.getElementById('fc-hash-preview');
    if (!plain) return;
    plain.addEventListener('input', async function() {
        const val = plain.value;
        if (!val) { if (preview) preview.textContent = ''; return; }
        const buf = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(val));
        const hex = Array.from(new Uint8Array(buf)).map(b => b.toString(16).padStart(2,'0')).join('');
        if (hashField) hashField.value = hex;
        if (preview) preview.textContent = 'Hash: ' + hex;
    });
})();

function saveFormConfig() {
    // Collect brackets
    const brackets = [];
    document.querySelectorAll('#fc-brackets-list .bracket-row').forEach(row => {
        const min = parseInt(row.querySelector('.br-min').value);
        const max = parseInt(row.querySelector('.br-max').value);
        const disc = parseInt(row.querySelector('.br-discount').value);
        if (!isNaN(min) && !isNaN(max) && !isNaN(disc)) brackets.push({min_age:min,max_age:max,discount:disc});
    });

    const val = (id, fallback) => { const el = document.getElementById(id); return el ? el.value : fallback; };
    const checked = id => { const el = document.getElementById(id); return el && el.checked ? '1' : '0'; };

    fetch(window.location.href, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            action:                        'save_form_config',
            slug:                          tripSlug,
            webhook_url:                   val('fc-webhook','').trim(),
            prezzo_base_persona:           val('fc-prezzo-base',0),
            room_types:                    JSON.stringify(activeRoomTypes),
            supplemento_singola:           val('fc-suppl-singola',0),
            sconto_terzo_letto:            val('fc-sconto-3',0),
            sconto_quarto_letto:           val('fc-sconto-4',0),
            sconto_quinto_letto:           val('fc-sconto-5',0),
            child_discounts_enabled:       checked('fc-child-enabled'),
            child_discount_brackets:       JSON.stringify(brackets),
            insurance_enabled:             checked('fc-insurance-enabled'),
            percentuale_assicurazione:     val('fc-assicurazione',5),
            competitor_enabled:            checked('fc-competitor-enabled'),
            prezzo_concorrenza_persona:    val('fc-concorrenza-pp',0),
            prezzo_concorrenza_letti_extra:val('fc-concorrenza-extra',0),
            agency_code_hash:              val('fc-agency-hash','').trim(),
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.href = window.location.pathname + '?slug=' + tripSlug + '&saved=1&tab=formconfig';
        }
    });
}

// ─────────────────────────────────────────────────────────────────────────────
// Init on load
// ─────────────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    initDrag();
    renumberItinerary();
    // Init gallery grid if values present
    const galleryTa = document.getElementById('gallery');
    if (galleryTa && galleryTa.value.trim()) {
        updateGalleryPreview(galleryTa.value);
    }
    // Init short desc counter
    const sdTa = document.getElementById('short_description');
    if (sdTa) updateShortDescCounter(sdTa);
    // Init hero preview
    const heroInput = document.getElementById('hero_image');
    if (heroInput && heroInput.value.trim()) {
        updateHeroPreview(heroInput.value.trim());
    }
});

// ─────────────────────────────────────────────────────────────────────────────
// Submit with optional form config pre-save
// ─────────────────────────────────────────────────────────────────────────────
function submitWithFormConfig(action) {
    document.getElementById('form-action-hidden').value = action;
    var activeTab = 'info';
    try { activeTab = localStorage.getItem('edit_trip_tab') || 'info'; } catch(e) {}
    if (activeTab !== 'formconfig') {
        document.getElementById('edit-form').submit();
        return;
    }
    // Active tab is formconfig: AJAX save first, then submit
    var brackets = [];
    document.querySelectorAll('#fc-brackets-list .bracket-row').forEach(function(row) {
        var min  = parseInt(row.querySelector('.br-min').value);
        var max  = parseInt(row.querySelector('.br-max').value);
        var disc = parseInt(row.querySelector('.br-discount').value);
        if (!isNaN(min) && !isNaN(max) && !isNaN(disc)) brackets.push({min_age:min,max_age:max,discount:disc});
    });
    var val = function(id, fallback) { var el = document.getElementById(id); return el ? el.value : fallback; };
    var checked = function(id) { var el = document.getElementById(id); return el && el.checked ? '1' : '0'; };
    fetch(window.location.href, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            action:                         'save_form_config',
            slug:                           tripSlug,
            webhook_url:                    val('fc-webhook','').trim(),
            prezzo_base_persona:            val('fc-prezzo-base',0),
            room_types:                     JSON.stringify(activeRoomTypes),
            supplemento_singola:            val('fc-suppl-singola',0),
            sconto_terzo_letto:             val('fc-sconto-3',0),
            sconto_quarto_letto:            val('fc-sconto-4',0),
            sconto_quinto_letto:            val('fc-sconto-5',0),
            child_discounts_enabled:        checked('fc-child-enabled'),
            child_discount_brackets:        JSON.stringify(brackets),
            insurance_enabled:              checked('fc-insurance-enabled'),
            percentuale_assicurazione:      val('fc-assicurazione',5),
            competitor_enabled:             checked('fc-competitor-enabled'),
            prezzo_concorrenza_persona:     val('fc-concorrenza-pp',0),
            prezzo_concorrenza_letti_extra: val('fc-concorrenza-extra',0),
            agency_code_hash:               val('fc-agency-hash','').trim(),
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            document.getElementById('edit-form').submit();
        } else {
            alert('Errore nel salvataggio del Form Config. Riprova.');
        }
    })
    .catch(function() {
        alert('Errore di rete durante il salvataggio del Form Config.');
    });
}
</script>

</body>
</html>
