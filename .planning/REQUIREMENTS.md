# Requirements: Viaggia Col Baffo Website

**Defined:** 2026-03-06
**Core Value:** Lorenzo always personally present on every trip — the site must convey this intimacy and premium experience while looking as established as Boscolo.

## v1 Requirements

### Infrastructure

- [x] **INFRA-01**: Project deploys to SiteGround via GitHub Actions FTP on push to main branch
- [x] **INFRA-02**: .htaccess provides clean URLs (viaggio/slug, destinazione/slug), forces HTTPS, protects /data/ from direct access, enables gzip and browser caching
- [x] **INFRA-03**: /data/ directory contains trips.json and tags.json, writable by PHP, not directly accessible via HTTP
- [x] **INFRA-04**: /assets/img/ directory exists (with .gitkeep) as placeholder for future uploaded images
- [x] **INFRA-05**: README.md documents setup, trip management, webhook config, and OpenAI key setup

### Design System

- [x] **DESIGN-01**: CSS variables defined: --black #0D0D0D, --gold #C9A84C, --gold-light #e8c76a, --white #FFFFFF, --dark #1a1a1a, --dark-card #222222, --grey #888888, status colors (green/orange/red), --font-heading Playfair Display, --font-body Inter, --radius 12px, --shadow, --transition
- [x] **DESIGN-02**: Google Fonts loaded (Playfair Display + Inter) and Font Awesome 6 CDN for icons
- [x] **DESIGN-03**: Mobile-first responsive design: 1-col mobile, 2-col tablet, 3-col desktop grids
- [x] **DESIGN-04**: Trip cards: full-bleed photo, gradient overlay, continent badge (gold pill, top-left), status pill (top-right: green/orange/red), Playfair title, dates, duration, price, CTA link
- [x] **DESIGN-05**: Cinematic section headers in Playfair Display with gold accent underlines; smooth 0.3s transitions on all interactive elements

### Data Layer

- [x] **DATA-01**: trips.json stores all trip data: slug, title, continent, status (confermata/ultimi-posti/sold-out/programmata), published (bool), dates, duration, price_from, hero_image, gallery (array), short_description, full_description, itinerary (array of {day, title, description}), included (array), excluded (array), tags (array), form_config (object)
- [x] **DATA-02**: tags.json stores all available tags with slug and display label (continents, themes, months, occasions)
- [x] **DATA-03**: PHP functions.php provides: load_trips(), get_trip_by_slug(), get_trips_by_continent(), get_trips_by_tag(), save_trips(), load_tags()
- [x] **DATA-04**: Sample data pre-populated: "West America Aprile 2026" trip with real content, status ultimi-posti, price 3490, published true, full itinerary, tags [america, road-trip, aprile, coppia, famiglia, avventura, parchi-naturali]

### Homepage

- [x] **HOME-01**: Full-viewport hero section: tagline "Viaggia col Baffo — E non cambi mai più", subline, two CTAs ("Scopri i viaggi" gold + "Sei un'agenzia?" outline white), cinematic dark background Unsplash photo. No logo inside the hero — the sticky header logo is sufficient.
- [x] **HOME-02**: Urgency bar below hero: "West America Aprile 2026 — Ultimi 5 posti disponibili" (hardcoded, updatable)
- [x] **HOME-03**: "I Nostri Viaggi Attivi" section: reads published=true trips from trips.json, renders trip cards in horizontal-scroll mobile / grid desktop layout
- [x] **HOME-04**: "Esplora le Destinazioni" section: 6 cards grid (3x2 desktop), America / Asia / Europa / Africa / Oceania / Medio Oriente, each with Unsplash photo, dark overlay, centered name in Playfair, hover zoom + white border effect, links to destinazione.php?slug=
- [x] **HOME-05**: "Perché viaggiare col Baffo" section: 4 icon blocks — Lorenzo sempre con te, 40 anni di esperienza, Pacchetto tutto incluso, Assistenza H24
- [x] **HOME-06**: "Chi è il Baffo" section: two-column layout — left placeholder portrait, right Lorenzo's story (48 US states, Y86 Travel since 1986, personal accompaniment, IATA badge)
- [x] **HOME-07**: "Cosa dicono di noi" section: 3 testimonial cards with stars, name, trip name, review text
- [x] **HOME-08**: "Sei un'agenzia di viaggi?" full-width dark/gold-bordered banner with headline and CTA linking to agenzie.php
- [x] **HOME-09**: Footer: logo, nav links, phone, WhatsApp link, email, Instagram/Facebook icons, IATA badge, P.IVA, copyright

### Trip Catalog

- [x] **CATALOG-01**: Hero banner "I Nostri Viaggi" over dark travel photo
- [x] **CATALOG-02**: Dual-row filter bar: Row 1 = continent filters (Tutti + 6 continents); Row 2 = tag/theme filters (Tutti + all tags from tags.json); active filter shows gold background
- [x] **CATALOG-03**: Trip count display "Mostrando X viaggi" updates dynamically with filtering
- [x] **CATALOG-04**: Trip grid: 3-col desktop / 2-col tablet / 1-col mobile, cards identical to homepage
- [x] **CATALOG-05**: JavaScript filtering: clicking filters shows only matching trips; multiple tag filters combinable (AND logic); smooth CSS transition on filter change; URL params preserved for deep-linking (?continent=america&tag=famiglia)
- [x] **CATALOG-06**: Empty state when no trips match: friendly message + small custom request form (Tally embed configurable in config.php)

### Single Trip Page

- [x] **TRIP-01**: PHP reads trip from trips.json by ?slug= parameter; 404 redirect if slug not found
- [x] **TRIP-02**: Full-viewport hero: trip hero_image, dark gradient overlay, trip title in large Playfair, dates + duration + price, status pill
- [x] **TRIP-03**: Sticky top bar (appears after scrolling past hero): trip name left, "Richiedi Preventivo" gold button right (scrolls to form section)
- [x] **TRIP-04**: Highlights bar: 4 info boxes — Date, Durata, Da €X.XXX, Posti status
- [x] **TRIP-05**: Sticky tab navigation: Itinerario | Cosa Include | Galleria | Richiedi Preventivo — clicking scrolls smoothly to section
- [x] **TRIP-06**: Itinerario section: accordion — each day is a clickable row (Giorno N — Title), expanding shows description; timeline visual with gold dots on left
- [x] **TRIP-07**: Cosa Include section: two columns — green checkmarks for included items, red X for excluded items
- [x] **TRIP-08**: Galleria section: CSS masonry grid of gallery photos; click opens pure CSS/JS lightbox (no external library)
- [x] **TRIP-09**: Tags section: "Questo viaggio è perfetto per:" — all trip tags as gold pill links to viaggi.php?tag=
- [x] **TRIP-10**: Related trips section: 3 cards of trips sharing same continent or overlapping tags

### AI Quote Form

- [x] **FORM-01**: Form renderer reads form_config from trip's JSON and renders HTML form dynamically (PHP + JS)
- [x] **FORM-02**: JavaScript calculates live price total as user selects room type, optional add-ons, and participant count; displays "Preventivo stimato: €X.XXX" updating in real time
- [x] **FORM-03**: When tipo_cliente = "Agenzia" selected, additional fields appear: nome agenzia, P.IVA, commissione richiesta
- [x] **FORM-04**: Form submits via AJAX POST to /api/submit-form.php, which forwards JSON to trip's webhook_url via cURL; shows success/error message without page reload
- [x] **FORM-05**: Below form: WhatsApp button "Preferisci scrivere su WhatsApp?" linking to configured WhatsApp number
- [x] **FORM-06**: Admin AI form generator: textarea for Lorenzo to describe trip in plain Italian; "Genera Form con AI" button calls GPT-4o-mini API; returns form_config JSON; admin previews generated form; edits webhook_url; saves to trips.json

### Destination Pages

- [x] **DEST-01**: destinazione.php reads ?slug= (america/asia/europa/africa/oceania/medio-oriente); 404 if invalid slug
- [x] **DEST-02**: Full-viewport hero with stunning Unsplash photo and destination name overlay; breadcrumb: Home > Destinazioni > [Name]
- [x] **DEST-03**: Intro section: 3 paragraphs of inspiring editorial text (hardcoded in destinations-data.php per destination)
- [x] **DEST-04**: Practical info boxes: currency, language, best season, timezone, visa requirements (hardcoded per destination)
- [x] **DEST-05**: "Cosa Vedere" section: 3-4 sub-destination cards (e.g. America: New York, Grand Canyon, California, Las Vegas) — each with Unsplash photo, name, 2-line description; Boscolo-style layout
- [x] **DEST-06**: "Curiosità" section: 3 interesting facts with icon, title, text, gold left accent border
- [x] **DEST-07**: Trips section: IF trips exist for this continent (published=true) → show trip cards grid; IF no trips → show "sold out, join waitlist" dark box with name+email form that POSTs to webhook URL configured in config.php

### B2B Agencies Page

- [x] **B2B-01**: Dark hero: "Diventa Agenzia Partner" headline + subline
- [x] **B2B-02**: Trust bar: "Garanzia scritta • Commissioni competitive • Supporto dedicato • Materiali marketing inclusi"
- [x] **B2B-03**: 3 value prop cards: "I tuoi clienti restano TUOI" (written guarantee), "Commissioni fino al 12%", "Catalogo pronto da vendere"
- [x] **B2B-04**: How it works: 3 steps — Registrati → Ricevi il catalogo → Inizia a guadagnare
- [x] **B2B-05**: Embedded Tally form for agency registration (URL from config.php)
- [x] **B2B-06**: Agency partner testimonial (placeholder)

### Admin Panel

- [x] **ADMIN-01**: admin/login.php: minimal dark login form with logo, password field, submit; PHP session auth; password from config.php; all admin pages redirect to login if not authenticated
- [x] **ADMIN-02**: admin/index.php dashboard: table of all trips (title, continent, tags, status, published); actions per trip: Edit, Preview (new tab), Delete, Toggle Published; stats bar (total/published/draft count); "Crea Nuovo Viaggio" gold button
- [x] **ADMIN-03**: admin/edit-trip.php: full trip form — basic info (title, slug auto-generated, continent select, status select, published toggle), dates + price (start date, end date, auto-calculated duration, price_from)
- [x] **ADMIN-04**: Admin edit form: media fields — hero image URL with live preview thumbnail, gallery images (textarea one URL per line with preview grid)
- [x] **ADMIN-05**: Admin edit form: content fields — short description (max 160 chars with counter), full description (rich textarea)
- [x] **ADMIN-06**: Admin edit form: itinerary builder — "Aggiungi Giorno" button adds rows; each row has auto day number, title text, description textarea, remove button; rows reorderable by drag (pure JS)
- [x] **ADMIN-07**: Admin edit form: includes/excludes — two textareas (one item per line each)
- [x] **ADMIN-08**: Admin edit form: tag chip input — predefined tags selectable as gold pills; typing adds custom tags; tags removable; saved as array
- [x] **ADMIN-09**: Admin edit form: AI form generator section — plain Italian description textarea, "Genera Form con AI" button, loading state, JSON preview of generated form_config, webhook_url editable field, save to trip
- [x] **ADMIN-10**: Admin edit form: save actions — "Salva Bozza" (unpublished), "Pubblica" (published=true), "Anteprima" (opens viaggio.php?slug= in new tab)
- [x] **ADMIN-11**: admin/config.php stores: admin password, OpenAI API key (empty default), default webhook URL, WhatsApp number, Tally form URLs

## v2 Requirements

### Enhanced Features

- **ENH-01**: Image upload functionality in admin (currently URL-only)
- **ENH-02**: Multi-language support (Italian/English)
- **ENH-03**: Trip comparison tool
- **ENH-04**: PDF catalog generator
- **ENH-05**: Email newsletter signup integration
- **ENH-06**: Blog/editorial section
- **ENH-07**: Live availability counter synced from external system
- **ENH-08**: Google Analytics / Meta Pixel integration

### Automation

- **AUTO-01**: Automatic WhatsApp notification on new form submission
- **AUTO-02**: CRM integration (HubSpot/ActiveCampaign)
- **AUTO-03**: Automated email confirmation to inquirer

## Out of Scope

| Feature | Reason |
|---------|--------|
| User accounts / customer login | Inquiry-only model, no booking engine needed v1 |
| Payment processing / booking engine | Complex, out of scope for initial launch |
| Node.js / npm / build tools | SiteGround shared hosting constraint |
| External CSS frameworks (Bootstrap, Tailwind) | Pure custom CSS per brief |
| SQLite or MySQL | trips.json is sufficient, simpler to maintain |
| Real-time inventory sync | Static JSON only |
| Mobile app | Web-first |
| Video hosting | Unsplash images sufficient for v1 |

## Traceability

Which phases cover which requirements. Updated during roadmap creation.

| Requirement | Phase | Status |
|-------------|-------|--------|
| INFRA-01 | Phase 1 — Foundation | Complete |
| INFRA-02 | Phase 1 — Foundation | Complete |
| INFRA-03 | Phase 1 — Foundation | Complete |
| INFRA-04 | Phase 1 — Foundation | Complete |
| INFRA-05 | Phase 1 — Foundation | Complete |
| DESIGN-01 | Phase 1 — Foundation | Complete |
| DESIGN-02 | Phase 1 — Foundation | Complete |
| DESIGN-03 | Phase 1 — Foundation | Complete |
| DESIGN-04 | Phase 1 — Foundation | Complete |
| DESIGN-05 | Phase 1 — Foundation | Complete |
| DATA-01 | Phase 1 — Foundation | Complete |
| DATA-02 | Phase 1 — Foundation | Complete |
| DATA-03 | Phase 1 — Foundation | Complete |
| DATA-04 | Phase 1 — Foundation | Complete |
| HOME-01 | Phase 2 — Homepage | Complete |
| HOME-02 | Phase 2 — Homepage | Complete |
| HOME-03 | Phase 2 — Homepage | Complete |
| HOME-04 | Phase 2 — Homepage | Complete |
| HOME-05 | Phase 2 — Homepage | Complete |
| HOME-06 | Phase 2 — Homepage | Complete |
| HOME-07 | Phase 2 — Homepage | Complete |
| HOME-08 | Phase 2 — Homepage | Complete |
| HOME-09 | Phase 2 — Homepage | Complete |
| CATALOG-01 | Phase 3 — Trip Catalog | Complete |
| CATALOG-02 | Phase 3 — Trip Catalog | Complete |
| CATALOG-03 | Phase 3 — Trip Catalog | Complete |
| CATALOG-04 | Phase 3 — Trip Catalog | Complete |
| CATALOG-05 | Phase 3 — Trip Catalog | Complete |
| CATALOG-06 | Phase 3 — Trip Catalog | Complete |
| TRIP-01 | Phase 4 — Trip Detail + Quote Form | Complete |
| TRIP-02 | Phase 4 — Trip Detail + Quote Form | Complete |
| TRIP-03 | Phase 4 — Trip Detail + Quote Form | Complete |
| TRIP-04 | Phase 4 — Trip Detail + Quote Form | Complete |
| TRIP-05 | Phase 4 — Trip Detail + Quote Form | Complete |
| TRIP-06 | Phase 4 — Trip Detail + Quote Form | Complete |
| TRIP-07 | Phase 4 — Trip Detail + Quote Form | Complete |
| TRIP-08 | Phase 4 — Trip Detail + Quote Form | Complete |
| TRIP-09 | Phase 4 — Trip Detail + Quote Form | Complete |
| TRIP-10 | Phase 4 — Trip Detail + Quote Form | Complete |
| FORM-01 | Phase 4 — Trip Detail + Quote Form | Complete |
| FORM-02 | Phase 4 — Trip Detail + Quote Form | Complete |
| FORM-03 | Phase 4 — Trip Detail + Quote Form | Complete |
| FORM-04 | Phase 4 — Trip Detail + Quote Form | Complete |
| FORM-05 | Phase 4 — Trip Detail + Quote Form | Complete |
| FORM-06 | Phase 4 — Trip Detail + Quote Form | Complete |
| DEST-01 | Phase 5 — Destinations + B2B | Complete |
| DEST-02 | Phase 5 — Destinations + B2B | Complete |
| DEST-03 | Phase 5 — Destinations + B2B | Complete |
| DEST-04 | Phase 5 — Destinations + B2B | Complete |
| DEST-05 | Phase 5 — Destinations + B2B | Complete |
| DEST-06 | Phase 5 — Destinations + B2B | Complete |
| DEST-07 | Phase 5 — Destinations + B2B | Complete |
| B2B-01 | Phase 5 — Destinations + B2B | Complete |
| B2B-02 | Phase 5 — Destinations + B2B | Complete |
| B2B-03 | Phase 5 — Destinations + B2B | Complete |
| B2B-04 | Phase 5 — Destinations + B2B | Complete |
| B2B-05 | Phase 5 — Destinations + B2B | Complete |
| B2B-06 | Phase 5 — Destinations + B2B | Complete |
| ADMIN-01 | Phase 6 — Admin Panel | Complete |
| ADMIN-02 | Phase 6 — Admin Panel | Complete |
| ADMIN-03 | Phase 6 — Admin Panel | Complete |
| ADMIN-04 | Phase 6 — Admin Panel | Complete |
| ADMIN-05 | Phase 6 — Admin Panel | Complete |
| ADMIN-06 | Phase 6 — Admin Panel | Complete |
| ADMIN-07 | Phase 6 — Admin Panel | Complete |
| ADMIN-08 | Phase 6 — Admin Panel | Complete |
| ADMIN-09 | Phase 6 — Admin Panel | Complete |
| ADMIN-10 | Phase 6 — Admin Panel | Complete |
| ADMIN-11 | Phase 6 — Admin Panel | Complete |

**Coverage:**
- v1 requirements: 56 total
- Mapped to phases: 56
- Unmapped: 0

---
*Requirements defined: 2026-03-06*
*Last updated: 2026-03-06 — traceability finalized after roadmap creation*
