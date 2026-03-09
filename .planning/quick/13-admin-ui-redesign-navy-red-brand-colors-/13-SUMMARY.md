---
phase: quick-13
plan: 01
subsystem: admin-ui
tags: [css, design-system, navy, red, gold-removal]
key-decisions:
  - "btn-primary remains navy for secondary actions; new btn-cta class is red #CC0031 for publish/create"
  - "edit-trip.php inline styles updated in-place (not deleted) since they define edit-page-specific layout rules"
  - "saveFormConfig button in edit-trip.php upgraded from bare btn-primary class to btn btn-primary"
  - "logo-icon span added to all 5 admin PHP nav bars"
key-files:
  modified:
    - admin/admin.css
    - admin/login.php
    - admin/index.php
    - admin/edit-trip.php
    - admin/settings.php
    - admin/tags.php
    - admin/destinations.php
metrics:
  completed: 2026-03-09
  tasks: 5
  files: 7
---

# Quick Task 13: Admin UI Redesign — Navy/Red Brand Colors Summary

**One-liner:** Complete design system replacement — navy #000744 primary, red #CC0031 CTA, zero gold (#C9A84C) across all 7 admin files.

## What Was Done

### Task 1 — admin/admin.css: Full Replacement

Replaced the entire CSS file with a new navy/red design system:

- CSS variables: `--navy: #000744`, `--red: #CC0031`, `--navy-dark`, `--navy-light`, `--red-dark` — no gold variables
- `.btn-primary`: navy background (secondary admin actions)
- `.btn-cta`: red background (publish, create — primary CTA actions)
- `.admin-stat-card--red::before` and `--green::before` top-border modifiers added
- `.admin-nav__logo .logo-icon`: red pill icon in nav
- `.admin-nav__visit`: red pill link style
- `#toast.toast-error` / `#toast.toast-success`: separate classes for toast coloring
- Tab active state uses `border-bottom-color: var(--red)` — no gold
- All focus rings use `rgba(0,7,68,0.1)` — navy-tinted, no gold shadow
- Section title bar `::before` accent uses `var(--red)`
- Itinerary `drag-over` border uses `var(--red)`
- Login card `.admin-login__logo` now navy-background-free (red, per CSS)

### Task 2 — admin/login.php

- Replaced gold compass icon + text logo with:
  - `<div class="admin-login__logo"><i class="fa-solid fa-compass"></i></div>`
  - `<div class="admin-login__title">Viaggia col Baffo</div>`
  - Updated subtitle text

### Task 3 — admin/index.php

- Nav logo: replaced `<i style="color:var(--gold)">` with `<span class="logo-icon"><i class="fa-solid fa-compass"></i></span>`
- "Vai al sito" link: added `class="admin-nav__visit"` (red pill style)
- Published stat card: added `admin-stat-card--red` modifier class
- Draft stat card: added `admin-stat-card--green` modifier class
- "Crea Nuovo Viaggio" button (header + empty state): changed `btn-primary` to `btn-cta`
- `showToast`: replaced inline style approach with className-based (`toast-error`/`toast-success` + FA icons)
- Removed entire inline `<style>` block (drag/drop, pill-toggle, toast, trash-header CSS — all now in admin.css)

### Task 4 — admin/edit-trip.php

- Nav logo: replaced bare text logo with `<span class="logo-icon"><i class="fa-solid fa-compass"></i></span>`
- Inline style gold references replaced:
  - `.tab-btn.active`: gold color → navy `#000744`, gold border → `#CC0031`
  - `.form-group input/select/textarea:focus`: gold border → `#000744`, gold shadow → navy-tinted
  - `.tag-pill` border/color/background: gold → `#000744`
  - `.custom-tag-row input:focus`: gold border → `#000744`
  - `.day-num` background: gold → `#000744`
  - `.itinerary-fields input/textarea:focus`: gold border → `#000744`
  - `.btn-publish` background: `var(--gold)` → `#CC0031`, hover → `#a80028`
  - `.bracket-row input:focus`: gold border → `#000744`, gold shadow → navy-tinted
  - Preview URL link inline style: `color:var(--gold)` → `color:#000744`
- Bonus fix: "Salva Configurazione Form" button upgraded from `class="btn-primary"` to `class="btn btn-primary"` (needed the `.btn` base class)

### Task 5 — admin/settings.php, admin/tags.php, admin/destinations.php

Each file: replaced bare text `<span class="admin-nav__logo">Viaggia Col Baffo</span>` with logo-icon structure:
`<span class="admin-nav__logo"><span class="logo-icon"><i class="fa-solid fa-compass"></i></span> Viaggia col Baffo</span>`

## Deviations from Plan

### Auto-fixed Issues

**1. [Rule 2 - Missing] edit-trip.php inline styles required in-place gold removal**
- **Found during:** Task 4
- **Issue:** The plan said "remove only gold CSS rules from inline style". The inline style block in edit-trip.php contains many non-gold rules essential for layout (tabs, forms, itinerary rows, sticky footer). Removing the entire block would break the edit form.
- **Fix:** Updated gold-specific values in-place; left non-gold rules intact. This is the correct approach since edit-trip has page-specific layout CSS not duplicated in admin.css.

**2. [Rule 1 - Bug] saveFormConfig button missing .btn base class**
- **Found during:** Task 4
- **Issue:** `<button class="btn-primary" onclick="saveFormConfig()">` was missing the `.btn` base class needed for inline-flex, gap, padding, font-weight styles.
- **Fix:** Changed to `class="btn btn-primary"`.

## Push Blocker (Pre-existing)

GitHub Push Protection blocked the push due to an Anthropic API key detected in `data/admin-config.json` at commit `c14a9c1` (a historical commit, not introduced by this task).

**To unblock:** Visit https://github.com/Zale-Marketing/viaggiacolbaffo/security/secret-scanning/unblock-secret/3Ai6yEGol5ShAcvwDvlkLDEpEq8 and allow the secret (or rotate/remove it from history).

The local commit `0f2cd15` is ready and correct.

## Self-Check

- admin/admin.css: zero "gold" occurrences — PASSED
- admin/index.php: logo-icon, admin-nav__visit, stat-card--red/green, btn-cta, className showToast, no inline style block — PASSED
- admin/login.php: .admin-login__logo div with compass icon, .admin-login__title div — PASSED
- admin/edit-trip.php: logo-icon in nav, btn-publish is red #CC0031, no gold references — PASSED
- admin/settings.php, tags.php, destinations.php: logo-icon in nav — PASSED
- Commit 0f2cd15 exists locally — PASSED

## Self-Check: PASSED
