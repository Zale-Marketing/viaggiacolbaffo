---
phase: 6
slug: admin-panel
status: draft
nyquist_compliant: false
wave_0_complete: false
created: 2026-03-06
---

# Phase 6 — Validation Strategy

> Per-phase validation contract for feedback sampling during execution.

---

## Test Infrastructure

| Property | Value |
|----------|-------|
| **Framework** | None — PHP CLI unavailable on SiteGround shared hosting (confirmed STATE.md). Manual browser testing + Bash content inspection. |
| **Config file** | N/A |
| **Quick run command** | `grep -c "..." /path/to/file.php` (content inspection) |
| **Full suite command** | Human-verify checklist (matching Phase 5 pattern) |
| **Estimated runtime** | ~5 seconds (grep) + manual browser time |

---

## Sampling Rate

- **After every task commit:** Run content inspection grep confirming key function names / strings exist
- **After every plan wave:** Human browser check of that wave's pages
- **Before `/gsd:verify-work`:** Full human-verify checklist must pass
- **Max feedback latency:** ~30 seconds (grep) + browser

---

## Per-Task Verification Map

| Task ID | Plan | Wave | Requirement | Test Type | Automated Command | File Exists | Status |
|---------|------|------|-------------|-----------|-------------------|-------------|--------|
| 6-login | 01 | 1 | ADMIN-01 | manual+grep | `grep -c "session_start\|password_verify" admin/login.php` | ❌ W0 | ⬜ pending |
| 6-dashboard | 01 | 1 | ADMIN-02 | manual+grep | `grep -c "load_trips\|stats" admin/index.php` | ❌ W0 | ⬜ pending |
| 6-edit-basic | 02 | 2 | ADMIN-03 | manual+grep | `grep -c "save_trip\|trips\.json" admin/edit-trip.php` | ❌ W0 | ⬜ pending |
| 6-hero-preview | 02 | 2 | ADMIN-04 | manual+grep | `grep -c "preview\|heroUrl\|thumbnail" admin/edit-trip.php` | ❌ W0 | ⬜ pending |
| 6-shortdesc | 02 | 2 | ADMIN-05 | manual+grep | `grep -c "maxlength\|charCount\|remaining" admin/edit-trip.php` | ❌ W0 | ⬜ pending |
| 6-itinerary-dnd | 02 | 2 | ADMIN-06 | manual+grep | `grep -c "dragstart\|dragover\|drop" admin/edit-trip.php` | ❌ W0 | ⬜ pending |
| 6-includes-excludes | 02 | 2 | ADMIN-07 | manual+grep | `grep -c "explode.*newline\|implode\|includes\|excludes" admin/edit-trip.php` | ❌ W0 | ⬜ pending |
| 6-tags | 02 | 2 | ADMIN-08 | manual+grep | `grep -c "tag.*pill\|custom.*tag\|hidden.*tags" admin/edit-trip.php` | ❌ W0 | ⬜ pending |
| 6-ai-desc | 03 | 3 | ADMIN-09 | manual+grep | `grep -c "api.anthropic.com\|anthropic-version" api/generate-form.php` | ❌ W0 (modify) | ⬜ pending |
| 6-publish | 02 | 2 | ADMIN-10 | manual+grep | `grep -c "published\|preview_token\|Pubblica\|Bozza" admin/edit-trip.php` | ❌ W0 | ⬜ pending |
| 6-settings | 03 | 3 | ADMIN-11 | manual+grep | `grep -c "admin-config\.json" includes/config.php` | ❌ W0 (modify) | ⬜ pending |

*Status: ⬜ pending · ✅ green · ❌ red · ⚠️ flaky*

---

## Wave 0 Requirements

- [ ] `admin/login.php` — covers ADMIN-01
- [ ] `admin/index.php` — covers ADMIN-02
- [ ] `admin/edit-trip.php` — covers ADMIN-03 through ADMIN-10
- [ ] `admin/settings.php` — covers ADMIN-11
- [ ] `admin/tags.php` — tag management UI
- [ ] `admin/destinations.php` — destination editing UI
- [ ] `admin/admin.css` — admin stylesheet
- [ ] `data/admin-config.json` — created by settings.php on first save
- [ ] `data/destinations.json` — migrated from includes/destinations-data.php

*All files are new (Wave 0 creates them). Existing infrastructure does not cover phase requirements.*

---

## Manual-Only Verifications

| Behavior | Requirement | Why Manual | Test Instructions |
|----------|-------------|------------|-------------------|
| Login form renders; wrong password rejected; session set on success | ADMIN-01 | PHP CLI unavailable; session behavior requires browser | Visit /admin, enter wrong PW, verify redirect; enter correct PW, verify dashboard loads |
| Dashboard lists all trips with correct counts | ADMIN-02 | Requires rendered HTML in browser | Visit /admin, verify trip rows match trips.json count |
| Edit form saves all fields to trips.json | ADMIN-03 | JSON write requires actual form submission | Edit a trip, save, grep trips.json for updated values |
| Hero image URL → thumbnail preview | ADMIN-04 | img src change requires browser render | Paste URL in hero field, verify thumbnail appears immediately |
| Short description char counter | ADMIN-05 | JS counter requires browser interaction | Type in short_desc, verify countdown updates |
| Itinerary drag-and-drop reorder | ADMIN-06 | Drag events require browser | Drag itinerary row, verify day numbers renumber |
| Includes/excludes save as arrays | ADMIN-07 | Requires form submission + JSON inspection | Enter multi-line value, save, grep trips.json |
| Tag pills toggle; custom tags | ADMIN-08 | JS interaction requires browser | Click tag pills, add custom tag, save, verify trips.json |
| Anthropic API returns AI description | ADMIN-09 | API call requires network + valid key | Click AI generate, verify text populates description field |
| Pubblica/Bozza toggles published state | ADMIN-10 | Requires form submission + homepage check | Publish trip, verify visible on homepage; unpublish, verify hidden |
| Settings form saves config overlay | ADMIN-11 | Requires file write + page reload | Change WhatsApp number in settings, save, verify config.php reads new value |

---

## Validation Sign-Off

- [ ] All tasks have `<automated>` verify or Wave 0 dependencies
- [ ] Sampling continuity: no 3 consecutive tasks without automated verify
- [ ] Wave 0 covers all MISSING references
- [ ] No watch-mode flags
- [ ] Feedback latency < 30s
- [ ] `nyquist_compliant: true` set in frontmatter

**Approval:** pending
