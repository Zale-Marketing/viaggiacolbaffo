<?php
// functions.php — Data access API for all phases
// Requires config.php to be loaded first (for DATA_DIR constant)

function load_trips(): array {
    $file = DATA_DIR . 'trips.json';
    if (!file_exists($file)) return [];
    $json = file_get_contents($file);
    return json_decode($json, true) ?? [];
}

function get_trip_by_slug(string $slug): ?array {
    foreach (load_trips() as $trip) {
        if ($trip['slug'] === $slug) return $trip;
    }
    return null;
}

function get_trips_by_continent(string $continent): array {
    return array_values(array_filter(load_trips(), fn($t) => $t['continent'] === $continent));
}

function get_trips_by_tag(string $tag): array {
    return array_values(array_filter(load_trips(), fn($t) => in_array($tag, $t['tags'] ?? [])));
}

function save_trips(array $trips): bool {
    $file = DATA_DIR . 'trips.json';
    $json = json_encode(array_values($trips), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $fp = fopen($file, 'w');
    if (!$fp) return false;
    flock($fp, LOCK_EX);
    fwrite($fp, $json);
    flock($fp, LOCK_UN);
    fclose($fp);
    return true;
}

function load_tags(): array {
    $file = DATA_DIR . 'tags.json';
    if (!file_exists($file)) return [];
    return json_decode(file_get_contents($file), true) ?? [];
}

function save_tags(array $tags): bool {
    $file = DATA_DIR . 'tags.json';
    $json = json_encode(array_values($tags), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $fp = fopen($file, 'w');
    if (!$fp) return false;
    flock($fp, LOCK_EX);
    fwrite($fp, $json);
    flock($fp, LOCK_UN);
    fclose($fp);
    return true;
}

function load_destinations(): array {
    $file = DATA_DIR . 'destinations.json';
    if (!file_exists($file)) return [];
    return json_decode(file_get_contents($file), true) ?? [];
}

function save_destinations(array $destinations): bool {
    $file = DATA_DIR . 'destinations.json';
    $json = json_encode($destinations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $fp = fopen($file, 'w');
    if (!$fp) return false;
    flock($fp, LOCK_EX);
    fwrite($fp, $json);
    flock($fp, LOCK_UN);
    fclose($fp);
    return true;
}

/**
 * purge_sg_cache()
 * Flushes the SiteGround dynamic cache for the entire site via their REST API.
 * Requires SG_API_TOKEN and SG_SITE_ID to be defined in config.
 * Silently skips if credentials are not set.
 */
function purge_sg_cache(): void {
    $token   = defined('SG_API_TOKEN') ? SG_API_TOKEN : '';
    $site_id = defined('SG_SITE_ID')  ? SG_SITE_ID  : '';
    if ($token === '' || $site_id === '') return;
    $url = 'https://api.siteground.com/v1/projects/' . rawurlencode($site_id) . '/cache';
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST  => 'DELETE',
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 8,
    ]);
    curl_exec($ch);
    curl_close($ch);
}
