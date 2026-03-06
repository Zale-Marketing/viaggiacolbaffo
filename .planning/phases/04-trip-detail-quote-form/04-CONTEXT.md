# Phase 4: Trip Detail + Quote Form - Context

**Gathered:** 2026-03-06
**Status:** Ready for planning

<domain>
## Phase Boundary

Build `viaggio.php` — the full single-trip experience: cinematic hero, sticky tab navigation, itinerary accordion, includes/excludes list, masonry photo gallery with lightbox, and a dynamic quote form driven by `form_config` in trips.json with live price calculation, B2B/B2C toggle, and webhook submission via AJAX. Also build `api/generate-form.php` — a standalone endpoint that calls GPT-4o-mini to generate `form_config` JSON from a plain Italian trip description. Admin UI for the generator is Phase 6.

</domain>

<decisions>
## Implementation Decisions

### Page layout
- Full-viewport dark hero with `$hero_page = true` (transparent header, same pattern as homepage/catalog)
- Sticky tab bar after scrolling past hero: tabs for Itinerario, Cosa Include, Galleria, Richiedi Preventivo
- Sections flow full-width sequentially: hero → sticky nav → itinerary → includes/excludes → gallery → quote form
- No sticky sidebar — the quote form is a bottom section only

### Itinerary accordion
- Default state: Day 1 open, all others collapsed
- Single-open behavior: opening a new day closes the previous one
- Visual treatment per row: navy circle pill with zero-padded day number on the left, bold title text, chevron on the right that rotates 90° on open
- CSS transition on expand/collapse (var(--transition))

### Gallery
- Thumbnail grid: masonry-style 3-column layout (CSS columns or grid with `grid-auto-rows: auto`), 2-col tablet, 1-col mobile
- Lightbox: custom vanilla JS, no external library
  - Opens full-screen on thumbnail click
  - Navigation: left/right arrow buttons + keyboard ← / → + swipe (touchstart/touchend delta)
  - Photo counter displayed top-right: "2 / 4"
  - Esc key or click outside to close
  - Dark overlay background

### Quote form
- Positioned as a full-width bottom section, dark background (`var(--dark-card)`), centered at ~700px max-width
- B2B/B2C toggle at top (default: B2C "Privato"; when agency code validates, suggest switching to B2B)
- Participant inputs:
  - Adult counter (+ / - buttons, min 1)
  - Children counter (+ / - buttons, min 0); each child added reveals an age input field
- Room type: select driven by `form_config.room_types` (slug, label, price_delta)
- Add-ons: checkboxes driven by `form_config.addons` — insurance is always one of them
- Agency code field (visible when B2B / "Agenzia" selected): validated client-side with SHA-256 hash comparison against a stored hash in `form_config` or `config.php`; on valid code, agency-specific fields appear: Nome agenzia, Codice IATA (optional), Città / Provincia
- Live price estimate box (visually distinct — navy border or highlighted): shows total and per-person breakdown, updates in real time as room type / participant count / add-ons change
- Competitor savings line: "Risparmia X€ rispetto al prezzo medio" — driven by `form_config.competitor_price`
- `form_config` pricing constants: `price_per_person`, `single_supplement`, `third_bed_price`, `fourth_bed_price`, `competitor_benchmark`
- Form submission: fetch() POST to `trip.webhook_url` (or fallback to `DEFAULT_WEBHOOK_URL` in config.php); no page reload; success message replaces form on success; error message inline on failure

### AI form generator endpoint
- File: `api/generate-form.php`
- Method: POST, JSON body: `{ "description": "..." }` (plain Italian text)
- If `OPENAI_API_KEY` is empty in config.php: return a default `form_config` derived from the trip's `price_from` value (sensible defaults for room_types, standard fields list, no addons)
- If key present: call GPT-4o-mini with a prompt instructing it to infer room_types, addons, pricing constants, and fields from the description; return structured JSON matching the `form_config` schema
- Response: `{ "success": true, "form_config": { ... } }` or `{ "success": false, "error": "..." }`
- No authentication on the endpoint in Phase 4 — Phase 6 admin panel will call it from behind auth

### Claude's Discretion
- Exact sticky nav transition behavior (background appears on scroll, same scroll-listener pattern as header)
- Includes/excludes list visual treatment (checkmark icons for included, X icons for excluded — Font Awesome 6)
- Hero content layout (title, dates, duration, price_from, status pill, CTA "Richiedi Preventivo" scrolls to form)
- Lightbox CSS overlay and photo sizing details
- GPT-4o-mini system prompt wording
- 404 redirect logic for invalid slugs (header("Location: /404") or inline error page)

</decisions>

<code_context>
## Existing Code Insights

### Reusable Assets
- `get_trip_by_slug(string $slug): ?array` in `includes/functions.php` — load trip by URL slug
- `.section`, `.section--dark`, `.container` — layout utilities, use for all sections
- `.section-header`, `.section-header__title` — section headings with navy underline (navy #000744, NOT red)
- `.btn`, `.btn--gold` (actually navy fill), `.btn--outline-white` — button variants
- `header.php` with `$hero_page = true` flag — transparent-to-scrolled transition, same as homepage and catalog
- `includes/footer.php` — standard include
- `var(--transition)` CSS variable — use for all animations (0.3s ease)
- `var(--dark-card)`, `var(--dark)`, `var(--gold)` / `var(--primary)` (navy) CSS variables

### Established Patterns
- PHP inline `<script>` per page (no separate .js files for page-specific logic)
- Unsplash direct URLs for all photos (already in trips.json gallery arrays)
- `config.php` constants: `OPENAI_API_KEY`, `DEFAULT_WEBHOOK_URL`, `ADMIN_PASSWORD` — all already defined
- No external JS libraries — all interactivity (accordion, lightbox, form logic) must be vanilla JS
- PHP reads `$_GET['slug']` or slug from clean URL (via .htaccess rewrite) to identify the trip

### Integration Points
- `viaggio.php` reads slug from URL, calls `get_trip_by_slug()`, renders trip or redirects to 404
- `api/generate-form.php` reads `OPENAI_API_KEY` from `includes/config.php`
- The quote form's fetch() POST goes to `$trip['webhook_url']` (per-trip) or `DEFAULT_WEBHOOK_URL` fallback
- Sticky tab "Richiedi Preventivo" anchor-scrolls to the quote form section (same-page anchor link)
- `form_config` schema already defined in `data/trips.json` — West America trip has a complete example

</code_context>

<specifics>
## Specific Ideas

- The quote form must replicate an existing working form with this exact feature set: B2B/B2C toggle, adult + child counters with per-child age inputs, live price breakdown box, competitor savings line, insurance add-on, SHA-256 agency code validation, and webhook fetch() submission
- `form_config` JSON schema must be extended to include: `price_per_person`, `single_supplement`, `third_bed_price`, `fourth_bed_price`, `competitor_benchmark` as top-level pricing constants (in addition to existing `room_types`, `addons`, `fields`)
- The West America trips.json entry should be updated to include these new pricing constant fields
- If OpenAI key is not configured, `api/generate-form.php` should still be functional — returning a sensible default form_config so the endpoint works without AI

</specifics>

<deferred>
## Deferred Ideas

- Admin UI for the AI form generator (textarea + JSON preview) — Phase 6 admin panel
- Saving generated form_config back to trips.json through the UI — Phase 6

</deferred>

---

*Phase: 04-trip-detail-quote-form*
*Context gathered: 2026-03-06*
