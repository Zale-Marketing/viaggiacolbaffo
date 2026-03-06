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
if (!OPENAI_API_KEY) {
    echo json_encode([
        'success'     => true,
        'form_config' => default_form_config($description),
        'source'      => 'default',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// System prompt for GPT-4o-mini
$system_prompt = <<<PROMPT
Sei un assistente per l'operatore turistico "Viaggia col Baffo".
Data una descrizione di un viaggio in italiano, genera un oggetto JSON "form_config" per il modulo preventivo online.

L'oggetto DEVE rispettare esattamente questa struttura:
{
  "price_per_person": <intero — prezzo base a persona in camera doppia>,
  "single_supplement": <intero — supplemento singola>,
  "third_bed_price": <intero negativo — sconto terzo letto>,
  "fourth_bed_price": <intero negativo — sconto quarto letto>,
  "competitor_benchmark": <intero — prezzo medio di mercato per tour simili, usato per mostrare il risparmio>,
  "room_types": [
    {"slug": "doppia",  "label": "Camera Doppia",  "price_delta": 0},
    {"slug": "singola", "label": "Camera Singola", "price_delta": <supplemento>},
    {"slug": "tripla",  "label": "Camera Tripla",  "price_delta": <sconto negativo>}
  ],
  "addons": [
    {"slug": "assicurazione", "label": "Assicurazione viaggio completa", "price": 180},
    <altri optional rilevanti per il viaggio con price in euro interi>
  ],
  "fields": ["nome","cognome","email","telefono","tipo_cliente","numero_partecipanti","room_type","note"]
}

Rispondi SOLO con il JSON valido, senza commenti né markdown.
PROMPT;

// Call GPT-4o-mini
$payload = json_encode([
    'model'           => 'gpt-4o-mini',
    'messages'        => [
        ['role' => 'system', 'content' => $system_prompt],
        ['role' => 'user',   'content' => $description],
    ],
    'temperature'     => 0.3,
    'response_format' => ['type' => 'json_object'],
], JSON_UNESCAPED_UNICODE);

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . OPENAI_API_KEY,
    ],
]);
$result    = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_err  = curl_error($ch);
curl_close($ch);

if ($curl_err || $http_code !== 200) {
    // Fallback to defaults on API error
    echo json_encode([
        'success'     => true,
        'form_config' => default_form_config($description),
        'source'      => 'default_fallback',
        'api_error'   => $curl_err ?: "HTTP $http_code",
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$body        = json_decode($result, true);
$content     = $body['choices'][0]['message']['content'] ?? '';
$form_config = json_decode($content, true);

if (!$form_config || !isset($form_config['price_per_person'])) {
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
