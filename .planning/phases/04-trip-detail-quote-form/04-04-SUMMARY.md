---
phase: 04-trip-detail-quote-form
plan: "04"
subsystem: api
tags: [php, curl, openai, gpt-4o-mini, webhook, json]

# Dependency graph
requires:
  - phase: 04-trip-detail-quote-form
    provides: includes/config.php constants (DEFAULT_WEBHOOK_URL, OPENAI_API_KEY, ROOT) and get_trip_by_slug() from functions.php

provides:
  - api/submit-form.php — webhook proxy that receives quote form POST and forwards JSON to per-trip or default webhook via cURL
  - api/generate-form.php — AI form_config generator using GPT-4o-mini with graceful default fallback when no API key

affects: [viaggio.php quote form JS fetch calls, admin panel (Phase 6)]

# Tech tracking
tech-stack:
  added: []
  patterns:
    - PHP JSON-only endpoints (no HTML output, Content-Type application/json header first)
    - cURL proxy pattern with CURLOPT_RETURNTRANSFER and HTTP code inspection
    - AI-with-default-fallback pattern: always return valid response, never fail the caller

key-files:
  created:
    - api/submit-form.php
    - api/generate-form.php
  modified: []

key-decisions:
  - "PHP CLI not available in bash on this machine — both endpoints verified by content inspection confirming all structural and behavioral requirements"
  - "generate-form.php falls back to default_form_config on cURL error or non-200 HTTP (not just on empty key) — caller always receives a usable form_config"
  - "source field in generate-form.php response distinguishes default / default_fallback / default_parse_fallback / ai for operator diagnostics"

patterns-established:
  - "PHP API endpoints in api/ use __DIR__ . '/../includes/config.php' for config path (one level up from api/)"
  - "Graceful degradation when no webhook configured: return {success:true, note:no_webhook} rather than error"
  - "AI endpoints always return success:true with a valid payload — errors surface in source/api_error fields, never as failures"

requirements-completed: [FORM-04, FORM-06]

# Metrics
duration: 5min
completed: 2026-03-06
---

# Phase 4 Plan 04: API Endpoints Summary

**PHP webhook proxy (submit-form.php) and GPT-4o-mini form generator (generate-form.php) with three-tier default fallback strategy**

## Performance

- **Duration:** ~5 min
- **Started:** 2026-03-06T17:17:05Z
- **Completed:** 2026-03-06T17:22:00Z
- **Tasks:** 2
- **Files modified:** 2 (created)

## Accomplishments

- Quote form submission endpoint that proxies POST data as JSON to per-trip webhook_url (falling back to DEFAULT_WEBHOOK_URL) with 10-second cURL timeout and graceful no-webhook degradation
- AI form_config generator that calls GPT-4o-mini with a structured Italian system prompt when OPENAI_API_KEY is present, returning a valid form_config in all code paths (API success, API failure, parse failure, or no key)
- `default_form_config()` extracts price hint from Euro pattern in description and derives all schema fields (single_supplement 18%, third/fourth bed -6%, competitor_benchmark +20%)

## Task Commits

Each task was committed atomically:

1. **Task 1: Create api/submit-form.php webhook proxy** - `f57b36e` (feat)
2. **Task 2: Create api/generate-form.php AI form generator** - `2f4a3d6` (feat)

**Plan metadata:** (docs commit — see below)

## Files Created/Modified

- `api/submit-form.php` — Webhook proxy: sanitizes quote-form POST, resolves webhook_url, forwards JSON via cURL, returns JSON success/error
- `api/generate-form.php` — AI generator: reads JSON body description, calls GPT-4o-mini or returns default_form_config, always returns valid form_config

## Decisions Made

- PHP CLI not available in bash on this machine — both endpoints verified by content inspection confirming all structural and behavioral requirements (consistent with prior plans in this phase)
- `generate-form.php` falls back to `default_form_config` on cURL error or non-200 HTTP, not just on empty key — caller always receives a usable form_config regardless of API health
- `source` field in generate-form.php response distinguishes `default` / `default_fallback` / `default_parse_fallback` / `ai` for operator diagnostics without breaking the response contract

## Deviations from Plan

None - plan executed exactly as written.

## Issues Encountered

PHP CLI not available in bash (same constraint as prior plans in this phase, documented in STATE.md). Both files verified by content inspection against plan specifications.

## User Setup Required

None — no external service configuration required at this stage. DEFAULT_WEBHOOK_URL and OPENAI_API_KEY are already defined as empty strings in includes/config.php and must be filled in before go-live.

## Next Phase Readiness

- FORM-04 complete: `api/submit-form.php` ready for `viaggio.php` quote form JS fetch POST
- FORM-06 complete: `api/generate-form.php` ready for admin panel AI generation trigger (Phase 6)
- Both endpoints follow same cURL/JSON conventions established in this plan — Phase 6 admin can add more endpoints in `api/` using the same pattern

---
*Phase: 04-trip-detail-quote-form*
*Completed: 2026-03-06*
