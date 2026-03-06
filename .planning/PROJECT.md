# Viaggia Col Baffo — Website

## What This Is

A complete website for "Viaggia Col Baffo," an Italian tour operator founded by Lorenzo D'Alessandro ("il Baffo"). Built in PHP + HTML + CSS + Vanilla JavaScript for Apache shared hosting on SiteGround, deployed via GitHub Actions FTP. The site must look like a large, established operator (Boscolo-tier) despite currently having only 2 trips — achieved through rich editorial destination pages that exist independently of trip availability.

## Core Value

Lorenzo is always personally present on every trip — the site must convey this intimacy, trust, and premium experience at every touchpoint, while appearing as established and full-featured as a major tour operator.

## Requirements

### Validated

(None yet — ship to validate)

### Active

- [ ] Homepage with cinematic hero, active trips grid, destination cards, founder story, testimonials, B2B banner
- [ ] Trip catalog page (viaggi.php) with dual-row filtering (continent + tags) and smooth JS transitions
- [ ] Single trip page (viaggio.php) with full itinerary accordion, includes/excludes, gallery lightbox, and AI-generated quote form
- [ ] Destination pages (destinazione.php) with editorial content, practical info, curiosity facts, and trip grid or sold-out waitlist form
- [ ] B2B agencies page (agenzie.php) with value props, how-it-works, and Tally registration form
- [ ] Admin panel with login, trip dashboard, and full create/edit trip form
- [ ] AI Form Generator: Lorenzo describes trip in plain Italian → GPT-4o-mini generates dynamic form_config JSON → rendered live on trip page with real-time price calculation
- [ ] Dynamic form renderer that reads form_config from trips.json and submits to configurable webhook (Make/Zapier/n8n)
- [ ] Tag system powering all filtering: continent + theme + month + occasion tags
- [ ] Auto-routing: saving a trip with continent/tags automatically surfaces it on catalog, destination page, and homepage — no manual work
- [ ] GitHub Actions FTP deploy workflow to SiteGround
- [ ] .htaccess with clean URLs, HTTPS redirect, /data/ protection, gzip, caching
- [ ] Sample data: West America Aprile 2026 trip (real content), all 6 destination editorial pages

### Out of Scope

- Node.js, npm, build tools, any server-side framework — strict PHP + vanilla stack
- External CSS libraries (Bootstrap, Tailwind) — pure custom CSS only
- Real-time features (chat, live inventory) — static JSON data only
- Mobile app — web-first only
- Payment processing — inquiry/quote only, no booking engine
- User accounts / login for end customers — admin-only auth

## Context

- **Hosting**: SiteGround Apache shared hosting — no Node.js, no composer required
- **Repo**: https://github.com/Zale-Marketing/viaggiacolbaffo
- **Current live site**: https://viaggiacolbaffo.com (WordPress, being replaced)
- **Reference sites**: boscolo.com (premium editorial), vamonos-vacanze.it (emotional copy + photo grid)
- **Real trip reference**: https://viaggiacolbaffo.com/west-america-aprile-2026/
- **B2B reference**: https://viaggiacolbaffo.com/diventa-agenzia-partner/
- **Brand**: Cinematic, luxury, emotional, premium. Gold (#C9A84C) accents on near-black (#0D0D0D) background. Playfair Display headings, Inter body. Font Awesome 6 for icons only.
- **Founder**: Lorenzo D'Alessandro "il Baffo" — 40 years experience, 48 US states, IATA accredited, personally accompanies every trip
- **Current trips**: Only 2 real trips, but site must look like Boscolo — editorial destination pages exist even with 0 trips (show "sold out / join waitlist")
- **Logo**: https://viaggiacolbaffo.com/wp-content/uploads/2025/09/Progetto-senza-titolo-2025-09-30T075103.747-e1759212405873.png

## Constraints

- **Tech stack**: PHP + HTML + CSS + Vanilla JS only — no exceptions
- **Hosting**: Apache shared hosting, no shell access for package managers, no Node.js runtime
- **Images**: Unsplash direct URLs for all placeholders (no local image storage in repo)
- **Data layer**: trips.json + tags.json as the sole data store (PHP reads/writes)
- **Admin auth**: Session-based PHP auth only, password in config.php
- **OpenAI**: GPT-4o-mini via API — key stored in config.php (empty by default, optional feature)
- **Dependencies**: Font Awesome 6 CDN only — everything else hand-written
- **Deploy**: GitHub Actions + FTP-Deploy-Action to /nuovo.viaggiacolbaffo.com/public_html/

## Key Decisions

| Decision | Rationale | Outcome |
|----------|-----------|---------|
| PHP + vanilla JS (no framework) | SiteGround shared hosting constraint, no Node.js, simpler deploy | — Pending |
| trips.json as data store | No database available, admin panel writes JSON directly, simpler than SQLite on shared host | — Pending |
| Editorial destination pages always exist | Makes site look like Boscolo even with 2 trips — destinations are content, not just trip containers | — Pending |
| AI form generator via GPT-4o-mini | Lorenzo can describe a trip in plain Italian and get a smart quote form instantly | — Pending |
| Webhook-based form submission | Compatible with Make/Zapier/n8n without a CRM on-server — flexible and zero-maintenance | — Pending |
| Unsplash direct URLs for images | No image upload infrastructure needed, fast CDN, replaceable later | — Pending |

---
*Last updated: 2026-03-06 after initialization*
