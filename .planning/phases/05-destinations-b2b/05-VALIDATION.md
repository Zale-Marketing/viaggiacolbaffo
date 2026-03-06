---
phase: 5
slug: destinations-b2b
status: draft
nyquist_compliant: false
wave_0_complete: false
created: 2026-03-06
---

# Phase 5 — Validation Strategy

> Per-phase validation contract for feedback sampling during execution.

---

## Test Infrastructure

| Property | Value |
|----------|-------|
| **Framework** | Manual browser + curl (no automated test framework — PHP static site) |
| **Config file** | none |
| **Quick run command** | `curl -s -o /dev/null -w "%{http_code}" http://localhost/destinazione/america` |
| **Full suite command** | See Per-Task Verification Map manual checks |
| **Estimated runtime** | ~2 minutes (manual) |

---

## Sampling Rate

- **After every task commit:** Run quick curl check on affected slug
- **After every plan wave:** Run full manual checklist
- **Before `/gsd:verify-work`:** All manual checks must pass
- **Max feedback latency:** ~120 seconds

---

## Per-Task Verification Map

| Task ID | Plan | Wave | Requirement | Test Type | Automated Command | File Exists | Status |
|---------|------|------|-------------|-----------|-------------------|-------------|--------|
| 5-01-01 | 01 | 1 | DEST-01 | curl | `curl -s -o /dev/null -w "%{http_code}" http://localhost/destinazione/america` | ❌ W0 | ⬜ pending |
| 5-01-02 | 01 | 1 | DEST-02 | curl | `curl -s -o /dev/null -w "%{http_code}" http://localhost/destinazione/asia` | ❌ W0 | ⬜ pending |
| 5-01-03 | 01 | 1 | DEST-03 | curl | `curl -s http://localhost/destinazione/america \| grep -c "curiosita"` | ❌ W0 | ⬜ pending |
| 5-01-04 | 01 | 1 | DEST-07 | curl | `curl -s -o /dev/null -w "%{http_code}" http://localhost/destinazione/slug-inesistente` | ❌ W0 | ⬜ pending |
| 5-02-01 | 02 | 2 | DEST-06 | manual | Browser: destination with trips shows cards | ❌ W0 | ⬜ pending |
| 5-02-02 | 02 | 2 | DEST-06 | manual | Browser: destination without trips shows waitlist form | ❌ W0 | ⬜ pending |
| 5-03-01 | 03 | 3 | B2B-01 | curl | `curl -s -o /dev/null -w "%{http_code}" http://localhost/b2b` | ❌ W0 | ⬜ pending |
| 5-03-02 | 03 | 3 | B2B-03 | manual | Browser: Tally form loads in B2B page | ❌ W0 | ⬜ pending |

*Status: ⬜ pending · ✅ green · ❌ red · ⚠️ flaky*

---

## Wave 0 Requirements

- [ ] Verify `.htaccess` rule for `destinazione/([^/]+)` is active
- [ ] Confirm `get_trips_by_continent()` in `trips.php` is accessible

*Existing infrastructure: Apache/.htaccess routing already present. No new framework needed.*

---

## Manual-Only Verifications

| Behavior | Requirement | Why Manual | Test Instructions |
|----------|-------------|------------|-------------------|
| Hero image loads per destination | DEST-01 | Visual check | Open each destination URL, verify hero image is destination-specific |
| Editorial intro text displays | DEST-02 | Content quality | Read intro paragraph per destination |
| Practical info boxes render | DEST-03 | Layout check | Verify info boxes (visa, currency, language, climate) appear |
| Sub-destination cards show | DEST-04 | Visual check | Verify sub-destination grid renders with images |
| Curiosity facts render | DEST-05 | Content check | Verify facts section appears at bottom |
| Trip cards show when trips exist | DEST-06 | Data-dependent | Use america slug (has west-america-aprile-2026) — verify cards appear |
| Waitlist form shows when no trips | DEST-06 | Data-dependent | Use a slug with no published trips — verify form appears |
| B2B value props render | B2B-01 | Layout check | Open /b2b, verify value proposition section |
| B2B how-it-works steps | B2B-02 | Layout check | Verify numbered steps section renders |
| Tally form embeds correctly | B2B-03 | Third-party | Verify Tally iframe/widget loads and is interactive |
| No client contact guarantee visible | B2B-04 | Content check | Verify written guarantee copy present |
| Commission structure shown | B2B-05 | Content check | Verify commission details visible |
| Agency registration CTA | B2B-06 | UX check | Verify CTA button/section present |
| 404 for invalid slug | DEST-07 | HTTP check | `curl -s -o /dev/null -w "%{http_code}" http://localhost/destinazione/nonexistent` → 404 |

---

## Validation Sign-Off

- [ ] All tasks have `<automated>` verify or Wave 0 dependencies
- [ ] Sampling continuity: no 3 consecutive tasks without automated verify
- [ ] Wave 0 covers all MISSING references
- [ ] No watch-mode flags
- [ ] Feedback latency < 120s
- [ ] `nyquist_compliant: true` set in frontmatter

**Approval:** pending
