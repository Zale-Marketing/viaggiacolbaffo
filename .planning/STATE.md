---
gsd_state_version: 1.0
milestone: v1.0
milestone_name: milestone
status: planning
stopped_at: Completed 03-trip-catalog-01-PLAN.md
last_updated: "2026-03-06T15:07:08.745Z"
last_activity: 2026-03-06 — Roadmap created, 56 v1 requirements mapped to 6 phases
progress:
  total_phases: 6
  completed_phases: 2
  total_plans: 9
  completed_plans: 8
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
Last activity: 2026-03-06 — Roadmap created, 56 v1 requirements mapped to 6 phases

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

### Pending Todos

None yet.

### Blockers/Concerns

None yet.

### Quick Tasks Completed

| # | Description | Date | Commit | Directory |
|---|-------------|------|--------|-----------|
| 1 | Fix logo visibility on dark backgrounds — white pill behind logo image in header and footer | 2026-03-06 | 00c034e | [1-fix-logo-visibility-on-dark-backgrounds-](./quick/1-fix-logo-visibility-on-dark-backgrounds-/) |

## Session Continuity

Last session: 2026-03-06T15:07:08.742Z
Stopped at: Completed 03-trip-catalog-01-PLAN.md
Resume file: None
