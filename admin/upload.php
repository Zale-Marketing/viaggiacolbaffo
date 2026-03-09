<?php
/**
 * admin/upload.php — Image upload endpoint
 */
session_start();
if (empty($_SESSION['admin'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non autorizzato']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$upload_dir = __DIR__ . '/../uploads/';

if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) {
        echo json_encode(['success' => false, 'error' => 'Impossibile creare la cartella uploads']);
        exit;
    }
    file_put_contents($upload_dir . '.htaccess', "php_flag engine off\nOptions -ExecCGI\n");
}

if (!isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'error' => 'Nessun file ricevuto']);
    exit;
}

$file = $_FILES['file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    $errors = [
        UPLOAD_ERR_INI_SIZE   => 'File troppo grande (limite server)',
        UPLOAD_ERR_FORM_SIZE  => 'File troppo grande',
        UPLOAD_ERR_PARTIAL    => 'Upload incompleto',
        UPLOAD_ERR_NO_FILE    => 'Nessun file selezionato',
        UPLOAD_ERR_NO_TMP_DIR => 'Cartella temporanea mancante',
        UPLOAD_ERR_CANT_WRITE => 'Impossibile scrivere sul disco',
    ];
    echo json_encode(['success' => false, 'error' => $errors[$file['error']] ?? 'Errore upload']);
    exit;
}

$allowed_mime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime, $allowed_mime)) {
    echo json_encode(['success' => false, 'error' => 'Formato non supportato. Usa JPG, PNG, WEBP o GIF.']);
    exit;
}

if ($file['size'] > 8 * 1024 * 1024) {
    echo json_encode(['success' => false, 'error' => 'File troppo grande. Massimo 8MB.']);
    exit;
}

$ext = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'][$mime];
$original = pathinfo($file['name'], PATHINFO_FILENAME);
$original = preg_replace('/[^a-zA-Z0-9_-]/', '-', $original);
$original = strtolower(substr($original, 0, 40));
$filename  = $original . '-' . substr(bin2hex(random_bytes(4)), 0, 8) . '.' . $ext;
$dest      = $upload_dir . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    echo json_encode(['success' => false, 'error' => 'Errore nel salvataggio del file']);
    exit;
}

$url = '/uploads/' . $filename;
echo json_encode(['success' => true, 'url' => $url]);
