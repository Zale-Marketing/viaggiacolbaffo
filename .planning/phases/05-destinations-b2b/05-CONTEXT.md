# Phase 5: Destinations + B2B - Context

**Gathered:** 2026-03-06
**Status:** Ready for planning

<domain>
## Phase Boundary

Build `destinazione.php` (6 editorial destination pages: america, asia, europa, africa, oceania, medio-oriente) and `agenzie.php` (B2B agency partnership page). Destination pages exist independently of trip availability — they make the site look Boscolo-tier even with 0 trips. The B2B page enables agencies to register as partners. Admin panel for managing commission rates per trip is Phase 6.

</domain>

<decisions>
## Implementation Decisions

### Editorial content

- Claude writes solid, emotionally compelling Italian editorial copy for all 6 destinations — good-to-ship quality, Lorenzo refines later if desired
- 3 paragraphs per destination in `includes/destinations-data.php`
- All 6 destinations get full, accurate practical info boxes (currency, language, best season, timezone, visa requirements) — not placeholders
- Curiosità section: 3 facts per destination with Font Awesome 6 icons (fa-*), title, text, gold left accent border

### Data structure

- Single `includes/destinations-data.php` file — one PHP associative array keyed by slug
- Each slug entry contains: hero_image, intro_paragraphs (array of 3), practical_info (array of boxes), see_also (4 sub-destinations), curiosita (3 facts)
- Sub-destination data (name, photo URL, 2-line description) lives inside this same file

### Sub-destination cards ("Cosa Vedere")

- Visual style: vertical photo cards — full-bleed Unsplash photo top, name in Playfair + 2-line description below on dark card
- Exactly 4 per destination (4-col desktop / 2-col tablet / 1-col mobile grid)
- Cards are clickable: link to `viaggi.php?continent=[slug]` (filters catalog to that continent)
- Reuses visual DNA of `.trip-card` — consistent with the site's established card pattern

### B2B page (agenzie.php)

- Tone: warm partnership / inviting — "Cresciamo insieme" — premium but human, matches Lorenzo's personal brand
- Commission language: "commissioni competitive" — no hardcoded percentage. Per-trip commission rates are managed by Lorenzo per trip (Phase 6 admin). Agenzie.php just says "Guadagna una commissione su ogni prenotazione" with a note that the exact % is per trip
- Written guarantee ("I tuoi clienti restano TUOI"): replicate the copy from the current live site at viaggiacolbaffo.com/diventa-agenzia-partner/
- Agency testimonial (B2B-06): fictional Italian travel agency name + agent first name — realistic, Lorenzo replaces with a real one post-launch
- Agency registration form: Tally embed (TALLY_B2B_URL from config.php); if URL is empty, show fallback "Contattaci su WhatsApp" button using WHATSAPP_B2B_FALLBACK

### Waitlist form (DEST-07)

- When a destination has 0 published trips: show sold-out dark box with name + email + phone form
- Custom PHP form — POSTs server-side via cURL to WAITLIST_WEBHOOK_URL (same pattern as api/submit-form.php)
- Fields: nome, email, telefono
- Success/error message inline, no page reload (AJAX fetch or PHP redirect)
- Submission includes destination name/slug so Lorenzo knows which destination the lead is for

### New config.php constants

- `WAITLIST_WEBHOOK_URL` — POST target for destination sold-out waitlist form (empty default)
- `TALLY_B2B_URL` — Tally embed URL for agency registration on agenzie.php (empty default)
- `WHATSAPP_B2B_FALLBACK` — WhatsApp link shown on agenzie.php if TALLY_B2B_URL is empty

### Claude's Discretion

- Exact Unsplash photo URLs for destination heroes and sub-destination cards
- Specific sub-destination names for each of the 6 destinations (e.g. America: New York, Grand Canyon, California, Las Vegas — or similar)
- Italian editorial copy content and phrasing
- Practical info values (real-world accurate data)
- Curiosità fact content per destination
- B2B page section copywriting (headings, sublines, how-it-works steps)
- Waitlist form AJAX vs PHP redirect approach

</decisions>

<code_context>
## Existing Code Insights

### Reusable Assets

- `$hero_page = true` + `includes/header.php`: transparent-to-scrolled header — apply to both `destinazione.php` and `agenzie.php`
- `.trip-card` + `.trip-grid`: sub-destination cards reuse this visual DNA (same dark card, photo, text pattern)
- `.section`, `.section--dark`, `.container`, `.section-header`, `.section-header__title`: layout utilities for all sections
- `.btn--gold`, `.btn--outline-white`: CTA buttons
- `get_trips_by_continent(string $continent): array` in `includes/functions.php`: returns trips filtered by continent — use for DEST-07 check
- `includes/footer.php`: standard include
- CSS variables: `var(--gold)`, `var(--dark-card)`, `var(--dark)`, `var(--primary)` (#000744 navy), `var(--transition)`

### Established Patterns

- PHP includes: `config.php` + `functions.php` first, then `header.php` / `footer.php`
- Single `assets/css/style.css` — Phase 5 CSS appended as a new block
- Page-specific JS inline in the PHP file (no separate .js files)
- Unsplash direct URLs for all photos
- Config constants for external service URLs (Tally, webhooks) — check `defined()` before use
- cURL POST pattern for webhook submission already in `api/submit-form.php` — replicate for waitlist form

### Integration Points

- `destinazione.php` reads `?slug=` (or clean URL via .htaccess), requires `includes/destinations-data.php`
- Destination card links on homepage (`destinazione.php?slug=america` etc.) already exist from Phase 2 — Phase 5 fulfills those links
- `get_trips_by_continent()` called on each destination page to decide trips-grid vs waitlist-form
- New constants added to `includes/config.php` (WAITLIST_WEBHOOK_URL, TALLY_B2B_URL, WHATSAPP_B2B_FALLBACK)

</code_context>

<specifics>
## Specific Ideas

- Written guarantee copy: scrape/replicate from viaggiacolbaffo.com/diventa-agenzia-partner/ — use that exact language rather than inventing it
- Sub-destination cards link to `viaggi.php?continent=[slug]` — already established filter param from Phase 3
- Commission rate: NEVER hardcode a percentage on agenzie.php — "commissioni competitive" only. Per-trip rate will be a field in the Phase 6 admin and displayed on the trip page
- B2B tone reference: current live site at viaggiacolbaffo.com/diventa-agenzia-partner/

</specifics>

<deferred>
## Deferred Ideas

- Per-trip commission rate field in admin (visible on trip page to agencies) — Phase 6 admin panel
- Sub-destination detail pages — future phase or v2

</deferred>

---

*Phase: 05-destinations-b2b*
*Context gathered: 2026-03-06*
