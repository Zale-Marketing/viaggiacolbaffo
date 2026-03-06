# Roadmap: Viaggia Col Baffo Website

## Overview

Six phases deliver a complete tour operator website on PHP + vanilla stack. Phase 1 lays the technical and design foundation. Phase 2 launches the homepage — the primary trust-building surface. Phase 3 adds the trip catalog with filtering. Phase 4 delivers the deep single-trip experience and AI-powered quote form. Phase 5 adds editorial destination pages and the B2B agencies page, making the site look as established as Boscolo. Phase 6 gives Lorenzo full admin control to create and manage trips without touching code.

## Phases

**Phase Numbering:**
- Integer phases (1, 2, 3): Planned milestone work
- Decimal phases (2.1, 2.2): Urgent insertions (marked with INSERTED)

Decimal phases appear between their surrounding integers in numeric order.

- [ ] **Phase 1: Foundation** - Infrastructure, design system, and data layer that all other phases depend on
- [ ] **Phase 2: Homepage** - Full cinematic homepage that conveys Lorenzo's intimacy and premium experience
- [ ] **Phase 3: Trip Catalog** - Browsable trip catalog with dual-row continent + tag filtering
- [ ] **Phase 4: Trip Detail + Quote Form** - Full trip experience: itinerary, gallery, and AI-powered quote form
- [ ] **Phase 5: Destinations + B2B** - Editorial destination pages and agency partnership page
- [ ] **Phase 6: Admin Panel** - Lorenzo manages trips end-to-end without touching code

## Phase Details

### Phase 1: Foundation
**Goal**: The codebase is deployed to SiteGround, the design system is live, and trip data loads correctly — all other phases can build on this without rework
**Depends on**: Nothing (first phase)
**Requirements**: INFRA-01, INFRA-02, INFRA-03, INFRA-04, INFRA-05, DESIGN-01, DESIGN-02, DESIGN-03, DESIGN-04, DESIGN-05, DATA-01, DATA-02, DATA-03, DATA-04
**Success Criteria** (what must be TRUE):
  1. Pushing to main triggers the GitHub Actions workflow and files appear on SiteGround under the correct subdirectory
  2. Visiting a clean URL (e.g., /viaggio/west-america) works — no .php extension needed, HTTPS is forced, and /data/ returns 403
  3. A test PHP file reading trips.json correctly returns the "West America Aprile 2026" trip with all fields populated
  4. A design preview page shows the full token set: --gold, --black, Playfair Display headings, Inter body, correct card layout with status pill and continent badge
  5. The CSS grid system renders 1-column on mobile, 2-column on tablet, and 3-column on desktop
**Plans**: TBD

### Phase 2: Homepage
**Goal**: Visitors landing on the homepage feel the premium, cinematic experience and can immediately see active trips and contact Lorenzo
**Depends on**: Phase 1
**Requirements**: HOME-01, HOME-02, HOME-03, HOME-04, HOME-05, HOME-06, HOME-07, HOME-08, HOME-09
**Success Criteria** (what must be TRUE):
  1. The hero section fills the full viewport with the cinematic dark photo, logo, tagline, and both CTAs visible without scrolling
  2. The "West America Aprile 2026 — Ultimi 5 posti" urgency bar appears immediately below the hero
  3. Scrolling down reveals the active trips grid (reading from trips.json), destination cards grid, why-Baffo blocks, Lorenzo's story section, testimonials, and B2B banner — all styled in the premium dark/gold palette
  4. Clicking "Scopri i viaggi" navigates to the catalog; clicking a destination card navigates to that destination page
  5. The footer is complete with nav links, WhatsApp link, IATA badge, and social icons
**Plans**: TBD

### Phase 3: Trip Catalog
**Goal**: Visitors can browse and filter all trips by continent and theme, and always see the right count and empty state
**Depends on**: Phase 2
**Requirements**: CATALOG-01, CATALOG-02, CATALOG-03, CATALOG-04, CATALOG-05, CATALOG-06
**Success Criteria** (what must be TRUE):
  1. The catalog hero banner loads and the dual-row filter bar shows all continents on row 1 and all tags (from tags.json) on row 2
  2. Clicking "America" on row 1 filters the grid to only American trips; the "Mostrando X viaggi" counter updates instantly
  3. Adding a tag filter on row 2 further narrows results (AND logic) — and removing all filters restores the full list with smooth CSS transition
  4. Sharing a URL with ?continent=america&tag=famiglia opens the catalog with those filters pre-applied
  5. When no trips match the active filters, the friendly empty-state message and custom request form (Tally embed) appear
**Plans**: TBD

### Phase 4: Trip Detail + Quote Form
**Goal**: A visitor arriving on a specific trip page gets the full itinerary experience and can submit a quote request that reaches Lorenzo
**Depends on**: Phase 3
**Requirements**: TRIP-01, TRIP-02, TRIP-03, TRIP-04, TRIP-05, TRIP-06, TRIP-07, TRIP-08, TRIP-09, TRIP-10, FORM-01, FORM-02, FORM-03, FORM-04, FORM-05, FORM-06
**Success Criteria** (what must be TRUE):
  1. Navigating to /viaggio/west-america-aprile-2026 loads the correct trip; an invalid slug redirects to a 404 page
  2. The sticky top bar appears after scrolling past the hero; the sticky tab navigation allows jumping to Itinerario, Cosa Include, Galleria, and Richiedi Preventivo sections
  3. The itinerary accordion opens and closes each day; the gallery lightbox opens a photo full-screen without any external JS library
  4. The quote form renders dynamically from form_config, calculates a live price estimate as room type and participant count change, and shows extra agency fields when "Agenzia" is selected
  5. Submitting the form sends the data to the configured webhook via AJAX and shows a success message without a page reload
  6. The AI form generator in admin accepts a plain Italian trip description, calls GPT-4o-mini, and returns a form_config JSON preview that can be saved to the trip
**Plans**: TBD

### Phase 5: Destinations + B2B
**Goal**: The site looks as established as Boscolo — destination pages exist with rich editorial content regardless of trip availability, and agencies can register as partners
**Depends on**: Phase 1
**Requirements**: DEST-01, DEST-02, DEST-03, DEST-04, DEST-05, DEST-06, DEST-07, B2B-01, B2B-02, B2B-03, B2B-04, B2B-05, B2B-06
**Success Criteria** (what must be TRUE):
  1. All 6 destination slugs (america, asia, europa, africa, oceania, medio-oriente) load their own hero, editorial intro, practical info boxes, sub-destination cards, and curiosity facts
  2. A destination with active trips shows those trip cards; a destination with no active trips shows the sold-out waitlist form instead
  3. The B2B page loads the full value props, how-it-works steps, and the embedded Tally agency registration form
  4. An invalid destination slug returns a 404 page
**Plans**: TBD

### Phase 6: Admin Panel
**Goal**: Lorenzo can log in, create a trip, publish it, and see it live on the site — all without touching code or the server
**Depends on**: Phase 4, Phase 5
**Requirements**: ADMIN-01, ADMIN-02, ADMIN-03, ADMIN-04, ADMIN-05, ADMIN-06, ADMIN-07, ADMIN-08, ADMIN-09, ADMIN-10, ADMIN-11
**Success Criteria** (what must be TRUE):
  1. Visiting /admin without a session redirects to the login page; entering the correct password from config.php grants access
  2. The dashboard table lists all trips with title, continent, tags, status, and published state; toggling published updates instantly
  3. The full edit form saves all trip fields — basic info, dates, price, hero image URL with thumbnail preview, gallery URLs, short/full description, day-by-day itinerary (draggable rows), includes/excludes, and tag chips
  4. Saving a trip via "Pubblica" makes it immediately visible on the homepage trip grid, catalog, and its destination page without any manual cache clearing
  5. config.php values (OpenAI key, webhook URL, WhatsApp number, Tally URLs) are editable through the admin interface
**Plans**: TBD

## Progress

**Execution Order:**
Phases execute in numeric order: 1 → 2 → 3 → 4 → 5 → 6

Note: Phase 5 depends on Phase 1 (not Phase 3/4), so it could run in parallel with phases 3–4 if needed — but sequential execution is the default.

| Phase | Plans Complete | Status | Completed |
|-------|----------------|--------|-----------|
| 1. Foundation | 0/TBD | Not started | - |
| 2. Homepage | 0/TBD | Not started | - |
| 3. Trip Catalog | 0/TBD | Not started | - |
| 4. Trip Detail + Quote Form | 0/TBD | Not started | - |
| 5. Destinations + B2B | 0/TBD | Not started | - |
| 6. Admin Panel | 0/TBD | Not started | - |
