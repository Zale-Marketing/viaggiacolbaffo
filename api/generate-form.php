<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../includes/config.php';

$input = json_decode(file_get_contents('php://input'), true);
$description = trim($input['description'] ?? '');

if (!$description) {
    echo json_encode(['success' => false, 'error' => 'Descrizione mancante.']);
    exit;
}

function default_form_params(string $description): array {
    preg_match('/€\s?([\d\.]+)/', $description, $m);
    $price = isset($m[1]) ? (int)str_replace('.', '', $m[1]) : 4000;
    return [
        'prezzo_adulto'                   => $price,
        'supplemento_singola'             => (int)round($price * 0.37),
        'prezzo_terzo_letto'              => (int)round($price * 0.69),
        'prezzo_quarto_letto'             => (int)round($price * 0.69),
        'prezzo_concorrenza_per_persona'  => (int)round($price * 1.6),
        'prezzo_terzo_quarto_concorrenza' => (int)round($price * 1.15),
        'percentuale_assicurazione'       => 5,
    ];
}

if (!ANTHROPIC_API_KEY) {
    echo json_encode([
        'success' => true,
        'params'  => default_form_params($description),
        'source'  => 'default',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$system = 'Sei un assistente per operatori turistici italiani. Dato un testo descrittivo di un viaggio, suggerisci i parametri numerici per il form preventivo. Rispondi SOLO con un JSON valido con questi campi esatti: prezzo_adulto (intero €, prezzo per adulto in camera doppia), supplemento_singola (intero €, extra per viaggiatore solo), prezzo_terzo_letto (intero €, prezzo 3° posto letto, può essere negativo = sconto), prezzo_quarto_letto (intero €), prezzo_concorrenza_per_persona (intero €, stima prezzo medio concorrenza), prezzo_terzo_quarto_concorrenza (intero €, prezzo concorrenza per 3°/4° letto), percentuale_assicurazione (numero, tipicamente 5). Nessun testo aggiuntivo, nessun markdown fence.';

$payload = json_encode([
    'model'      => 'claude-sonnet-4-6',
    'max_tokens' => 512,
    'system'     => $system,
    'messages'   => [['role' => 'user', 'content' => $description]],
    'temperature'=> 0.2,
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
        'success' => true,
        'params'  => default_form_params($description),
        'source'  => 'default_fallback',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$body    = json_decode($result, true);
$content = $body['content'][0]['text'] ?? '';
$params  = json_decode($content, true);
if (!is_array($params)) {
    $clean  = preg_replace('/^```[a-z]*\n?|\n?```$/m', '', trim($content));
    $params = json_decode($clean, true);
}
if (!is_array($params)) {
    echo json_encode([
        'success' => true,
        'params'  => default_form_params($description),
        'source'  => 'default_parse_fallback',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

echo json_encode([
    'success' => true,
    'params'  => $params,
    'source'  => 'ai',
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
