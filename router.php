<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');

// Static files - serve directly
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot|pdf|webp|map)$/i', $uri)) {
    return false;
}

// Existing .php files - serve directly
if (preg_match('/\.php$/i', $uri) && file_exists(__DIR__ . '/' . $uri)) {
    return false;
}

// Dynamic routes
if (preg_match('#^viaggio/([a-z0-9-]+)$#', $uri, $m)) {
    $_GET['slug'] = $m[1];
    if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']) {
        parse_str($_SERVER['QUERY_STRING'], $qs);
        $_GET = array_merge($_GET, $qs);
    }
    require __DIR__ . '/viaggio.php';
    exit;
}
if (preg_match('#^destinazione/([a-z0-9-]+)$#', $uri, $m)) {
    $_GET['slug'] = $m[1];
    require __DIR__ . '/destinazione.php';
    exit;
}

// Static routes
$routes = [
    '' => 'index.php',
    'viaggi' => 'viaggi.php',
    'agenzie' => 'agenzie.php',
    'destinazioni' => 'destinazioni.php',
    'admin' => 'admin/index.php',
    'admin/login' => 'admin/login.php',
    'admin/dashboard' => 'admin/dashboard.php',
];

if (isset($routes[$uri])) {
    require __DIR__ . '/' . $routes[$uri];
    exit;
}

// Existing files/directories
if (file_exists(__DIR__ . '/' . $uri)) {
    return false;
}

// 404
http_response_code(404);
if (file_exists(__DIR__ . '/404.php')) {
    require __DIR__ . '/404.php';
} else {
    echo '<h1>404 - Pagina non trovata</h1>';
}
