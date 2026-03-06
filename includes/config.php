<?php
/**
 * Viaggia Col Baffo — Shared Configuration
 *
 * Admin-config.json overlay pattern:
 *   data/admin-config.json is loaded FIRST, before any define() call.
 *   Each constant uses the JSON value when non-empty, falling back to the
 *   hardcoded default below. This lets the admin panel persist site-wide
 *   settings without editing PHP files.
 *
 * PHP constants cannot be redefined, so the JSON must be read before any
 * define() is executed. DATA_DIR is not yet available here, so we build
 * the path directly from __DIR__.
 */

// ── Admin-config overlay ──────────────────────────────────────────────────────
$_acfg = [];
$_acfg_file = __DIR__ . '/../data/admin-config.json';
if (file_exists($_acfg_file)) {
    $_acfg = json_decode(file_get_contents($_acfg_file), true) ?? [];
}

// ── Constants (admin-config.json values take precedence when non-empty) ───────

// SECURITY: Change this password before go-live.
define('ADMIN_PASSWORD',       $_acfg['admin_password']       ?: 'Admin2025!');

// Lorenzo's WhatsApp number (include country code, e.g. +39 333 1234567)
// Used in the footer and quote form CTAs.
define('WHATSAPP_NUMBER',      $_acfg['whatsapp_number']       ?: '+39 XXX XXXXXXX');

// Tally form URLs — fill these in when your Tally forms are created.
// TALLY_CATALOG_URL: the custom-request form shown when no trips match catalog filters.
// TALLY_B2B_URL: the agency registration form on the B2B page.
define('TALLY_CATALOG_URL',    $_acfg['tally_catalog_url']    ?? '');
define('TALLY_B2B_URL',        $_acfg['tally_b2b_url']        ?? '');

// Anthropic API key — leave empty to disable the AI form generator (Phase 4).
// Fill in when you want to enable AI-powered form generation in the admin panel.
define('ANTHROPIC_API_KEY',    $_acfg['anthropic_api_key']    ?? '');

// Default webhook URL for quote-form submissions.
// Can be overridden per-trip via the 'webhook_url' field in form_config.
define('DEFAULT_WEBHOOK_URL',  $_acfg['default_webhook_url']  ?? '');

// Absolute path to the project root (the directory containing this includes/ folder).
// Always use ROOT and DATA_DIR in PHP — never relative paths.
define('ROOT',     __DIR__ . '/..');

// Absolute path to the data directory.
define('DATA_DIR', ROOT . '/data/');

// Phase 5: Destinations + B2B
define('WAITLIST_WEBHOOK_URL', $_acfg['waitlist_webhook_url'] ?? '');    // POST target for destination waitlist form
define('WHATSAPP_B2B_FALLBACK',$_acfg['b2b_webhook_url']     ?? '');    // WhatsApp link when TALLY_B2B_URL is empty

// ── Cleanup: do not pollute global scope with overlay variables ───────────────
unset($_acfg, $_acfg_file);
