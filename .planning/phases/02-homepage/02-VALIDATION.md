---
phase: 2
slug: homepage
status: draft
nyquist_compliant: false
wave_0_complete: false
created: 2026-03-06
---

# Phase 2 — Validation Strategy

> Per-phase validation contract for feedback sampling during execution.

---

## Test Infrastructure

| Property | Value |
|----------|-------|
| **Framework** | None (PHP/HTML/CSS project — no test framework installed) |
| **Config file** | none |
| **Quick run command** | `php -l index.php && php -l includes/footer.php && php -l includes/header.php` |
| **Full suite command** | PHP lint on all modified files + visual browser review checklist |
| **Estimated runtime** | ~5 seconds (lint) + manual visual review |

---

## Sampling Rate

- **After every task commit:** Run `php -l index.php && php -l includes/footer.php && php -l includes/header.php`
- **After every plan wave:** Run full PHP lint on all modified files + visual browser review
- **Before `/gsd:verify-work`:** All HOME-0X requirements visually confirmed in browser
- **Max feedback latency:** ~5 seconds (lint); visual review per wave

---

## Per-Task Verification Map

| Task ID | Plan | Wave | Requirement | Test Type | Automated Command | File Exists | Status |
|---------|------|------|-------------|-----------|-------------------|-------------|--------|
| 2-01-01 | 01 | 1 | HOME-01 | visual + lint | `php -l index.php` | ❌ W0 | ⬜ pending |
| 2-01-02 | 01 | 1 | HOME-02 | visual + lint | `php -l index.php` | ❌ W0 | ⬜ pending |
| 2-01-03 | 01 | 1 | HOME-03 | php output check | `php -r "require 'includes/config.php'; require 'includes/functions.php'; $t=array_filter(load_trips(),fn($x)=>$x['published']===true); echo count($t).' active trips\n';"` | ❌ W0 | ⬜ pending |
| 2-01-04 | 01 | 1 | HOME-04 | visual + lint | `php -l index.php` | ❌ W0 | ⬜ pending |
| 2-01-05 | 01 | 1 | HOME-05 | visual + lint | `php -l index.php` | ❌ W0 | ⬜ pending |
| 2-01-06 | 01 | 1 | HOME-06 | visual + lint | `php -l index.php` | ❌ W0 | ⬜ pending |
| 2-01-07 | 01 | 1 | HOME-07 | visual + lint | `php -l index.php` | ❌ W0 | ⬜ pending |
| 2-01-08 | 01 | 1 | HOME-08 | visual + lint | `php -l index.php` | ❌ W0 | ⬜ pending |
| 2-01-09 | 01 | 2 | HOME-09 | visual + lint | `php -l index.php && php -l includes/footer.php` | ❌ W0 | ⬜ pending |

*Status: ⬜ pending · ✅ green · ❌ red · ⚠️ flaky*

---

## Wave 0 Requirements

- [ ] `index.php` — main deliverable, does not exist yet
- [ ] `includes/footer.php` — requires replacement (currently placeholder)
- [ ] `includes/header.php` — requires `$hero_page` flag addition
- [ ] `assets/css/style.css` — requires hero, carousel, destination card, why-grid, testimonial, footer CSS additions

*No test framework installation needed — PHP lint is sufficient for syntax validation; visual review covers behavior.*

---

## Manual-Only Verifications

| Behavior | Requirement | Why Manual | Test Instructions |
|----------|-------------|------------|-------------------|
| Hero fills full viewport with cinematic photo, logo, tagline, 2 CTAs | HOME-01 | No CSS test framework; visual layout check only | Load index.php in browser, verify hero is 100vh with photo and both buttons visible without scrolling |
| Urgency bar appears below hero with correct text | HOME-02 | Static HTML content, visual check | Scroll just past hero fold, confirm red bar with "West America Aprile 2026 — Ultimi 5 posti disponibili" |
| Active trips grid shows correct cards with status pills | HOME-03 | UI rendering check | Verify trip cards appear with correct status pills (Ultimi Posti / Sold Out) |
| Destination card hover zoom + white border | HOME-04 | CSS transition, interactive | Hover over each destination card, confirm scale(1.05) and white border appear |
| Header transparent on hero, solid after scroll | All | JS scroll state + CSS specificity | Verify header semi-transparent at top; scroll down, verify solid white; scroll back up, verify transparent again |
| Footer 3-column layout on desktop, stacked on mobile | HOME-09 | Responsive layout | Review at 1280px (3-col) and 375px (1-col) viewports |
| WhatsApp link opens correct number | HOME-09 | External link, interactive | Click WhatsApp link, verify wa.me number is digits-only |

---

## Validation Sign-Off

- [ ] All tasks have `<automated>` verify or Wave 0 dependencies
- [ ] Sampling continuity: no 3 consecutive tasks without automated verify
- [ ] Wave 0 covers all MISSING references
- [ ] No watch-mode flags
- [ ] Feedback latency < 10s (lint pass)
- [ ] `nyquist_compliant: true` set in frontmatter

**Approval:** pending
