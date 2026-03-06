<?php
/**
 * Viaggia Col Baffo — Shared Configuration
 *
 * This file is included by every PHP page in the project.
 * Fill in the values below before going live.
 */

// SECURITY: Change this password before go-live.
define('ADMIN_PASSWORD', 'Admin2025!');

// Lorenzo's WhatsApp number (include country code, e.g. +39 333 1234567)
// Used in the footer and quote form CTAs.
define('WHATSAPP_NUMBER', '+39 XXX XXXXXXX');

// Tally form URLs — fill these in when your Tally forms are created.
// TALLY_CATALOG_URL: the custom-request form shown when no trips match catalog filters.
// TALLY_B2B_URL: the agency registration form on the B2B page.
define('TALLY_CATALOG_URL', '');
define('TALLY_B2B_URL', '');

// OpenAI API key — leave empty to disable the AI form generator (Phase 4).
// Fill in when you want to enable AI-powered form generation in the admin panel.
define('OPENAI_API_KEY', '');

// Default webhook URL for quote-form submissions.
// Can be overridden per-trip via the 'webhook_url' field in form_config.
define('DEFAULT_WEBHOOK_URL', '');

// Absolute path to the project root (the directory containing this includes/ folder).
// Always use ROOT and DATA_DIR in PHP — never relative paths.
define('ROOT', __DIR__ . '/..');

// Absolute path to the data directory.
define('DATA_DIR', ROOT . '/data/');

// Phase 5: Destinations + B2B
define('WAITLIST_WEBHOOK_URL', '');      // POST target for destination waitlist form
define('WHATSAPP_B2B_FALLBACK', '');     // WhatsApp link when TALLY_B2B_URL is empty
