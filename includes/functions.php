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
