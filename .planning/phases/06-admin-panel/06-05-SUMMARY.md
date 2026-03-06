---
phase: 06-admin-panel
plan: 05
subsystem: ui
tags: [php, admin-config, anthropic, claude-sonnet-4-6, destinations-json]

# Dependency graph
requires:
  - phase: 06-admin-panel
    plan: 01
    provides: "ANTHROPIC_API_KEY constant, load_destinations() function, admin-config.json data layer"

provides:
  - viaggio.php deleted/published/preview-token access gates
  - index.php urgency bar driven by admin-config.json
  - footer.php company name/VAT/address from admin-config.json
  - generate-form.php Anthropic API integration replacing OpenAI
  - destinazione.php + destinazioni.php live data via load_destinations()

affects:
  - phase-07
  - admin panel tests
  - public site QA

# Tech tracking
tech-stack:
  added: []
  patterns:
    - "admin-config.json read inline with DATA_DIR constant wherever site content is needed"
    - "Anthropic claude-sonnet-4-6 API call pattern with x-api-key header and content[0].text response path"
    - "Preview token bypass: ?preview=TOKEN in GET allows viewing unpublished trips"

key-files:
  created: []
  modified:
    - viaggio.php
    - index.php
    - includes/footer.php
    - api/generate-form.php
    - destinazione.php
    - destinazioni.php

key-decisions:
  - "viaggio.php gates use header('Location: /404') redirect consistent with existing not-found pattern (no 404.php exists on disk)"
  - "Urgency bar collapsed to single <span> — admin-config.json urgency_bar_text replaces split text/pill spans"
  - "generate-form.php: OPENAI_API_KEY fully removed; ANTHROPIC_API_KEY checked; response parsed via content[0].text"
  - "destinazione.php and destinazioni.php: destinations-data.php require replaced with load_destinations() — both files iterated $destinations as keyed array which load_destinations() returns identically"

patterns-established:
  - "Pattern 1: Admin-config.json inline read — use DATA_DIR . 'admin-config.json', json_decode, unset vars after use"
  - "Pattern 2: Anthropic API call — curl_init with x-api-key header, anthropic-version: 2023-06-01, parse $body['content'][0]['text']"

requirements-completed: [ADMIN-09]

# Metrics
duration: 2min
completed: 2026-03-06
---

# Phase 6 Plan 05: Public-Site Integration Summary

**Published/deleted/preview-token gates added to viaggio.php, urgency bar and footer driven by admin-config.json, generate-form.php migrated to Anthropic claude-sonnet-4-6, and destination pages migrated to load_destinations() JSON source**

## Performance

- **Duration:** ~2 min
- **Started:** 2026-03-06T21:29:50Z
- **Completed:** 2026-03-06T21:29:56Z
- **Tasks:** 2
- **Files modified:** 6

## Accomplishments
- viaggio.php now gates deleted trips (redirect /404) and unpublished trips (redirect /404 unless ?preview=TOKEN matches preview_token)
- index.php urgency bar text read from admin-config.json (urgency_bar_text key), with hardcoded Italian fallback
- footer.php company name, P.IVA, and address read from admin-config.json; P.IVA and address rendered conditionally when non-empty
- api/generate-form.php fully migrated from OpenAI GPT-4o-mini to Anthropic claude-sonnet-4-6 — OPENAI_API_KEY removed, ANTHROPIC_API_KEY checked, response parsed via $body['content'][0]['text']
- destinazione.php and destinazioni.php migrated from static `require_once destinations-data.php` to `load_destinations()` live JSON source

## Task Commits

Each task was committed atomically:

1. **Task 1: viaggio.php gates, index.php urgency bar, footer.php company data** - `d2eb74d` (feat)
2. **Task 2: api/generate-form.php Anthropic + destinazione.php migration** - `8375073` (feat)

## Files Created/Modified
- `viaggio.php` - Added deleted gate, preview_token check, published gate after get_trip_by_slug()
- `index.php` - Added admin-config.json read block; urgency bar HTML uses $urgency_text variable
- `includes/footer.php` - Added admin-config.json read for company_name/company_vat/company_address; footer bottom bar renders dynamic values
- `api/generate-form.php` - Replaced entire OpenAI cURL block with Anthropic claude-sonnet-4-6 call; removed OPENAI_API_KEY reference
- `destinazione.php` - Replaced require_once destinations-data.php with $destinations = load_destinations()
- `destinazioni.php` - Replaced require_once destinations-data.php with $destinations = load_destinations()

## Decisions Made
- viaggio.php 404 handling uses `header("Location: /404")` redirect (consistent with existing slug-not-found pattern) — 404.php does not exist on disk, so require would have crashed
- Urgency bar HTML simplified from two spans (text + pill) to one span containing the full $urgency_text string — admin UI stores a single string, no need to split
- generate-form.php fallback chain preserved: no-key → default, API error → default_fallback, parse fail → default_parse_fallback, success → ai

## Deviations from Plan

### Auto-fixed Issues

**1. [Rule 3 - Blocking] 404.php does not exist — used redirect instead of require**
- **Found during:** Task 1 (viaggio.php gates)
- **Issue:** Plan specified `require ROOT . '/404.php'; exit;` but 404.php does not exist on disk; would have caused a fatal error
- **Fix:** Used `header("Location: /404"); exit;` consistent with the existing null-trip guard two lines above
- **Files modified:** viaggio.php
- **Verification:** grep confirms redirect pattern matches existing slug guard
- **Committed in:** d2eb74d (Task 1 commit)

---

**Total deviations:** 1 auto-fixed (1 blocking)
**Impact on plan:** Fix necessary for correctness — using the established redirect pattern already in viaggio.php. No scope creep.

## Issues Encountered
None beyond the 404.php deviation documented above.

## User Setup Required
None - no external service configuration required for these file changes. ANTHROPIC_API_KEY must be set in admin-config.json to enable AI form generation (existing opt-in behaviour).

## Next Phase Readiness
- Public site now fully reads from admin-managed data sources (trips.json, destinations.json, admin-config.json)
- Admin panel (Plans 01-04) + public integration (Plan 05) complete — ready for Phase 6 final verification plan (06)
- No blockers

---
*Phase: 06-admin-panel*
*Completed: 2026-03-06*
