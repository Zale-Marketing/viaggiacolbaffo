---
phase: 01-foundation
plan: 01
subsystem: infra
tags: [github-actions, ftp-deploy, apache, htaccess, php, siteground]

# Dependency graph
requires: []
provides:
  - GitHub Actions FTP deploy pipeline to SiteGround (push-to-main triggers deploy)
  - Root .htaccess with HTTPS redirect, clean URLs, gzip, and browser caching
  - data/.htaccess blocking direct HTTP access to /data/ (Apache 2.4 syntax)
  - includes/config.php with all shared constants for every downstream phase
  - assets/img/ directory tracked via .gitkeep
  - admin/ and api/ placeholder directories
  - README.md with operator documentation
affects: [01-02, 01-03, 02-homepage, 03-catalog, 04-trip-detail, 05-destinations, 06-admin]

# Tech tracking
tech-stack:
  added: [SamKirkland/FTP-Deploy-Action@v4.3.6, actions/checkout@v4, Apache mod_deflate, Apache mod_expires]
  patterns:
    - "config.php defines all shared constants — every PHP file does require_once includes/config.php"
    - "ROOT and DATA_DIR constants from __DIR__ — never relative paths in PHP"
    - "Two-layer .htaccess: root handles routing, data/.htaccess denies HTTP access"
    - "FTP secrets in GitHub Secrets only — never in codebase"

key-files:
  created:
    - .github/workflows/deploy.yml
    - .htaccess
    - data/.htaccess
    - includes/config.php
    - assets/img/.gitkeep
    - admin/.gitkeep
    - api/.gitkeep
    - README.md
  modified: []

key-decisions:
  - "FTP exclude list MUST include **/.git* and **/.git*/** — without these, FTP-Deploy-Action's own defaults are wiped and git state files upload"
  - "data/.htaccess uses Apache 2.4 syntax (Require all denied), not Apache 2.2 (Order deny,allow)"
  - "ROOT uses __DIR__ . '/..' from includes/ — not a hard-coded path, works on any server"
  - "OPENAI_API_KEY empty by default — AI form generator is opt-in, site works without it"

patterns-established:
  - "Clean URL pattern: /viaggio/{slug} -> viaggio.php?slug={slug} with /?$ trailing-slash tolerance"
  - "Constants pattern: all shared config in includes/config.php, downstream plans require_once this file"
  - "Directory protection: data/.htaccess with 'Require all denied' for any sensitive data directory"

requirements-completed: [INFRA-01, INFRA-02, INFRA-03, INFRA-04, INFRA-05]

# Metrics
duration: 2min
completed: 2026-03-06
---

# Phase 1 Plan 01: Infrastructure Summary

**GitHub Actions FTP pipeline to SiteGround with Apache clean URLs, HTTPS redirect, /data/ protection, and PHP config constants**

## Performance

- **Duration:** 2 min
- **Started:** 2026-03-06T11:04:57Z
- **Completed:** 2026-03-06T11:07:03Z
- **Tasks:** 2
- **Files modified:** 8

## Accomplishments

- Deploy pipeline via SamKirkland/FTP-Deploy-Action@v4.3.6 targets `/nuovo.viaggiacolbaffo.com/public_html/` on push to main, excluding .git, .claude, .planning, and README.md
- Root .htaccess enforces HTTPS (301 redirect), provides clean URLs for /viaggio/ and /destinazione/ slugs with trailing-slash tolerance, removes .php extensions, and adds gzip + browser caching
- data/.htaccess uses Apache 2.4 `Require all denied` to block direct HTTP access to JSON data files
- includes/config.php defines all 8 shared constants (ADMIN_PASSWORD, WHATSAPP_NUMBER, TALLY_CATALOG_URL, TALLY_B2B_URL, OPENAI_API_KEY, DEFAULT_WEBHOOK_URL, ROOT, DATA_DIR) with operator comments
- README.md covers setup (clone, GitHub Secrets, push to main), trip management (all fields and status values), webhook configuration, and OpenAI key setup

## Task Commits

Each task was committed atomically:

1. **Task 1: GitHub Actions deploy workflow + directory scaffold** - `eab8744` (feat)
2. **Task 2: .htaccess rules, data protection, config.php, and README** - `a4272eb` (feat)

**Plan metadata:** (docs commit — see final_commit step)

## Files Created/Modified

- `.github/workflows/deploy.yml` - GitHub Actions FTP deploy workflow, push-to-main trigger
- `.htaccess` - HTTPS redirect, clean URLs (/viaggio/, /destinazione/, extension-less), gzip, browser caching
- `data/.htaccess` - Apache 2.4 `Require all denied` to block direct HTTP access to /data/
- `includes/config.php` - All shared PHP constants for every phase (ADMIN_PASSWORD through DATA_DIR)
- `assets/img/.gitkeep` - Tracks empty img directory in git
- `admin/.gitkeep` - Placeholder for Phase 6 admin panel directory
- `api/.gitkeep` - Placeholder for Phase 4 API endpoints directory
- `README.md` - Operator documentation: setup, trip management, webhook config, OpenAI key setup

## Decisions Made

- FTP exclude list uses `**/.git*` and `**/.git*/**` patterns — without these, FTP-Deploy-Action wipes its own defaults and uploads git state files to the server
- Apache 2.4 syntax (`Require all denied`) in data/.htaccess, not deprecated 2.2 syntax (`Order deny,allow`) — SiteGround runs Apache 2.4
- `ROOT` defined as `__DIR__ . '/..'` from within `includes/` — portable across any server, never a hard-coded absolute path
- `OPENAI_API_KEY` left empty by default — AI form generator is an opt-in feature, all other functionality works without it

## Deviations from Plan

None - plan executed exactly as written.

## Issues Encountered

- `php -l` verification skipped — PHP not available in the bash execution environment. Config file was verified by direct inspection: all 8 `define()` calls are syntactically correct with proper string quoting and `<?php` opening tag.

## User Setup Required

The following GitHub Secrets must be added in the repository before the deploy workflow will run:

- `FTP_SERVER` — SiteGround FTP hostname (e.g. `ftp.viaggiacolbaffo.com`)
- `FTP_USERNAME` — SiteGround FTP username
- `FTP_PASSWORD` — SiteGround FTP password

These are already configured per the CONTEXT.md notes ("GitHub Secrets already created in repo settings").

Also required before go-live (in `includes/config.php`):
- Change `ADMIN_PASSWORD` from `Admin2025!` to a strong password
- Fill in `WHATSAPP_NUMBER` with Lorenzo's actual number
- Fill in `TALLY_CATALOG_URL` and `TALLY_B2B_URL` when Tally forms are created

## Next Phase Readiness

- Deploy pipeline is live — every subsequent plan's code will be shipped by this workflow
- Clean URL patterns are established — all downstream pages must use matching slugs
- config.php constants are defined — plans 01-02 and 01-03 can `require_once 'includes/config.php'` immediately
- data/ directory exists and is protected — plan 01-02 can write trips.json and tags.json
- No blockers for 01-02 (data layer) or 01-03 (design system)

---
*Phase: 01-foundation*
*Completed: 2026-03-06*

## Self-Check: PASSED

All 9 files verified present on disk. Both task commits (eab8744, a4272eb) confirmed in git log.
