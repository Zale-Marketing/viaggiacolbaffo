# Phase 2: Homepage - Context

**Gathered:** 2026-03-06
**Status:** Ready for planning

<domain>
## Phase Boundary

Build `index.php` — the full homepage with cinematic hero, urgency bar, active trips grid, destination cards, why-Baffo section, founder story, testimonials, and B2B banner. Also replace the placeholder `footer.php` with the full production footer (shared by all pages from this phase onward). Navigation to catalog (`viaggi.php`) and destination pages (`destinazione.php`) is via links only — those pages are built in later phases.

</domain>

<decisions>
## Implementation Decisions

### Header behavior on hero sections

- `header.php` gains support for a `$hero_page` PHP flag
- When `$hero_page = true` is set before including `header.php`, a `has-hero` class is added to the `<body>`
- In `has-hero` state: header starts with a semi-transparent dark overlay (rgba 0,0,0,0.3 or similar) and white nav links
- On scroll past the hero: header transitions to solid white with navy links (existing `.scrolled` behavior)
- This flag applies to **all pages with a full-viewport dark hero** — not homepage-only (catalog, trip pages will reuse it)
- The hero section does NOT repeat the logo — the header logo is sufficient

### Active trips mobile layout

- Mobile: CSS snap-scroll carousel (`overflow-x: auto` + `scroll-snap-type: x mandatory`)
- Each card is ~85% viewport width so the next card peeks at ~15%, signaling scrollability
- No JS dots, arrows, or scroll hint text — the peeking card is sufficient
- Desktop: standard `.trip-grid` responsive grid (already in style.css, 2-col tablet / 3-col desktop)

### Destination cards

- Card at rest: full-bleed photo, dark gradient overlay, destination name in Playfair + one-line tagline below name
- On hover: photo scales (transform: scale(1.05)), white border appears, dark overlay lightens slightly (opacity reduces)
- Hover accent color: **white border** (not red or gold) — clean on dark overlays, brand-neutral
- Grid: 3×2 desktop, 2×3 tablet, 1-col mobile (6 destinations: America, Asia, Europa, Africa, Oceania, Medio Oriente)
- Each links to `destinazione.php?slug=` for Phase 5

### Footer

- Replaces `footer.php` entirely — becomes the shared footer for **all pages** from Phase 2 onward
- 3-column layout (desktop): Col 1: logo + 1-line tagline | Col 2: nav links (Viaggi, Destinazioni, Agenzie, Contatti) | Col 3: phone, WhatsApp link, email, Instagram/Facebook icons, IATA badge
- Bottom bar: `--primary` (#000744) navy background with P.IVA + copyright in smaller white text
- Footer background: `--dark` (#111827)
- Collapses to single-column stack on mobile

### Claude's Discretion

- Exact Unsplash photo URLs for hero and 6 destination cards
- Hero tagline/subline final phrasing (use requirements as source of truth: "Viaggia col Baffo — E non cambi mai più")
- Urgency bar exact wording (use requirement: "West America Aprile 2026 — Ultimi 5 posti disponibili")
- Lorenzo's portrait photo (use Unsplash placeholder for now)
- Testimonial content (invent 3 plausible Italian testimonials)
- Why-Baffo icon choices from Font Awesome 6
- Semi-transparent overlay exact rgba value for header-over-hero state
- Header JS transition implementation detail (CSS class toggle on scroll)

</decisions>

<code_context>
## Existing Code Insights

### Reusable Assets

- `.trip-card` + `.trip-grid` classes: **permanent from Phase 1** — homepage uses them directly for the active trips section
- `.btn--gold` (navy fill): use for "Scopri i viaggi" primary CTA on hero
- `.btn--outline-white` (transparent + white border): use for "Sei un'agenzia?" secondary CTA on hero
- `.btn-accent` (red): available for urgency elements if needed
- `.section-header` / `.section-header__title`: use for all section headings (already styled with red underline)
- `.section` (padding 5rem 0) + `.section--dark`: use for alternating section backgrounds
- `.container` (max-width 1200px, auto margins): use for all section content wrappers

### Established Patterns

- PHP includes: all pages include `config.php` and `functions.php` first, then `header.php` / `footer.php`
- Single `style.css` + single `main.js` — no page-specific CSS files; page-specific JS goes inline in the page
- CSS variables drive all colors — no hard-coded hex values in new CSS except where intentional (e.g. card overlays)
- `load_trips()` from `functions.php` returns all trips; filter for `published === true` for active trips display

### Integration Points

- `index.php` requires `includes/config.php` + `includes/functions.php` to read trips.json
- `$hero_page = true` must be set before `include 'includes/header.php'` in index.php and all future hero pages
- Footer.php replacement affects all future pages immediately — must be complete before Phase 3 starts
- Destination card links: `destinazione.php?slug=america` etc. (page built in Phase 5, links exist from Phase 2)

</code_context>

<specifics>
## Specific Ideas

- Reference sites: boscolo.com for premium editorial feel; vamonos-vacanze.it for emotional copy + photo grid
- Hero should fill 100vh with a cinematic Unsplash travel photo (dark, atmospheric — desert/road/landscape)
- The urgency bar is hardcoded per requirements — not dynamic from trips.json
- Lorenzo: "48 US states, Y86 Travel since 1986, personally accompanies every trip, IATA accredited" — these facts go in the founder story section

</specifics>

<deferred>
## Deferred Ideas

None — discussion stayed within phase scope.

</deferred>

---

*Phase: 02-homepage*
*Context gathered: 2026-03-06*
