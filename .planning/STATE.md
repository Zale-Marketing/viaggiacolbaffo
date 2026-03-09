---
gsd_state_version: 1.0
milestone: v1.0
milestone_name: milestone
status: planning
stopped_at: Completed 06-admin-panel-06-PLAN.md
last_updated: "2026-03-06T21:36:27.891Z"
last_activity: 2026-03-06 — Roadmap created, 56 v1 requirements mapped to 6 phases
progress:
  total_phases: 6
  completed_phases: 4
  total_plans: 25
  completed_plans: 23
  percent: 0
---

# Project State

## Project Reference

See: .planning/PROJECT.md (updated 2026-03-06)

**Core value:** Lorenzo is personally present on every trip — the site conveys intimacy, trust, and premium experience while looking as established as Boscolo
**Current focus:** Phase 1 — Foundation

## Current Position

Phase: 1 of 6 (Foundation)
Plan: 0 of TBD in current phase
Status: Ready to plan
Last activity: 2026-03-09 - Completed quick task 5: Fix 4 bugs: room toggles inverted, B2B fields missing, checkbox labels invisible, counter room limits

Progress: [░░░░░░░░░░] 0%

## Performance Metrics

**Velocity:**
- Total plans completed: 0
- Average duration: -
- Total execution time: 0 hours

**By Phase:**

| Phase | Plans | Total | Avg/Plan |
|-------|-------|-------|----------|
| - | - | - | - |

**Recent Trend:**
- Last 5 plans: -
- Trend: -

*Updated after each plan completion*
| Phase 01-foundation P01 | 2 | 2 tasks | 8 files |
| Phase 01-foundation P02 | 2 | 2 tasks | 3 files |
| Phase 01-foundation P03 | 2 | 2 tasks | 5 files |
| Phase 02-homepage P01 | 2 | 2 tasks | 2 files |
| Phase 02-homepage P02 | 1min | 2 tasks | 0 files |
| Phase 02-homepage P03 | 2min | 2 tasks | 2 files |
| Phase 02-homepage P04 | 2min | 2 tasks | 1 files |
| Phase 03-trip-catalog P01 | 1min | 2 tasks | 2 files |
| Phase 03-trip-catalog P02 | 30min | 1 tasks | 2 files |
| Phase 04-trip-detail-quote-form P01 | 1min | 2 tasks | 2 files |
| Phase 04-trip-detail-quote-form P02 | 1min | 1 tasks | 1 files |
| Phase 04-trip-detail-quote-form P03 | 3min | 2 tasks | 1 files |
| Phase 04-trip-detail-quote-form P04 | 5min | 2 tasks | 2 files |
| Phase 05-destinations-b2b P01 | 4min | 2 tasks | 3 files |
| Phase 05-destinations-b2b P02 | 4min | 2 tasks | 2 files |
| Phase 05-destinations-b2b P03 | 3min | 2 tasks | 2 files |
| Phase 05-destinations-b2b P04 | 1min | 1 tasks | 0 files |
| Phase 05-destinations-b2b P04 | 1min | 1 tasks | 0 files |
| Phase 06-admin-panel P01 | 8min | 2 tasks | 7 files |
| Phase 06-admin-panel P05 | 2min | 2 tasks | 6 files |
| Phase 06-admin-panel P02 | 3min | 2 tasks | 2 files |
| Phase 06-admin-panel P04 | 3min | 2 tasks | 3 files |
| Phase 06-admin-panel P03 | 4min | 2 tasks | 1 files |
| Phase 06-admin-panel P06 | 2min | 1 tasks | 1 files |

## Accumulated Context

### Decisions

Decisions are logged in PROJECT.md Key Decisions table.
Recent decisions affecting current work:

- PHP + vanilla JS (no framework) — SiteGround shared hosting constraint
- trips.json as sole data store — no database available, PHP reads/writes directly
- Unsplash direct URLs for images — no upload infrastructure needed in v1
- AI form generator via GPT-4o-mini — optional feature, key empty by default in config.php
- Editorial destination pages always exist — makes site look like Boscolo even with only 2 trips
- [Phase 01-foundation]: FTP exclude list must include **/.git* and **/.git*/** — without these, FTP-Deploy-Action wipes defaults and uploads git state files
- [Phase 01-foundation]: data/.htaccess uses Apache 2.4 Require all denied (not Apache 2.2 Order deny,allow) — SiteGround runs Apache 2.4
- [Phase 01-foundation]: ROOT constant uses __DIR__ . '/..' from includes/ — portable across any server, never hard-coded path
- [Phase 01-foundation]: OPENAI_API_KEY left empty by default — AI form generator is opt-in, site works without it
- [Phase 01-foundation]: trips.json Italian text uses literal UTF-8 (JSON_UNESCAPED_UNICODE) for readable operator editing
- [Phase 01-foundation]: Japan form_config is empty object — sold-out trips suppress the Phase 4 quote form widget
- [Phase 01-foundation]: save_trips wraps array_values() — ensures 0-indexed JSON array after deletions in Phase 6 admin
- [Phase 01-foundation]: Trip card class names are PERMANENT from Plan 03 — phases 2-4 consume them directly without modification
- [Phase 01-foundation]: crossorigin attribute required on fonts.gstatic.com preconnect — without it the preconnect is silently ignored for CORS font requests
- [Phase 02-homepage]: No overflow:hidden on .hero (sticky header anti-pattern), body.has-hero scrolled rules after transparent rules (cascade order), will-change:transform on dest-card__img (Safari fix), hero flag pattern: set $hero_page=true before header include
- [Phase 02-homepage]: PHP CLI not available in bash — Task 2 PHP data layer verified via content inspection of config.php, functions.php, and trips.json; logic confirmed correct for load_trips() + published===true filter
- [Phase 02-homepage]: destinazione.php?slug= used for destination card hrefs (direct PHP URL per CONTEXT.md locked decision)
- [Phase 02-homepage]: Footer WHATSAPP_NUMBER uses str_replace to build wa.me URL; date('Y') for auto-updating copyright
- [Phase 02-homepage]: index.php written as single atomic file write — no append operations — eliminates partial-write risk
- [Quick-1-logo]: White pill background on .header-logo img and .footer-logo img — logo asset untouched, legible on any surface
- [Quick-1-logo]: Scroll threshold 80px on hero pages — keeps header transparent while user reads above-fold content
- [Phase 02-homepage]: min-height 280px on .trip-card prevents mobile card collapse, 3.5rem top padding on .trip-card__content clears badge zone, 4-stop gradient keeps top 30% transparent for badge legibility
- [Phase 03-trip-catalog]: Filter bar sticky at top:80px to clear site header; active pill uses #000744 navy (not var(--gold) which is red urgency)
- [Phase 03-trip-catalog]: PHP pre-apply single GET tag only; TALLY_CATALOG_URL guarded with PHP conditional; inline IIFE at page bottom
- [Phase 03-trip-catalog]: Filter bar redesigned post-approval: 4 compact dropdown menus replaced dual pill rows — more scalable as trip inventory grows
- [Phase 03-trip-catalog]: Empty state JS fix: display empty string changed to display block — empty string reverts to CSS default which was none
- [Phase 04-trip-detail-quote-form]: Phase 4 CSS uses same navy #000744 active state convention established in Phase 3 (not gold/red)
- [Phase 04-trip-detail-quote-form]: agency_code_hash is sha256('admin') — must be replaced before go-live with a stronger code
- [Phase 04-trip-detail-quote-form]: fourth_bed_price added as forward-compatible field even though no 4-bed room_type exists yet
- [Phase 04-trip-detail-quote-form]: PHP CLI not available in bash — viaggio.php syntax verified by grep content inspection (same constraint as Plan 01)
- [Phase 04-trip-detail-quote-form]: fmt_date() helper defined inline in viaggio.php for Italian month abbreviation — page-specific utility, not added to shared functions.php
- [Phase 04-trip-detail-quote-form]: agency-fields div carries inline style=display:none; JS controls visibility entirely — CSS load order independent
- [Phase 04-trip-detail-quote-form]: updatePrice multiplies addon total by (adultCount + childCount) — optional services cover the full booking group
- [Phase 04-trip-detail-quote-form]: validateAgencyCode falls back to showing fields on any non-empty code when agencyCodeHash is absent from form_config
- [Phase 04-trip-detail-quote-form]: PHP CLI not available in bash — api/submit-form.php and api/generate-form.php verified by content inspection
- [Phase 04-trip-detail-quote-form]: generate-form.php falls back to default_form_config on cURL error or non-200 HTTP — caller always receives valid form_config
- [Phase 04-trip-detail-quote-form]: source field in generate-form.php response distinguishes default/default_fallback/default_parse_fallback/ai for operator diagnostics
- [Quick-3-timeline-volo-hotel]: .volo-details-grid class used instead of .volo-details to avoid collision with id="volo-details" JS-controlled element
- [Quick-3-timeline-volo-hotel]: Volo toggle JS uses style.display check instead of classList.contains('open') — new HTML default is inline style=display:none
- [Quick-3-timeline-volo-hotel]: hotel key appended after volo key in trips.json; hotel-section placed between itinerary and cosa-include in viaggio.php
- [Phase 05-destinations-b2b]: TALLY_B2B_URL was already defined in config.php — only added WAITLIST_WEBHOOK_URL and WHATSAPP_B2B_FALLBACK to avoid duplicate define() errors
- [Phase 05-destinations-b2b]: All Phase 5 CSS uses unique dest-* and b2b-* prefixes — no collision with existing trip-card, section, btn--gold rules
- [Phase 05-destinations-b2b]: Breadcrumb 'Destinazioni' is plain text — /destinazioni list page does not exist in v1
- [Phase 05-destinations-b2b]: api/submit-waitlist.php returns success+note when WAITLIST_WEBHOOK_URL is empty — graceful degradation
- [Phase 05-destinations-b2b]: Commission language locked to 'commissioni competitive' only — no specific percentage ever written in agenzie.php
- [Phase 05-destinations-b2b]: destinazioni.php uses dest-cosa-card classes with 16/9 aspect-ratio override — no new CSS needed for listing cards
- [Phase 05-destinations-b2b]: Phase 5 verification is a blocking human-verify checkpoint — all 11 checklist items must pass before Phase 6 begins
- [Phase 05-destinations-b2b]: Phase 5 verification blocking human-verify checkpoint: all 11 checklist items passed and approved before Phase 6 begins
- [Phase 06-admin-panel]: config.php loads admin-config.json BEFORE any define() call — JSON overlay pattern, unset $_acfg at bottom to avoid global scope pollution
- [Phase 06-admin-panel]: ANTHROPIC_API_KEY replaces OPENAI_API_KEY throughout — rename reflects actual AI provider
- [Phase 06-admin-panel]: save_destinations() does NOT wrap array_values() — destinations is a keyed object accessed by slug, not a 0-indexed array
- [Phase 06-admin-panel]: viaggio.php 404 gates use header('Location: /404') redirect — 404.php does not exist on disk; consistent with existing null-trip guard
- [Phase 06-admin-panel]: generate-form.php fully migrated to Anthropic claude-sonnet-4-6; OPENAI_API_KEY removed; response parsed via content[0].text
- [Phase 06-admin-panel]: destinazione.php and destinazioni.php migrated from static destinations-data.php require to load_destinations() live JSON source
- [Phase 06-admin-panel]: JSON-body detection for reorder AJAX: Content-Type application/json decoded from php://input before action switch
- [Phase 06-admin-panel]: settings.php reads existing admin-config.json first and merges POST fields to preserve unknown keys
- [Phase 06-admin-panel]: tags.php cascade delete: save_tags() then save_trips() in same PHP request
- [Phase 06-admin-panel]: slug_locked computed from trip published flag at PHP render time; passed to JS via json_encode — slug becomes readonly and stays locked even if re-saved as draft
- [Phase 06-admin-panel]: form_config preserved from existing trip data during POST save — not overwritten by edit-trip.php form fields (Form Config tab is Plan 06)
- [Phase 06-admin-panel]: save_form_config merges webhook_url inside form_config before writing to trips.json — matches existing schema
- [Phase 06-admin-panel]: regenerate_token AJAX posts to same page instead of /admin/ajax.php — self-contained handler in edit-trip.php
- [Phase 06-admin-panel]: previewToken declared as let (not const) to allow update by regenerateToken() without page reload

### Pending Todos

None yet.

### Blockers/Concerns

None yet.

### Quick Tasks Completed

| # | Description | Date | Commit | Directory |
|---|-------------|------|--------|-----------|
| 1 | Fix logo visibility on dark backgrounds — white pill behind logo image in header and footer | 2026-03-06 | 00c034e | [1-fix-logo-visibility-on-dark-backgrounds-](./quick/1-fix-logo-visibility-on-dark-backgrounds-/) |
| 2 | Timeline itinerary, accompagnatore section, Dettagli Volo, tag pills on viaggio.php | 2026-03-06 | 1ccd33d | [2-timeline-itinerary-accompagnatore-sectio](./quick/2-timeline-itinerary-accompagnatore-sectio/) |
| 3 | Fix timeline visuals (red gradient line + red dots), redesign volo section (two-column card grid), add Alloggi tab with 3 hotel cards | 2026-03-06 | b961c05 | [3-fix-timeline-visuals-redesign-volo-secti](./quick/3-fix-timeline-visuals-redesign-volo-secti/) |
| 4 | destinazione.php text alignment, waitlist form redesign, agenzie B2B inline form, 404 for unknown slugs | 2026-03-06 | 35a698f | [4-fix-destinazione-php-text-alignment-wait](./quick/4-fix-destinazione-php-text-alignment-wait/) |
| 5 | Fix 4 bugs: room toggles inverted, B2B fields missing, checkbox labels invisible, counter room limits | 2026-03-09 | af8d755 | [5-fix-4-bugs-room-toggles-inverted-b2b-fie](./quick/5-fix-4-bugs-room-toggles-inverted-b2b-fie/) |

## Session Continuity

Last session: 2026-03-06T21:36:27.887Z
Stopped at: Completed 06-admin-panel-06-PLAN.md
Resume file: None
