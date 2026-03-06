---
phase: 2
slug: homepage
status: draft
nyquist_compliant: true
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

| Task ID | Plan | Wave | Requirement(s) | Test Type | Automated Command | Status |
|---------|------|------|----------------|-----------|-------------------|--------|
| 02-01-T1 | 01 | 1 | HOME-01, HOME-03, HOME-04 | php lint | `php -l includes/header.php` | ⬜ pending |
| 02-01-T2 | 01 | 1 | HOME-01, HOME-02, HOME-03, HOME-04, HOME-05, HOME-06, HOME-07, HOME-08, HOME-09 | grep count | `grep -c "has-hero\|\.hero\b\|\.urgency-bar\|\.trips-carousel\|\.dest-card\|\.why-grid\|\.testimonial-card\|\.b2b-banner\|\.site-footer" assets/css/style.css` | ⬜ pending |
| 02-02-T1 | 02 | 2 | HOME-01, HOME-02, HOME-03, HOME-04 | grep count | `grep -c "\.hero\b\|\.urgency-bar\|\.trips-carousel\|\.dest-card\|body\.has-hero" assets/css/style.css && grep -c "has-hero" includes/header.php` | ⬜ pending |
| 02-02-T2 | 02 | 2 | HOME-03 | php output check | `php -r "require 'includes/config.php'; require ROOT . '/includes/functions.php'; \$t=array_values(array_filter(load_trips(),fn(\$x)=>\$x['published']===true)); echo count(\$t).' active trips loaded\n'; if(count(\$t)===0){exit(1);}"` | ⬜ pending |
| 02-03-T1 | 03 | 3 | HOME-01–HOME-08 | php lint + grep | `php -l index.php && grep -c "hero__tagline\|urgency-bar\|trips-carousel\|dest-grid\|why-grid\|founder-grid\|testimonials-grid\|b2b-banner\|require.*footer" index.php` | ⬜ pending |
| 02-03-T2 | 03 | 3 | HOME-09 | php lint + grep | `php -l includes/footer.php && grep -c "site-footer\|site-footer__grid\|site-footer__bottom\|main\.js" includes/footer.php` | ⬜ pending |

*Status: ⬜ pending · ✅ green · ❌ red · ⚠️ flaky*

---

## Wave 0 Requirements

- [ ] `index.php` — main deliverable, does not exist yet (written as complete file in Plan 03 Task 1)
- [ ] `includes/footer.php` — requires replacement (currently placeholder); written in Plan 03 Task 2
- [ ] `includes/header.php` — requires `$hero_page` flag addition (Plan 01 Task 1)
- [ ] `assets/css/style.css` — requires hero, carousel, destination card, why-grid, testimonial, footer CSS additions (Plan 01 Task 2)

*No test framework installation needed — PHP lint is sufficient for syntax validation; visual review covers behavior.*

---

## Manual-Only Verifications

| Behavior | Requirement | Why Manual | Test Instructions |
|----------|-------------|------------|-------------------|
| Hero fills full viewport with cinematic photo, tagline, 2 CTAs — no logo inside hero | HOME-01 | No CSS test framework; visual layout check only | Load index.php in browser, verify hero is 100vh with photo and both buttons visible without scrolling; confirm no logo duplicated inside hero |
| Urgency bar appears below hero with correct text | HOME-02 | Static HTML content, visual check | Scroll just past hero fold, confirm red bar with "West America Aprile 2026 — Ultimi 5 posti disponibili" |
| Active trips grid shows correct cards with status pills | HOME-03 | UI rendering check | Verify trip cards appear with correct status pills (Ultimi Posti / Sold Out) |
| Destination card hover zoom + white border | HOME-04 | CSS transition, interactive | Hover over each destination card, confirm scale(1.05) and white border appear |
| Header transparent on hero, solid after scroll | All | JS scroll state + CSS specificity | Verify header semi-transparent at top; scroll down, verify solid white; scroll back up, verify transparent again |
| Footer 3-column layout on desktop, stacked on mobile | HOME-09 | Responsive layout | Review at 1280px (3-col) and 375px (1-col) viewports |
| WhatsApp link opens correct number | HOME-09 | External link, interactive | Click WhatsApp link, verify wa.me number is digits-only |

---

## Validation Sign-Off

- [x] All tasks have `<automated>` verify commands
- [x] Sampling continuity: no 3 consecutive tasks without automated verify
- [x] Wave 0 covers all MISSING references
- [x] No watch-mode flags
- [x] Feedback latency < 10s (lint pass)
- [x] `nyquist_compliant: true` set in frontmatter

**Approval:** pending
