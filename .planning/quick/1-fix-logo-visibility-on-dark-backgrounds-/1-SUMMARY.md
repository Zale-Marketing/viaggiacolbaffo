---
phase: quick-1-logo-visibility
plan: "01"
subsystem: frontend-styles
tags: [logo, header, footer, css, visibility, dark-background]
dependency_graph:
  requires: []
  provides: [logo-visibility-fix, header-scroll-behavior]
  affects: [assets/css/style.css, includes/header.php, includes/footer.php]
tech_stack:
  added: []
  patterns: [css-pill-background, sticky-header-scroll-class]
key_files:
  created: []
  modified:
    - assets/css/style.css
    - includes/header.php
    - includes/footer.php
decisions:
  - White pill background on logo img elements (background+padding+border-radius) chosen over logo asset replacement — no asset infrastructure needed
  - margin-bottom preserved on footer logo anchor via inline style since .site-footer__logo rule was deleted
  - Scroll threshold raised to 80px so hero header stays transparent while user reads initial viewport
metrics:
  duration: "~3 min"
  completed: "2026-03-06"
  tasks_completed: 2
  files_modified: 3
---

# Quick Fix 1: Fix Logo Visibility on Dark Backgrounds — Summary

**One-liner:** White pill background (padding+border-radius on img) wraps the navy+red logo so it reads on dark hero headers and dark footer; scroll threshold raised from 10px to 80px.

## What Was Done

### Task 1 — style.css

- Added `transition: background 0.3s ease, border-color 0.3s ease` to `header` for smooth state changes.
- Increased hero transparent header opacity from `rgba(0,0,0,0.3)` to `rgba(0,0,0,0.35)` for slightly better legibility of nav links.
- Replaced `var(--white)` with `#FFFFFF` literal in the `.scrolled` state to avoid any variable-resolution edge cases.
- Added `.header-logo img` rule: `background: #FFFFFF; padding: 6px 10px; border-radius: 8px; max-height: 50px; width: auto; display: block;`
- Added `.footer-logo img` rule: `background: #FFFFFF; padding: 8px 12px; border-radius: 8px; max-height: 55px; width: auto; display: block;`
- Removed `.site-footer__logo` rule (dead after footer PHP change; `margin-bottom` preserved on anchor).

### Task 2 — header.php + footer.php

- Added `class="header-logo"` to the logo `<a>` in `header.php`; removed inline style from the `<img>` (properties now in CSS).
- Changed scroll threshold from `window.scrollY > 10` to `window.scrollY > 80` in the inline scroll listener.
- Updated footer logo anchor to `class="footer-logo" style="display:block;margin-bottom:1rem;"` and removed `class="site-footer__logo"` from the `<img>`.

## Success Criteria Verification

- `.header-logo img` with `background: #FFFFFF` — confirmed in style.css line 318-319
- `.footer-logo img` with `background: #FFFFFF` — confirmed in style.css line 327-328
- `body.has-hero #site-header` with `rgba(0, 0, 0, 0.35)` — confirmed in style.css line 342
- `scrollY > 80` — confirmed in includes/header.php line 34
- `class="header-logo"` — confirmed in includes/header.php line 23
- `class="footer-logo"` — confirmed in includes/footer.php line 7
- No inline styles left on logo img elements — confirmed

## Commits

| Hash | Message |
|------|---------|
| ce27437 | feat(quick-1-01): add logo pill CSS and correct header scroll rules |
| 00c034e | fix(quick-1-01): apply header-logo/footer-logo classes, fix scroll threshold |

## Deviations from Plan

None — plan executed exactly as written.

## Self-Check: PASSED

- `assets/css/style.css` — exists and contains all required rules
- `includes/header.php` — exists and contains `class="header-logo"` and `scrollY > 80`
- `includes/footer.php` — exists and contains `class="footer-logo"`
- Both commits confirmed in git log
