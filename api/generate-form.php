<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../includes/config.php';

// Read JSON body
$input = json_decode(file_get_contents('php://input'), true);
$description = trim($input['description'] ?? '');

if (!$description) {
    echo json_encode(['success' => false, 'error' => 'Descrizione mancante.']);
    exit;
}

// Default form_config (no AI key required)
function default_form_config(string $description): array {
    // Extract a rough price hint from the description (look for €X.XXX pattern)
    preg_match('/€\s?([\d\.]+)/', $description, $m);
    $price = isset($m[1]) ? (int)str_replace('.', '', $m[1]) : 3000;

    return [
        'price_per_person'    => $price,
        'single_supplement'   => (int)round($price * 0.18),
        'third_bed_price'     => -(int)round($price * 0.06),
        'fourth_bed_price'    => -(int)round($price * 0.06),
        'competitor_benchmark'=> (int)round($price * 1.2),
        'room_types' => [
            ['slug' => 'doppia',  'label' => 'Camera Doppia',   'price_delta' => 0],
            ['slug' => 'singola', 'label' => 'Camera Singola',  'price_delta' => (int)round($price * 0.18)],
            ['slug' => 'tripla',  'label' => 'Camera Tripla',   'price_delta' => -(int)round($price * 0.06)],
        ],
        'addons' => [
            ['slug' => 'assicurazione', 'label' => 'Assicurazione viaggio completa', 'price' => 180],
        ],
        'fields' => ['nome','cognome','email','telefono','tipo_cliente','numero_partecipanti','room_type','note'],
    ];
}

// No API key — return defaults
if (!ANTHROPIC_API_KEY) {
    echo json_encode([
        'success'     => true,
        'form_config' => default_form_config($description),
        'source'      => 'default',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// Call Anthropic Claude API
$system = 'Sei un assistente per operatori turistici italiani. Dato un testo descrittivo di un viaggio, genera un JSON di configurazione form con questi campi ESATTI: price_per_person (intero €), single_supplement (intero €), third_bed_price (intero €, negativo), fourth_bed_price (intero €, negativo), competitor_benchmark (intero €), room_types (array di {slug, label, price_delta}), addons (array di {slug, label, price}), webhook_url (stringa vuota ""). Rispondi SOLO con il JSON valido, nessun testo aggiuntivo, nessun markdown fence.';

$payload = json_encode([
    'model'      => 'claude-sonnet-4-6',
    'max_tokens' => 1024,
    'system'     => $system,
    'messages'   => [['role' => 'user', 'content' => $description]],
    'temperature'=> 0.3,
], JSON_UNESCAPED_UNICODE);

$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'x-api-key: ' . ANTHROPIC_API_KEY,
        'anthropic-version: 2023-06-01',
    ],
]);
$result    = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200 || !$result) {
    echo json_encode([
        'success'     => true,
        'form_config' => default_form_config($description),
        'source'      => 'default_fallback',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$body    = json_decode($result, true);
$content = $body['content'][0]['text'] ?? '';  // Anthropic response path
$form_config = json_decode($content, true);
if (!is_array($form_config)) {
    // Try stripping any accidental markdown fences
    $clean = preg_replace('/^```[a-z]*\n?|\n?```$/m', '', trim($content));
    $form_config = json_decode($clean, true);
}
if (!is_array($form_config)) {
    echo json_encode([
        'success'     => true,
        'form_config' => default_form_config($description),
        'source'      => 'default_parse_fallback',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

echo json_encode([
    'success'     => true,
    'form_config' => $form_config,
    'source'      => 'ai',
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
