---
phase: 4
slug: trip-detail-quote-form
status: draft
nyquist_compliant: false
wave_0_complete: false
created: 2026-03-06
---

# Phase 4 — Validation Strategy

> Per-phase validation contract for feedback sampling during execution.

---

## Test Infrastructure

| Property | Value |
|----------|-------|
| **Framework** | Manual browser testing (no automated test framework in project) |
| **Config file** | none — SiteGround shared hosting, no test runner configured |
| **Quick run command** | `php -l {file}.php` (syntax check after each PHP file created) |
| **Full suite command** | Manual browser walkthrough of `/viaggio/west-america-aprile-2026` |
| **Estimated runtime** | ~10 minutes (full manual checklist) |

---

## Sampling Rate

- **After every task commit:** Run `php -l {file}.php` for any PHP file modified
- **After every plan wave:** Full manual browser walkthrough of trip detail page
- **Before `/gsd:verify-work`:** All 16 requirements manually verified
- **Max feedback latency:** ~2 minutes (syntax check) / ~10 minutes (full wave review)

---

## Per-Task Verification Map

| Task ID | Plan | Wave | Requirement | Test Type | Automated Command | File Exists | Status |
|---------|------|------|-------------|-----------|-------------------|-------------|--------|
| 4-01-01 | 01 | 1 | TRIP-01 | manual + syntax | `php -l viaggio.php` | ❌ Wave 0 | ⬜ pending |
| 4-01-02 | 01 | 1 | TRIP-02 | manual-visual | — | ❌ Wave 0 | ⬜ pending |
| 4-01-03 | 01 | 1 | TRIP-03 | manual-visual | — | ❌ Wave 0 | ⬜ pending |
| 4-01-04 | 01 | 1 | TRIP-04 | manual-visual | — | ❌ Wave 0 | ⬜ pending |
| 4-01-05 | 01 | 1 | TRIP-05 | manual-interactive | — | ❌ Wave 0 | ⬜ pending |
| 4-01-06 | 01 | 1 | TRIP-06 | manual-interactive | — | ❌ Wave 0 | ⬜ pending |
| 4-01-07 | 01 | 1 | TRIP-07 | manual-visual | — | ❌ Wave 0 | ⬜ pending |
| 4-01-08 | 01 | 1 | TRIP-08 | manual-interactive | — | ❌ Wave 0 | ⬜ pending |
| 4-01-09 | 01 | 1 | TRIP-09 | manual-visual | — | ❌ Wave 0 | ⬜ pending |
| 4-01-10 | 01 | 1 | TRIP-10 | manual-visual | — | ❌ Wave 0 | ⬜ pending |
| 4-02-01 | 02 | 1 | FORM-01 | manual-visual | — | ❌ Wave 0 | ⬜ pending |
| 4-02-02 | 02 | 1 | FORM-02 | manual-interactive | — | ❌ Wave 0 | ⬜ pending |
| 4-02-03 | 02 | 1 | FORM-03 | manual-interactive | — | ❌ Wave 0 | ⬜ pending |
| 4-02-04 | 02 | 2 | FORM-04 | manual + syntax | `php -l api/submit-form.php` | ❌ Wave 0 | ⬜ pending |
| 4-02-05 | 02 | 1 | FORM-05 | manual-visual | — | ❌ Wave 0 | ⬜ pending |
| 4-03-01 | 03 | 2 | FORM-06 | manual + curl | `php -l api/generate-form.php` + `curl -X POST ...` | ❌ Wave 0 | ⬜ pending |

*Status: ⬜ pending · ✅ green · ❌ red · ⚠️ flaky*

---

## Wave 0 Requirements

- [ ] `viaggio.php` — primary file, does not exist yet — covers all TRIP-* requirements
- [ ] `api/submit-form.php` — webhook proxy, does not exist — covers FORM-04
- [ ] `api/generate-form.php` — AI endpoint, does not exist — covers FORM-06
- [ ] `data/trips.json` — update West America `form_config` with pricing constants — covers FORM-01, FORM-02

*All Wave 0 items are created by the plans themselves — no separate test scaffolding needed.*

---

## Manual-Only Verifications

| Behavior | Requirement | Why Manual | Test Instructions |
|----------|-------------|------------|-------------------|
| Slug lookup returns correct trip; invalid slug shows 404 | TRIP-01 | PHP server-side routing, no unit test framework | Load `/viaggio/west-america-aprile-2026`, verify data loads; load `/viaggio/nonexistent`, verify 404 |
| Full-viewport hero renders | TRIP-02 | Visual layout | Check hero fills viewport, overlay text correct, CTA visible |
| Sticky top bar appears on scroll | TRIP-03 | CSS/JS scroll behavior | Scroll past hero, verify sticky nav appears with correct background |
| Highlights bar correctness | TRIP-04 | Visual, data-driven | Verify date, duration, price_from, status pill match trips.json |
| Tab nav scrolls to section | TRIP-05 | Interactive JS behavior | Click each tab, verify smooth scroll to correct section with offset |
| Accordion single-open, Day 1 default | TRIP-06 | Interactive JS behavior | Load page — Day 1 open; click Day 2 — Day 1 closes, Day 2 opens |
| Includes/excludes two-column layout | TRIP-07 | Visual layout | Verify ✓ icons for included, ✗ for excluded, two-column on desktop |
| Gallery masonry + lightbox | TRIP-08 | Interactive JS behavior | Click thumbnail — lightbox opens; arrow keys navigate; Esc closes |
| Tag pills correct links | TRIP-09 | Visual + navigation | Verify tags render, click links go to catalog with correct filter |
| Related trips (same continent, limit 3) | TRIP-10 | Visual, data-driven | Verify ≤3 trips shown, all same continent as West America |
| Quote form renders from form_config | FORM-01 | Visual, data-driven | Verify room type select, addons checkboxes match trips.json form_config |
| Live price updates on input change | FORM-02 | Interactive JS behavior | Change room type, adult count — verify price box updates in real time |
| B2B toggle + agency code validation | FORM-03 | Interactive JS behavior | Select "Agenzia" — extra fields appear; enter valid code — agency fields appear |
| Webhook form submission | FORM-04 | Network + interactive | Submit form — verify fetch POST fires, success message replaces form |
| WhatsApp button correct URL | FORM-05 | Visual + URL check | Verify wa.me link includes correct phone number |
| AI generate-form.php returns valid JSON | FORM-06 | curl test | `curl -X POST -H "Content-Type: application/json" -d '{"description":"test"}' http://localhost/api/generate-form.php` — verify valid form_config JSON |

---

## Validation Sign-Off

- [ ] All tasks have `<automated>` verify or Wave 0 dependencies
- [ ] Sampling continuity: no 3 consecutive tasks without automated verify
- [ ] Wave 0 covers all MISSING references
- [ ] No watch-mode flags
- [ ] Feedback latency < 10 minutes (manual) / 2 minutes (syntax check)
- [ ] `nyquist_compliant: true` set in frontmatter

**Approval:** pending
