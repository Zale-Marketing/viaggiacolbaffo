<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../includes/config.php';
require_once ROOT . '/includes/functions.php';

// Sanitize and collect all POST fields
$data = [];
$allowed_keys = [
    'trip_slug','trip_title','nome','cognome','email','telefono',
    'tipo_cliente','adulti','bambini','room_type','note',
    'nome_agenzia','codice_iata','citta','commissione'
];
foreach ($allowed_keys as $key) {
    if (isset($_POST[$key])) {
        $data[$key] = htmlspecialchars(strip_tags(trim($_POST[$key])));
    }
}
// Array fields
if (!empty($_POST['eta_bambini'])) {
    $data['eta_bambini'] = array_map('intval', (array)$_POST['eta_bambini']);
}
if (!empty($_POST['addons'])) {
    $data['addons'] = array_map('htmlspecialchars', (array)$_POST['addons']);
}

// Determine webhook URL: per-trip first, then default
$webhook_url = '';
$slug = $data['trip_slug'] ?? '';
if ($slug) {
    $trip = get_trip_by_slug($slug);
    if ($trip && !empty($trip['webhook_url'])) {
        $webhook_url = $trip['webhook_url'];
    }
}
if (!$webhook_url) {
    $webhook_url = DEFAULT_WEBHOOK_URL;
}

// No webhook configured — graceful degradation
if (!$webhook_url) {
    echo json_encode(['success' => true, 'note' => 'no_webhook']);
    exit;
}

// Forward via cURL
$payload = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$ch = curl_init($webhook_url);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload),
    ],
]);
$response  = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_err  = curl_error($ch);
curl_close($ch);

if ($curl_err || $http_code < 200 || $http_code >= 300) {
    echo json_encode(['success' => false, 'error' => 'Errore di invio. Riprova o contattaci su WhatsApp.']);
    exit;
}

echo json_encode(['success' => true]);
