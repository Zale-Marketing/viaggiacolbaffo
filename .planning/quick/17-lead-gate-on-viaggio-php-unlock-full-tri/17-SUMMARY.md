---
phase: quick-17
plan: 01
subsystem: ui
tags: [lead-gate, localStorage, fetch, blur, php, vanilla-js]

requires:
  - phase: quick-16
    provides: viaggio.php with centered tabs and hotel cards (base file)

provides:
  - Lead gate overlay HTML card (Nome/Cognome/Email/Telefono form) on viaggio.php
  - PHP itinerary split — days 1-2 free, day 3+ gated
  - 9 gated-content wrapper divs covering all content below day 2
  - GATE JS object (slug + webhook) in unconditional script block
  - JS gate logic (unlockGate, isUnlocked, localStorage persist, webhook POST with silent-fail)
  - Lead gate CSS appended to style.css (blur/opacity hidden state, card, form, button, responsive)

affects: [viaggio.php, assets/css/style.css]

tech-stack:
  added: []
  patterns:
    - "Lead gate: PHP splits itinerary array_slice(0,2) free + array_slice(2) gated"
    - "gated-content--hidden CSS class applied by JS on page load if not unlocked"
    - "GATE object defined before IIFE so it is accessible inside the closure"
    - "unlockGate() uses classList.remove for unhide + setTimeout 400ms for fade-out"
    - "localStorage key is per-trip: vcb_unlocked_{slug}"

key-files:
  created: []
  modified:
    - viaggio.php
    - assets/css/style.css

key-decisions:
  - "Lead gate overlays content via gradient fade (margin-top:-120px) rather than absolutely positioned modal — integrates naturally into page flow"
  - "Gate webhook POST fails silently and still unlocks — conversion never blocked by webhook errors"
  - "GATE object placed in unconditional script block (not inside if ($has_form)) — gate is always active regardless of quote form config"
  - "Lightbox div left outside gated-galleria wrapper — lightbox is a UI utility, not content"
  - "gated-content--hidden applied via JS classList, not PHP inline style — allows JS to remove class cleanly on unlock without style attribute conflicts"

requirements-completed: [LEAD-GATE-01]

duration: 8min
completed: 2026-03-09
---

# Quick Task 17: Lead Gate on viaggio.php Summary

**Lead gate form (Nome/Cognome/Email/Telefono) overlays viaggio.php day 3+ itinerary and all gated sections; localStorage unlock persists across refreshes; webhook POST fails silently and unlocks anyway**

## Performance

- **Duration:** ~8 min
- **Started:** 2026-03-09T15:10:00Z
- **Completed:** 2026-03-09T15:18:00Z
- **Tasks:** 2
- **Files modified:** 2

## Accomplishments

- Inserted lead gate overlay card with 4-field form (Nome, Cognome, Email, Telefono) between the last free itinerary day and the gated content
- Split the PHP itinerary foreach into `array_slice(0,2)` free days + `array_slice(2)` gated wrapper div
- Wrapped 8 additional sections with `class="gated-content"` divs: accompagnatore, volo, alloggi, cosa-include, galleria, tags, related, preventivo
- Added GATE JS object (`slug` + `webhook`) in the unconditional `<script>` block outside the quote form conditional
- Implemented full JS gate logic: auto-hide on load, localStorage check, form validation, spinner, webhook fetch (silent fail), unlock animation
- Appended 165-line lead gate CSS block at end of style.css: blur/opacity hidden state, card, icon, form rows, error, button, privacy note, mobile responsive

## Task Commits

1. **Task 1: PHP + HTML changes — GATE obj, gate overlay, split itinerary, gated-content wrappers** - `d3ef06e` (feat)
2. **Task 2: JS gate logic in IIFE + lead gate CSS in style.css** - `f59705a` (feat)

## Files Created/Modified

- `/viaggio.php` - Lead gate overlay HTML, GATE PHP object, itinerary array_slice split, 9 gated-content wrapper divs, JS gate logic in IIFE
- `/assets/css/style.css` - LEAD GATE CSS block appended (gated-content--hidden, lead-gate card/form/btn/privacy, @media 600px)

## Decisions Made

- GATE object declared before IIFE in the unconditional script block — ensures it is always defined even when `$has_form` is false and the CONFIG block is absent
- Webhook POST fails silently (`catch` calls `doUnlock`) — gate never blocks a conversion due to infrastructure issues
- Lightbox left outside gated-galleria div — lightbox is a modal utility, not gated content
- `gated-content--hidden` managed entirely by JS classList — clean removal on unlock without inline style interference
- Gate overlay uses `margin-top: -120px` gradient fade — blends naturally into the visible itinerary days rather than appearing as a separate modal

## Deviations from Plan

None — plan executed exactly as written.

## Issues Encountered

None.

## User Setup Required

Optional: Set `WAITLIST_WEBHOOK_URL` constant in `includes/config.php` (or `data/admin-config.json`) to receive lead submissions via POST. If not set, the gate still unlocks after form submission — data is not captured but conversion is not blocked.

## Next Phase Readiness

- Lead gate is live on all viaggio.php pages immediately
- WAITLIST_WEBHOOK_URL can be configured in admin settings whenever a webhook endpoint is available
- Gate unlock state is per-trip per-browser via localStorage key `vcb_unlocked_{slug}`

---
*Phase: quick-17*
*Completed: 2026-03-09*
