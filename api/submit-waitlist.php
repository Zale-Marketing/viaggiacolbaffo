<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../includes/config.php';

// Sanitize and collect POST fields
$allowed = ['nome', 'email', 'telefono', 'destination_slug', 'destination_name'];
$data = [];
foreach ($allowed as $key) {
    if (isset($_POST[$key])) {
        $data[$key] = htmlspecialchars(strip_tags(trim($_POST[$key])));
    }
}

// Read webhook URL — graceful degradation when not configured
$webhook_url = defined('WAITLIST_WEBHOOK_URL') ? WAITLIST_WEBHOOK_URL : '';
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
