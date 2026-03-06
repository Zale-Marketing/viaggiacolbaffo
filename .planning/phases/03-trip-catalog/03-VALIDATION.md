---
phase: 3
slug: trip-catalog
status: draft
nyquist_compliant: false
wave_0_complete: false
created: 2026-03-06
---

# Phase 3 — Validation Strategy

> Per-phase validation contract for feedback sampling during execution.

---

## Test Infrastructure

| Property | Value |
|----------|-------|
| **Framework** | None — grep-based structural checks + manual browser UAT |
| **Config file** | none |
| **Quick run command** | `grep -n "filter-pill\|trip-count\|applyFilters\|catalog-hero\|catalog-empty" viaggi.php` |
| **Full suite command** | Full grep suite (all 6 requirement checks below) + manual browser smoke test |
| **Estimated runtime** | ~5 seconds (grep) + ~2 minutes (manual smoke test) |

---

## Sampling Rate

- **After every task commit:** Run quick grep command above
- **After every plan wave:** Run full grep suite — all 6 checks must return matches
- **Before `/gsd:verify-work`:** Full suite must be green + manual browser smoke test
- **Max feedback latency:** 5 seconds (automated) / 2 minutes (manual smoke)

---

## Per-Task Verification Map

| Task ID | Plan | Wave | Requirement | Test Type | Automated Command | File Exists | Status |
|---------|------|------|-------------|-----------|-------------------|-------------|--------|
| 3-01-01 | 01 | 1 | CATALOG-01 | grep/content | `grep -n "catalog-hero" viaggi.php` | ❌ Wave 0 | ⬜ pending |
| 3-01-02 | 01 | 1 | CATALOG-02 | grep/content | `grep -c "filter-pill" viaggi.php` | ❌ Wave 0 | ⬜ pending |
| 3-01-03 | 01 | 1 | CATALOG-03 | grep/content | `grep -n "trip-count" viaggi.php` | ❌ Wave 0 | ⬜ pending |
| 3-01-04 | 01 | 1 | CATALOG-04 | grep/content | `grep -n "trip-card-wrapper" viaggi.php` | ❌ Wave 0 | ⬜ pending |
| 3-01-05 | 01 | 1 | CATALOG-05 | grep/content | `grep -n "applyFilters\|replaceState\|URLSearchParams" viaggi.php` | ❌ Wave 0 | ⬜ pending |
| 3-01-06 | 01 | 1 | CATALOG-06 | grep/content | `grep -n "catalog-empty\|TALLY_CATALOG_URL" viaggi.php` | ❌ Wave 0 | ⬜ pending |

*Status: ⬜ pending · ✅ green · ❌ red · ⚠️ flaky*

---

## Wave 0 Requirements

- [ ] `viaggi.php` — does not exist yet; created in Wave 1, Task 1
- [ ] Phase 3 CSS section in `assets/css/style.css` — does not exist yet; appended in Wave 1, Task 2

*Note: No test framework to install — project uses manual verification + grep-based structural checks. All Wave 0 items are created as part of Wave 1 execution.*

---

## Manual-Only Verifications

| Behavior | Requirement | Why Manual | Test Instructions |
|----------|-------------|------------|-------------------|
| Clicking "America" filters grid to only American trips | CATALOG-02 | JS DOM filtering cannot be tested with grep | Open viaggi.php in browser → click "America" pill → verify only American trips visible + count updates |
| AND logic: tag + continent filter narrows results | CATALOG-03 | Multi-filter interaction requires browser | Apply continent filter → apply tag filter → verify AND intersection displayed |
| CSS fade transitions on count/empty state | CATALOG-03/06 | Visual animation, not inspectable by grep | Apply filters until empty state appears — verify smooth CSS opacity transition |
| URL deep-link pre-applies filters on load | CATALOG-04 (CATALOG-05) | Requires live browser navigation | Navigate to `viaggi.php?continent=america&tag=famiglia` → verify filters pre-applied on load |
| Sticky filter bar clears site header | CATALOG-02 | Visual layout behavior | Scroll past hero → verify filter bar sticks at top without overlapping site header |
| Tally iframe renders in empty state | CATALOG-06 | Requires configured Tally URL or graceful fallback | Set TALLY_CATALOG_URL, apply non-matching filters → verify iframe appears; with empty URL → verify WhatsApp fallback |

---

## Validation Sign-Off

- [ ] All tasks have `<automated>` verify or Wave 0 dependencies
- [ ] Sampling continuity: no 3 consecutive tasks without automated verify
- [ ] Wave 0 covers all MISSING references
- [ ] No watch-mode flags
- [ ] Feedback latency < 300s
- [ ] `nyquist_compliant: true` set in frontmatter

**Approval:** pending
