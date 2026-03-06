# Phase 3: Trip Catalog - Context

**Gathered:** 2026-03-06
**Status:** Ready for planning

<domain>
## Phase Boundary

Browsable catalog page (`viaggi.php`) — hero banner, sticky dual-row filter bar (continent + tags), trip grid with live JS filtering, count display, URL deep-linking, and empty state with Tally embed when no trips match.

</domain>

<decisions>
## Implementation Decisions

### Filter bar design
- Active filter: navy #000744 background + white text
- Inactive filter: transparent background, white outline border
- Mobile: both rows scroll horizontally (no wrapping)
- Filter bar is sticky — sticks to top of viewport after user scrolls past the hero

### Multi-filter behavior
- Row 1 (continents): single-select — clicking a continent replaces the previous selection
- Row 2 (tags): multi-select — AND logic, multiple tags can be active simultaneously
- Clicking an already-active filter deselects it (toggle behavior)
- "Tutti" on each row deselects all filters in that row
- "Mostrando X viaggi" count updates instantly as JS filters apply, with a brief CSS fade transition on the number

### Catalog hero
- Short banner: ~35–40vh height (not full-viewport)
- Content: "I Nostri Viaggi" in large Playfair Display + short subline only (no CTA button)
- Static background photo with dark overlay — no parallax effect
- Purpose: just enough visual premium feel before quickly getting to filters

### Empty state
- Shown instantly when no trips match active filters (no skeleton, immediate swap)
- Warm, human Italian copy — e.g. "Nessun viaggio trovato per i tuoi filtri. Non trovate quello che cercate? Proponeteci un viaggio su misura!"
- Inline Tally iframe embed directly below the message (no modal, no new tab)
- Visual treatment: centered, dark background, white text — headline uses navy #000744 underline accent (NOT gold, NOT red — navy is the brand accent for decorative elements)
- CSS fade/opacity transition when empty state appears or disappears (grid ↔ empty state swap)

### Brand palette clarification (CRITICAL for planner/researcher)
- Navy #000744 is the PRIMARY brand color and decorative accent (section header underlines, active filter, CTAs)
- Red #CC0031 is URGENCY/ERRORS only (status badges, error states, urgency banners)
- There is NO gold accent color in use — despite --gold being defined in CSS variables, it maps to red (#CC0031) which is urgency-only
- All section header underlines: navy #000744
- All decorative accents (filter active state, underlines): navy #000744

### Claude's Discretion
- Exact Unsplash photo URL for the catalog hero banner
- Filter bar background color (dark, slightly elevated from page background)
- Exact spacing between filter rows
- Tally form URL placeholder in config.php (already defined as TALLY_CATALOG_URL or similar)

</decisions>

<code_context>
## Existing Code Insights

### Reusable Assets
- `.trip-card` + all sub-classes (`.trip-card__image`, `.trip-card__overlay`, `.trip-card__content`, `.trip-card__continent`, `.trip-card__status`, `.trip-card__title`, `.trip-card__dates`, `.trip-card__price`): Class names are PERMANENT — catalog reuses them without modification
- `.trip-grid`: CSS grid (1-col mobile / 2-col tablet / 3-col desktop) — use as-is
- `load_trips()`, `load_tags()`: PHP functions in `includes/functions.php` — load catalog data
- `.section`, `.container`, `.section-header`, `.section-header__title`, `.section-header__subtitle`: Layout utilities
- `.btn`, `.btn--gold` (actually navy), `.btn--outline-white`: Button patterns
- `header.php` with `$hero_page` flag pattern: set `$hero_page = true` before include if catalog hero uses same transparent-scrolled header behavior
- `main.js` is a shared placeholder — catalog filtering JS goes as inline `<script>` in `viaggi.php`

### Established Patterns
- PHP inline scripts per page (not separate .js files) — established in Phase 2
- Unsplash direct URLs for all photos
- All interactive elements use 0.3s CSS transitions (var(--transition))
- Status pills use existing `.status--{value}` class names
- URL params for state: `?continent=america&tag=famiglia` — read with `$_GET` in PHP for pre-applying filters on page load

### Integration Points
- `viaggi.php` is a new top-level file reading from `includes/functions.php`
- Header: `$hero_page = true` to trigger transparent header (matching Phase 2 hero pattern)
- Footer: standard `includes/footer.php` include
- Config: `TALLY_CATALOG_URL` constant needed in `includes/config.php` for the empty-state embed

</code_context>

<specifics>
## Specific Ideas

- Filter sticky behavior: filter bar should have a slightly elevated dark background (e.g. `var(--dark)` or `var(--dark-card)`) with a subtle bottom border to visually separate it from content when sticky
- The "Mostrando X viaggi" line should sit between the filter bar and the trip grid — not inside the filter bar
- Catalog hero photo: a travel/landscape photo (Unsplash) — something wide, dramatic, dark-toned to match the cinematic brand

</specifics>

<deferred>
## Deferred Ideas

None — discussion stayed within phase scope.

</deferred>

---

*Phase: 03-trip-catalog*
*Context gathered: 2026-03-06*
