---
phase: 1
slug: foundation
status: draft
nyquist_compliant: false
wave_0_complete: false
created: 2026-03-06
---

# Phase 1 — Validation Strategy

> Per-phase validation contract for feedback sampling during execution.

---

## Test Infrastructure

| Property | Value |
|----------|-------|
| **Framework** | None — PHP project, no test runner; validation via `php -l` + browser + curl |
| **Config file** | None — Wave 0 creates all files from scratch |
| **Quick run command** | `php -l <file>` |
| **Full suite command** | `php -l` on all .php files + manual browser check on design-preview.php |
| **Estimated runtime** | ~5 seconds for syntax checks; browser verification is manual |

---

## Sampling Rate

- **After every task commit:** Run `php -l <file>` on each modified PHP file
- **After every plan wave:** Deploy to SiteGround + run curl checks for INFRA-02 and INFRA-03
- **Before `/gsd:verify-work`:** Full suite must be green — all curl checks pass, design-preview.php renders correctly at mobile/tablet/desktop widths, PHP function output verified
- **Max feedback latency:** ~5 seconds for syntax checks

---

## Per-Task Verification Map

| Task ID | Plan | Wave | Requirement | Test Type | Automated Command | File Exists | Status |
|---------|------|------|-------------|-----------|-------------------|-------------|--------|
| 1-01-01 | 01 | 1 | INFRA-01 | smoke | GitHub Actions run completes without error | ❌ Wave 0 | ⬜ pending |
| 1-01-02 | 01 | 1 | INFRA-02 | smoke/manual | `curl -I https://nuovo.viaggiacolbaffo.com/viaggio/test` | ❌ Wave 0 | ⬜ pending |
| 1-01-03 | 01 | 1 | INFRA-03 | smoke | `curl -I https://nuovo.viaggiacolbaffo.com/data/trips.json` (expect 403) | ❌ Wave 0 | ⬜ pending |
| 1-01-04 | 01 | 1 | INFRA-04 | file-existence | `ls assets/img/.gitkeep` | ❌ Wave 0 | ⬜ pending |
| 1-01-05 | 01 | 1 | INFRA-05 | manual | Open README.md and verify required topics covered | ❌ Wave 0 | ⬜ pending |
| 1-02-01 | 02 | 2 | DATA-01 | php-lint + manual | `php -r "json_decode(file_get_contents('data/trips.json'), true);"` | ❌ Wave 0 | ⬜ pending |
| 1-02-02 | 02 | 2 | DATA-02 | php-lint + manual | `php -r "print_r(json_decode(file_get_contents('data/tags.json'), true));"` | ❌ Wave 0 | ⬜ pending |
| 1-02-03 | 02 | 2 | DATA-03 | manual | `php -l includes/functions.php` then call each function via CLI | ❌ Wave 0 | ⬜ pending |
| 1-02-04 | 02 | 2 | DATA-04 | manual | `php -r "require 'includes/functions.php'; print_r(get_trip_by_slug('west-america-aprile-2026'));"` | ❌ Wave 0 | ⬜ pending |
| 1-03-01 | 03 | 3 | DESIGN-01 | manual/visual | design-preview.php color swatches visible | ❌ Wave 0 | ⬜ pending |
| 1-03-02 | 03 | 3 | DESIGN-02 | manual/visual | Network tab in devtools confirms Playfair Display + Inter + Font Awesome load | ❌ Wave 0 | ⬜ pending |
| 1-03-03 | 03 | 3 | DESIGN-03 | manual/visual | Resize browser on design-preview.php — 1/2/3 columns at correct breakpoints | ❌ Wave 0 | ⬜ pending |
| 1-03-04 | 03 | 3 | DESIGN-04 | manual/visual | design-preview.php trip card shows continent badge, status pill, title, price, CTA | ❌ Wave 0 | ⬜ pending |
| 1-03-05 | 03 | 3 | DESIGN-05 | manual/visual | design-preview.php section headers show gold underline; hover transitions animate | ❌ Wave 0 | ⬜ pending |

*Status: ⬜ pending · ✅ green · ❌ red · ⚠️ flaky*

---

## Wave 0 Requirements

- [ ] `.github/workflows/deploy.yml` — INFRA-01 (deploy pipeline)
- [ ] `.htaccess` (root) — INFRA-02 (clean URLs, HTTPS, gzip, caching)
- [ ] `data/.htaccess` — INFRA-03 (block HTTP access to /data/)
- [ ] `assets/img/.gitkeep` — INFRA-04 (track empty directory)
- [ ] `README.md` — INFRA-05 (setup and ops documentation)
- [ ] `includes/config.php` — shared constants for all phases
- [ ] `includes/functions.php` — DATA-03 data access API
- [ ] `data/trips.json` — DATA-01, DATA-04 (full schema + West America + Japan)
- [ ] `data/tags.json` — DATA-02 (all tag slugs and labels)
- [ ] `assets/css/style.css` — DESIGN-01 through DESIGN-05
- [ ] `design-preview.php` — visual validation artifact for all DESIGN-* requirements
- [ ] `includes/header.php` — shared HTML head stub (used by design-preview.php)
- [ ] `includes/footer.php` — shared footer stub
- [ ] `assets/js/main.js` — empty stub (included by header/footer)

*All files are new — this is phase 1 from scratch.*

---

## Manual-Only Verifications

| Behavior | Requirement | Why Manual | Test Instructions |
|----------|-------------|------------|-------------------|
| FTP deploy triggers and files appear on SiteGround | INFRA-01 | Requires network access to SiteGround + GitHub Actions runtime | Push to main, watch Actions tab, then FTP into server and verify files present |
| Clean URL /viaggio/west-america resolves | INFRA-02 | Requires live deployment to verify .htaccess rewrite rules | `curl -I https://nuovo.viaggiacolbaffo.com/viaggio/west-america` after deploy |
| HTTPS redirect from HTTP | INFRA-02 | Requires live deployment | `curl -I http://nuovo.viaggiacolbaffo.com/` — expect 301 to HTTPS |
| /data/ returns 403 | INFRA-03 | Requires live deployment | `curl -I https://nuovo.viaggiacolbaffo.com/data/trips.json` — expect 403 |
| CSS grid breakpoints at 768px and 1024px | DESIGN-03 | Browser resize required | Open design-preview.php, resize browser window through 768px and 1024px thresholds |
| Fonts render correctly (Playfair Display + Inter) | DESIGN-02 | Visual inspection + network tab | Open design-preview.php, check font rendering and Network tab for font requests |
| Trip card visual appearance | DESIGN-04 | CSS visual regression requires human eye | Check design-preview.php: full-bleed image, gradient overlay, badges positioned correctly |
| West America trip content accuracy | DATA-04 | Content must match live site or be approved by Lorenzo | Verify slug, title, status, price_from, tags, itinerary structure in trips.json |

---

## Validation Sign-Off

- [ ] All tasks have `<automated>` verify or Wave 0 dependencies
- [ ] Sampling continuity: no 3 consecutive tasks without automated verify
- [ ] Wave 0 covers all MISSING references
- [ ] No watch-mode flags
- [ ] Feedback latency < 5s (php -l)
- [ ] `nyquist_compliant: true` set in frontmatter

**Approval:** pending
