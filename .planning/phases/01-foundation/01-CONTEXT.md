# Phase 1: Foundation - Context

**Gathered:** 2026-03-06
**Status:** Ready for planning

<domain>
## Phase Boundary

Set up the PHP project structure (directory layout, .htaccess, GitHub Actions FTP deploy), define the CSS design system (CSS variables, typography, card components, responsive grid), and create the JSON data layer (trips.json, tags.json, functions.php with PHP utility functions) with realistic sample data. All other phases build on this — no rework expected.

</domain>

<decisions>
## Implementation Decisions

### GitHub Actions Deploy
- FTP host: `ftp.viaggiacolbaffo.com`
- Credentials stored as GitHub Secrets: `FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD` (already created in repo settings)
- Deploy target path: `/nuovo.viaggiacolbaffo.com/public_html/`
- Deploy trigger: push to `main` branch
- Exclude from deploy: `.git/`, `.claude/`, `.planning/`, `README.md` — only ship actual site files

### Directory Structure
- Public PHP pages in root: `index.php`, `viaggi.php`, `viaggio.php`, `destinazione.php`, `agenzie.php`
- Shared PHP includes in `includes/`: `config.php`, `functions.php`, `header.php`, `footer.php`
- Admin panel in `admin/`
- API endpoints in `api/`
- Data files in `data/` (trips.json, tags.json — .htaccess-protected)
- Styles: single `assets/css/style.css` with organized sections (variables, base, components, layout, pages)
- JavaScript: single `assets/js/main.js` for shared behaviors; page-specific JS as inline scripts where needed
- Images: `assets/img/` with `.gitkeep` (Unsplash URLs in data, no local image uploads)
- Design preview: standalone `design-preview.php` in root (can be deleted or .htaccess-protected after Phase 1)

### Sample Data
- Pull real content from the live WordPress trip page (viaggiacolbaffo.com/west-america-aprile-2026/) for the West America Aprile 2026 trip — real itinerary, description, dates, price
- Add a second placeholder trip (e.g. a sold-out Japan/Asia trip) to enable catalog filtering, status pills, and empty state testing from Phase 3 onward
- `form_config` on the West America trip should be realistic — include room type options, add-ons, and price calculation fields matching the actual trip
- West America status: `ultimi-posti`, price_from: 3490, published: true
- Tags for West America: `[america, road-trip, aprile, coppia, famiglia, avventura, parchi-naturali]`

### config.php
- Admin password: `Admin2025!` (plain text, with comment instructing Lorenzo to change it)
- WhatsApp number: placeholder (`+39 XXX XXXXXXX`) — Lorenzo fills in `config.php`
- Tally URLs: empty string placeholders (`TALLY_CATALOG_URL`, `TALLY_B2B_URL`) with comments — Lorenzo fills when Tally forms are created
- OpenAI API key: empty string by default (optional feature — AI form generator in Phase 4)
- Default webhook URL: empty string placeholder

### Claude's Discretion
- Exact section organization within style.css
- .htaccess gzip and caching headers implementation detail
- Exact structure of the design-preview.php page (as long as it demonstrates all tokens)
- How to handle the Japan placeholder trip content (invent plausible data)
- form_config structure design (infer from the actual trip requirements)

</decisions>

<code_context>
## Existing Code Insights

### Reusable Assets
- None yet — this is Phase 1, building from scratch

### Established Patterns
- PHP + vanilla JS only — no framework, no npm, no build step
- No external CSS libraries — pure custom CSS
- Font Awesome 6 CDN for icons only
- Google Fonts CDN for Playfair Display + Inter
- Unsplash direct URLs for all placeholder images

### Integration Points
- All future phases (2–6) include `includes/config.php` and `includes/functions.php`
- All public pages share `includes/header.php` and `includes/footer.php`
- `data/trips.json` and `data/tags.json` are the sole data store read by all phases
- GitHub Actions workflow (`deploy.yml`) is the deploy mechanism used forever after

</code_context>

<specifics>
## Specific Ideas

- The real West America trip page at viaggiacolbaffo.com/west-america-aprile-2026/ should be referenced to populate trips.json accurately
- The design preview page is a validation tool, not a user-facing page — it can be .htaccess-protected or deleted after Phase 1 sign-off
- GitHub Secrets (`FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`) are already configured in the repo — the workflow just needs to reference them correctly

</specifics>

<deferred>
## Deferred Ideas

None — discussion stayed within phase scope.

</deferred>

---

*Phase: 01-foundation*
*Context gathered: 2026-03-06*
