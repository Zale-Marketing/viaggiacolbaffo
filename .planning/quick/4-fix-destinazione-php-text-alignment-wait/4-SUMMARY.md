---
quick: 4
subsystem: frontend
tags: [php, css, forms, destinations, b2b, ux]
dependency_graph:
  requires: []
  provides: [destinazione.php, agenzie.php]
  affects: [destinazione.php, agenzie.php]
tech_stack:
  added: []
  patterns: [inline-styles, inline-script, fetch-post-json, php-404-response]
key_files:
  modified:
    - destinazione.php
    - agenzie.php
decisions:
  - "PHP if/else branches now each own their complete section tags — no shared open/close HTML across branches"
  - "agenzie.php registration section replaced with fully inline form + style + script (no Tally iframe dependency)"
  - "404 uses http_response_code(404) + full page render instead of header(Location) redirect"
metrics:
  duration: "~5 minutes"
  completed: "2026-03-06"
  tasks_completed: 3
  files_modified: 2
---

# Quick Task 4: Fix destinazione.php text alignment, waitlist form redesign, agenzie B2B form, 404 for unknown slugs

**One-liner:** Fixed four UX/functional issues across destinazione.php and agenzie.php — left-aligned editorial text with 800px container, navy gradient waitlist card with dark inputs and red submit, full inline partner registration form POSTing JSON to Pabbly webhook, and proper HTTP 404 with "Pagina non trovata" for unknown slugs.

## Tasks Completed

| # | Task | Commit | Files |
|---|------|--------|-------|
| 1 | Fix destinazione.php — text alignment, waitlist form redesign, 404 for unknown slugs | 35a698f | destinazione.php |
| 2 | Replace agenzie.php registration section with inline partner form | 35a698f | agenzie.php |
| 3 | Commit all changes | 35a698f | — |

## Changes Made

### destinazione.php — ISSUE 1: Text alignment

- Added `<div style="max-width:800px;margin:0 auto;">` wrapper around the `intro_paragraphs` loop
- Each `<p class="dest-intro">` now carries `style="text-align:left;"`
- The `section-header__title` ("Scopri X") retains its existing centered CSS class

### destinazione.php — ISSUE 2: Waitlist form redesign

- Replaced the old plain `dest-waitlist` div with a navy gradient `.waitlist-card` section
- Card: `linear-gradient(135deg, #000744 0%, #000a66 100%)`, `border-radius:16px`, `max-width:600px`, `box-shadow:0 20px 60px rgba(0,7,68,0.4)`
- Icon: `fas fa-map-marked-alt` at 3rem, color `#CC0031`
- Title: "Nessun viaggio attivo per questa destinazione" in Playfair Display, white, 1.8rem
- Fields: dark glass inputs (`rgba(255,255,255,0.08)` background, `rgba(255,255,255,0.2)` border, white text)
- Submit: `#CC0031` background, hover darkens to `#a80028` with `translateY(-2px)`
- On success: form hides (`style.display='none'`), `#waitlist-success` div becomes visible with checkmark icon and "Perfetto! Ti contatteremo presto"
- Updated inline script to use `.wl-btn` selector and new success state pattern

### destinazione.php — ISSUE 4: 404 for unknown slugs

- Replaced `header('Location: /404')` + `exit` with:
  - `http_response_code(404)`
  - Include header, render styled "Pagina non trovata" page with back link to `/`
  - Include footer then `exit`
- No redirect occurs — response is 404 with inline page content
- Refactored the PHP if/else into clean top-level blocks (each branch owns its full `<section>` tags)

### agenzie.php — ISSUE 3: Inline partner registration form

- Removed the old section (Tally iframe / WhatsApp fallback / email fallback)
- Replaced with `<section class="b2b-form-section" id="registration">` containing:
  - Inline `<style>` block: form-container, form-header, guarantee-section, benefits-bar, form-row grid (2-col, single, triple), form-group, select, radio-group, radio-option (:has checked state), checkbox-group, submit-btn, message, loading/spinner
  - Full form with sections: Dati Agenzia (ragioneSociale, nomeCommerciale, partitaIva, codiceFiscale, codiceUnivoco, licenza, annoFondazione, fondoGaranzia, indirizzo, citta, cap, provincia), Referente Principale (nomeReferente, cognomeReferente, ruolo select, telefono, email, emailPec), Come ci hai conosciuto (radio group), Consensi e Privacy (3 checkboxes)
  - Inline `<script>` POSTs JSON to `https://connect.pabbly.com/workflow/sendwebhookdata/IjU3NjYwNTY0MDYzZjA0MzM1MjY1NTUzNzUxMzMi_pc`
  - Loading spinner during submission; success message replaces form on 2xx response; error message on failure
- All other sections (hero, trust-bar, value-props, how-it-works, guarantee, testimonial) untouched

## Deviations from Plan

### Auto-fixed Issues

**1. [Rule 1 - Bug] PHP if/else HTML structure refactored for clean branching**

- **Found during:** Task 1 — initial edit left a broken pattern with `</div></section>` shared across both branches and a dummy hidden `<section>` to close the PHP block
- **Fix:** Rewrote the entire VIAGGI O WAITLIST comment block so each `<?php if ...?>` / `<?php else: ?>` branch owns its complete, self-contained section element — no shared HTML open/close across branches
- **Files modified:** destinazione.php
- **Commit:** 35a698f

## Self-Check

- [x] `http_response_code(404)` present in destinazione.php (line 9)
- [x] `text-align:left` present on intro paragraphs (line 71)
- [x] `.waitlist-card` CSS class present in destinazione.php
- [x] `linear-gradient(135deg, #000744` present in destinazione.php
- [x] `partnerForm` present in agenzie.php (lines 407, 560)
- [x] `pabbly.com` webhook URL present in agenzie.php (line 558)
- [x] `ragioneSociale` field present in agenzie.php (lines 413-414)
- [x] Commit `35a698f` exists in git log

## Self-Check: PASSED
