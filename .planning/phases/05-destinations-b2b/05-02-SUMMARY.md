---
phase: 05-destinations-b2b
plan: 02
subsystem: ui
tags: [php, destinations, editorial, waitlist, curl, ajax]

requires:
  - phase: 05-destinations-b2b-01
    provides: "includes/destinations-data.php with $destinations array, Phase 5 CSS (dest-*/b2b-* classes), WAITLIST_WEBHOOK_URL constant in config.php"

provides:
  - "destinazione.php — single PHP template rendering all 6 destination editorial pages"
  - "api/submit-waitlist.php — cURL webhook endpoint for sold-out destination waitlist form"

affects:
  - "Homepage destination cards (Phase 2) now resolve to working pages"
  - ".htaccess /destinazione/[slug] clean URL rule now serves real content"

tech-stack:
  added: []
  patterns:
    - "Single PHP template for multiple slugs — slug read from GET param, validated against whitelist, 404 on invalid"
    - "Conditional trips/waitlist rendering — published filter on get_trips_by_continent(), waitlist shown when result is empty"
    - "Inline AJAX waitlist form — fetch() POST to api endpoint, inline success/error message, no page reload"
    - "cURL webhook endpoint — clone of api/submit-form.php, graceful degradation when WAITLIST_WEBHOOK_URL is empty"

key-files:
  created:
    - "destinazione.php"
    - "api/submit-waitlist.php"
    - ".planning/phases/05-destinations-b2b/05-02-SUMMARY.md"
  modified: []

key-decisions:
  - "Breadcrumb 'Destinazioni' is plain text (not a link) — /destinazioni list page does not exist in v1"
  - "Waitlist AJAX uses inline fetch() pattern consistent with viaggio.php quote form — no separate .js file"
  - "api/submit-waitlist.php returns {success:true, note:'no_webhook'} when WAITLIST_WEBHOOK_URL is empty — site works without webhook configured"
  - "Trip cards on destination page use identical markup to viaggi.php — no custom card variant"

patterns-established:
  - "destinazione.php: hero_page=true before header include — triggers body.has-hero transparent header"
  - "Trip grid with published===true filter — must always wrap get_trips_by_continent() with array_filter"

requirements-completed:
  - DEST-01
  - DEST-02
  - DEST-03
  - DEST-04
  - DEST-05
  - DEST-06
  - DEST-07

duration: 4min
completed: 2026-03-06
---

# Phase 5 Plan 02: Destination Template + Waitlist API Summary

**Single reusable destinazione.php template for 6 editorial destination pages with conditional trip grid or waitlist AJAX form, backed by api/submit-waitlist.php cURL webhook endpoint**

## Performance

- **Duration:** 4 min
- **Started:** 2026-03-06T20:00:52Z
- **Completed:** 2026-03-06T20:04:52Z
- **Tasks:** 2
- **Files modified:** 2 (both created)

## Accomplishments

- Created `destinazione.php` as a single PHP template for all 6 destination slugs: full hero with breadcrumb, editorial intro section, practical info grid (5 boxes), Cosa Vedere 4-card grid, Curiosita 3-card grid, conditional trips section (published filter) or sold-out waitlist form with AJAX submission
- Created `api/submit-waitlist.php` as a simplified cURL webhook endpoint: accepts nome/email/telefono/destination_slug/destination_name, sanitizes inputs, gracefully degrades when WAITLIST_WEBHOOK_URL is empty, forwards JSON to webhook on 2xx success

## Task Commits

Each task was committed atomically:

1. **Task 1: Create destinazione.php** - `97c30c4` (feat)
2. **Task 2: Create api/submit-waitlist.php** - `b36e66b` (feat)

**Plan metadata:** (this commit — docs)

## Files Created/Modified

- `destinazione.php` — Single PHP template for all 6 continent destination pages; reads $destinations[$slug] from destinations-data.php; conditionally shows trip grid or waitlist form
- `api/submit-waitlist.php` — cURL webhook endpoint for waitlist form POSTs; graceful degradation when WAITLIST_WEBHOOK_URL is empty; returns JSON in all cases

## Decisions Made

- Breadcrumb "Destinazioni" is plain text only — the /destinazioni list page does not exist yet in v1. Linking it would produce a 404 (noted in RESEARCH.md pitfall 6).
- Waitlist uses inline `fetch()` AJAX (same pattern as Phase 4 quote form) — consistent with established project pattern, better UX than PHP redirect.
- `api/submit-waitlist.php` returns `{"success":true,"note":"no_webhook"}` when WAITLIST_WEBHOOK_URL is empty — allows the site to function fully without webhook configuration.
- Trip cards on the destination page use the exact same markup as `viaggi.php` — no custom card variant needed, CSS already covers `.trip-grid` + `.trip-card`.

## Deviations from Plan

None — plan executed exactly as written.

## Issues Encountered

None.

## User Setup Required

None — no external service configuration required. `WAITLIST_WEBHOOK_URL` defaults to empty string; api/submit-waitlist.php returns graceful success when empty. Set the constant in config.php to enable webhook delivery.

## Next Phase Readiness

- `destinazione.php` and `api/submit-waitlist.php` are complete and ready to serve live traffic
- Phase 5 Plan 03 (agenzie.php B2B page) can proceed independently — no dependencies on this plan's output
- All 6 destination slugs will load with full editorial content once deployed
- Homepage destination cards (Phase 2) now link to working pages

---
*Phase: 05-destinations-b2b*
*Completed: 2026-03-06*
