# Phase 2: Homepage - Research

**Researched:** 2026-03-06
**Domain:** PHP homepage construction — hero layout, CSS snap-scroll carousel, transparent-to-opaque header, destination card grid, footer
**Confidence:** HIGH

---

<user_constraints>
## User Constraints (from CONTEXT.md)

### Locked Decisions

**Header behavior on hero sections**
- `header.php` gains support for a `$hero_page` PHP flag
- When `$hero_page = true` is set before including `header.php`, a `has-hero` class is added to the `<body>`
- In `has-hero` state: header starts with a semi-transparent dark overlay (rgba 0,0,0,0.3 or similar) and white nav links
- On scroll past the hero: header transitions to solid white with navy links (existing `.scrolled` behavior)
- This flag applies to all pages with a full-viewport dark hero — not homepage-only
- The hero section does NOT repeat the logo — the header logo is sufficient

**Active trips mobile layout**
- Mobile: CSS snap-scroll carousel (`overflow-x: auto` + `scroll-snap-type: x mandatory`)
- Each card is ~85% viewport width so the next card peeks at ~15%, signaling scrollability
- No JS dots, arrows, or scroll hint text — the peeking card is sufficient
- Desktop: standard `.trip-grid` responsive grid (already in style.css, 2-col tablet / 3-col desktop)

**Destination cards**
- Card at rest: full-bleed photo, dark gradient overlay, destination name in Playfair + one-line tagline below name
- On hover: photo scales (transform: scale(1.05)), white border appears, dark overlay lightens slightly (opacity reduces)
- Hover accent color: white border (not red or gold)
- Grid: 3x2 desktop, 2x3 tablet, 1-col mobile (6 destinations: America, Asia, Europa, Africa, Oceania, Medio Oriente)
- Each links to `destinazione.php?slug=` for Phase 5

**Footer**
- Replaces `footer.php` entirely — becomes the shared footer for all pages from Phase 2 onward
- 3-column layout (desktop): Col 1: logo + 1-line tagline | Col 2: nav links | Col 3: phone, WhatsApp, email, Instagram/Facebook icons, IATA badge
- Bottom bar: `--primary` (#000744) navy background with P.IVA + copyright
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

### Deferred Ideas (OUT OF SCOPE)
None — discussion stayed within phase scope.
</user_constraints>

---

<phase_requirements>
## Phase Requirements

| ID | Description | Research Support |
|----|-------------|-----------------|
| HOME-01 | Full-viewport hero section: logo centered, tagline, subline, two CTAs, cinematic dark background Unsplash photo | Hero CSS pattern with 100vh, position:absolute overlay, existing `.btn--gold` + `.btn--outline-white` classes |
| HOME-02 | Urgency bar below hero: "West America Aprile 2026 — Ultimi 5 posti disponibili" (hardcoded) | Simple `<div>` with `.btn-accent` red background; no PHP data binding needed |
| HOME-03 | "I Nostri Viaggi Attivi" section: reads published=true trips from trips.json, renders trip cards in horizontal-scroll mobile / grid desktop | `load_trips()` filter pattern documented; CSS snap-scroll carousel pattern; existing `.trip-card` / `.trip-grid` classes |
| HOME-04 | "Esplora le Destinazioni" section: 6-card grid, hover zoom + white border, links to destinazione.php?slug= | Destination card CSS pattern: `overflow:hidden` on wrapper, `transform:scale()` on `img`, CSS `transition`, white border via `outline` or `border` |
| HOME-05 | "Perché viaggiare col Baffo" section: 4 icon blocks | Simple flex/grid layout; Font Awesome 6 icons already loaded; no new dependencies |
| HOME-06 | "Chi è il Baffo" section: two-column layout with portrait + story | Standard two-column CSS grid; Unsplash placeholder for portrait |
| HOME-07 | "Cosa dicono di noi" section: 3 testimonial cards with stars | Stars via Font Awesome `fa-star`; card pattern similar to trip cards |
| HOME-08 | "Sei un'agenzia di viaggi?" full-width dark/gold-bordered banner with CTA to agenzie.php | Full-width dark section; existing `.btn--gold` / `.btn--outline-white` buttons |
| HOME-09 | Footer: logo, nav links, phone, WhatsApp, email, social icons, IATA badge, P.IVA, copyright | Replaces footer.php entirely; 3-col to 1-col responsive; Font Awesome social icons |
</phase_requirements>

---

## Summary

Phase 2 builds `index.php` (the full homepage) and replaces the placeholder `footer.php` with the production footer. The tech stack is entirely vanilla PHP + HTML + CSS + minimal inline JS — no frameworks, no build tools, no npm. Every library needed is already loaded in `header.php` (Google Fonts: Playfair Display + Inter; Font Awesome 6.5.0; site `style.css`). Phase 1 delivered all reusable component classes: `.trip-card`, `.trip-grid`, `.section`, `.section--dark`, `.container`, `.section-header`, `.btn--gold`, `.btn--outline-white`, `.btn-accent`. These are permanent and consumed directly without modification.

The central implementation challenges for this phase are: (1) the transparent-to-solid header state machine when `$hero_page = true`, (2) the CSS-only snap-scroll carousel for mobile active trips, and (3) the destination card hover effect with `overflow:hidden` + `transform:scale()`. Everything else is standard PHP include + HTML composition using established patterns from Phase 1.

Because the footer.php replacement is shared across all future pages, it must be completed and correct before Phase 3 begins. Any structural mistake in the footer (missing closing tags, broken PHP `require`) will break every page in the project.

**Primary recommendation:** Build in section order — header flag first (so the page looks right from the top), then each section top to bottom, then replace footer.php last as a distinct step.

---

## Standard Stack

### Core (already installed, no new dependencies)

| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| PHP | 8.x (SiteGround) | Server-side templating, trips.json reading | Hosting constraint — no Node.js |
| `style.css` (project) | Phase 1 | All colors, typography, component classes | Single stylesheet pattern — no page-specific CSS files |
| `main.js` (project) | Phase 1 | Shared JS behaviors; scroll handler | Page-specific JS goes inline in the page |
| Google Fonts | CDN | Playfair Display + Inter | Already in header.php |
| Font Awesome | 6.5.0 CDN | All icons (social, stars, features) | Already in header.php |

### No New Dependencies
This phase introduces zero new libraries. All required capabilities are covered by existing Phase 1 assets.

**Installation:** None required.

---

## Architecture Patterns

### Recommended File Structure for This Phase

```
/
├── index.php                  # Homepage — built in this phase
includes/
├── header.php                 # MODIFIED: add $hero_page flag support
├── footer.php                 # REPLACED: full production footer
assets/
└── css/
    └── style.css              # EXTENDED: hero, urgency bar, destination cards,
                               #           why-baffo, testimonials, B2B banner,
                               #           footer, has-hero header state
```

### Pattern 1: Hero Page PHP Flag

**What:** Before `include 'includes/header.php'`, set `$hero_page = true`. The header template reads this variable and conditionally adds `has-hero` to `<body>`.

**When to use:** Any page with a full-viewport dark photo hero (homepage, catalog, trip pages).

**Implementation in `header.php`:**
```php
// In header.php — add to the <body> opening tag
<body<?php if (!empty($hero_page)) echo ' class="has-hero"'; ?>>
```

**Implementation in `index.php`:**
```php
<?php
require_once __DIR__ . '/includes/config.php';
require_once ROOT . '/includes/functions.php';
$page_title = 'Viaggia col Baffo — Esperienze che cambiano la vita';
$hero_page = true;
require_once ROOT . '/includes/header.php';
?>
```

### Pattern 2: Transparent-to-Solid Header CSS + JS

**What:** When `body.has-hero`, the header starts transparent with white links. On scroll past the hero, the JS toggles `.scrolled` (already exists) which makes it solid white with navy links.

**CSS additions to `style.css`:**
```css
/* Transparent header over hero */
body.has-hero #site-header {
  background: rgba(0, 0, 0, 0.3);
  border-bottom: none;
}

body.has-hero #site-header nav a {
  color: var(--white);
}

/* When scrolled, revert to solid white — .scrolled already toggled by existing JS */
body.has-hero #site-header.scrolled {
  background: var(--white);
  border-bottom: 1px solid rgba(0, 7, 68, 0.12);
}

body.has-hero #site-header.scrolled nav a {
  color: #000744;
}
```

**Key insight:** The existing scroll JS in `header.php` already toggles `.scrolled` on `window.scrollY > 10`. No JS changes needed — only CSS additions for the `has-hero` state.

### Pattern 3: Full-Viewport Hero Section

**What:** A `<section>` covering 100vh with a background photo, dark overlay, centered text and two CTAs.

```html
<section class="hero">
  <div class="hero__overlay"></div>
  <div class="hero__content">
    <h1 class="hero__tagline">Viaggia col Baffo</h1>
    <p class="hero__subline">E non cambi mai più</p>
    <p class="hero__sub">Piccoli gruppi. Lorenzo sempre con te. Un'esperienza unica.</p>
    <div class="hero__ctas">
      <a href="/viaggi" class="btn btn--gold">Scopri i viaggi</a>
      <a href="/agenzie" class="btn btn--outline-white">Sei un'agenzia?</a>
    </div>
  </div>
</section>
```

```css
.hero {
  position: relative;
  height: 100vh;
  min-height: 600px;
  background-image: url('https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1920');
  background-size: cover;
  background-position: center;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
}

.hero__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to bottom, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.35) 60%, rgba(0,0,0,0.6) 100%);
}

.hero__content {
  position: relative;
  z-index: 2;
  padding: 0 1.5rem;
  max-width: 800px;
}

.hero__tagline {
  font-family: var(--font-heading);
  font-size: clamp(2.5rem, 6vw, 4.5rem);
  color: var(--white);
  margin: 0 0 0.5rem;
  text-shadow: 0 2px 12px rgba(0,0,0,0.5);
}

.hero__subline {
  font-family: var(--font-heading);
  font-style: italic;
  font-size: clamp(1.25rem, 3vw, 1.8rem);
  color: rgba(255,255,255,0.9);
  margin: 0 0 1.5rem;
}

.hero__ctas {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
}
```

**Critical:** The hero must be a direct child of `<main>` with no extra wrapper that introduces padding. The `header` is `position:sticky` so the hero fills to the top of the viewport behind it when transparent.

### Pattern 4: Urgency Bar

**What:** A hardcoded strip between hero and first content section. No PHP — static HTML.

```html
<div class="urgency-bar">
  <span class="urgency-bar__icon"><i class="fa-solid fa-fire"></i></span>
  <span class="urgency-bar__text">West America Aprile 2026</span>
  <span class="urgency-bar__pill">Ultimi 5 posti disponibili</span>
</div>
```

```css
.urgency-bar {
  background: var(--accent); /* #CC0031 red */
  color: var(--white);
  text-align: center;
  padding: 0.85rem 1.5rem;
  font-weight: 600;
  font-size: 0.95rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.urgency-bar__pill {
  background: rgba(255,255,255,0.2);
  border-radius: 20px;
  padding: 2px 12px;
  font-size: 0.85rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}
```

### Pattern 5: CSS Snap-Scroll Carousel (Mobile Active Trips)

**What:** On mobile, the trips section becomes a horizontal snap-scroll carousel. On tablet/desktop, it falls back to the standard `.trip-grid`.

```html
<div class="trips-carousel">
  <?php foreach ($active_trips as $trip): ?>
    <div class="trips-carousel__item">
      <!-- standard .trip-card markup here -->
    </div>
  <?php endforeach; ?>
</div>
```

```css
/* Mobile: snap-scroll carousel */
.trips-carousel {
  display: flex;
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  gap: 1rem;
  padding: 0 1.5rem 1rem;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none; /* Firefox */
}

.trips-carousel::-webkit-scrollbar {
  display: none; /* Chrome/Safari */
}

.trips-carousel__item {
  flex: 0 0 85%;
  scroll-snap-align: start;
}

/* Tablet+: revert to standard grid */
@media (min-width: 768px) {
  .trips-carousel {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    overflow-x: visible;
    padding: 0;
    scroll-snap-type: none;
  }
  .trips-carousel__item {
    flex: none;
  }
}

@media (min-width: 1024px) {
  .trips-carousel {
    grid-template-columns: repeat(3, 1fr);
  }
}
```

**PHP for active trips filter:**
```php
<?php
$all_trips = load_trips();
$active_trips = array_values(array_filter($all_trips, fn($t) => $t['published'] === true));
?>
```

### Pattern 6: Destination Cards Grid

**What:** 6 cards in 3x2 desktop / 2x3 tablet / 1-col mobile grid. Each card has full-bleed photo, dark overlay, Playfair name, tagline, and hover zoom with white border.

```html
<div class="dest-grid">
  <a href="/destinazione?slug=america" class="dest-card">
    <img src="https://images.unsplash.com/photo-..." alt="America" class="dest-card__img">
    <div class="dest-card__overlay"></div>
    <div class="dest-card__content">
      <h3 class="dest-card__name">America</h3>
      <p class="dest-card__tagline">Parchi, canyon e città iconiche</p>
    </div>
  </a>
  <!-- repeat for 5 more destinations -->
</div>
```

```css
.dest-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.25rem;
}

@media (min-width: 600px) {
  .dest-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1024px) {
  .dest-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

.dest-card {
  position: relative;
  display: block;
  border-radius: var(--radius);
  overflow: hidden;         /* CRITICAL: clips the scaled image */
  aspect-ratio: 4/3;
  cursor: pointer;
  border: 2px solid transparent;
  transition: border-color 0.25s ease;
}

.dest-card:hover {
  border-color: var(--white);
}

.dest-card__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.4s ease;
}

.dest-card:hover .dest-card__img {
  transform: scale(1.05);
}

.dest-card__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.65) 100%);
  transition: background 0.25s ease;
}

.dest-card:hover .dest-card__overlay {
  background: linear-gradient(to bottom, rgba(0,0,0,0.05) 0%, rgba(0,0,0,0.5) 100%);
}

.dest-card__content {
  position: absolute;
  bottom: 1.25rem;
  left: 1.25rem;
  right: 1.25rem;
  z-index: 2;
}

.dest-card__name {
  font-family: var(--font-heading);
  font-size: 1.4rem;
  color: var(--white);
  margin: 0 0 0.25rem;
  text-shadow: 0 1px 4px rgba(0,0,0,0.7);
}

.dest-card__tagline {
  font-size: 0.85rem;
  color: rgba(255,255,255,0.85);
  margin: 0;
  text-shadow: 0 1px 3px rgba(0,0,0,0.7);
}
```

**Critical:** `overflow: hidden` on `.dest-card` is mandatory — without it, `transform: scale(1.05)` on the image will overflow the card boundaries and look broken.

### Pattern 7: Why-Baffo Icon Blocks

**What:** 4 blocks in a responsive grid with Font Awesome icon, heading, and short description.

```html
<div class="why-grid">
  <div class="why-block">
    <div class="why-block__icon"><i class="fa-solid fa-user-tie fa-2x"></i></div>
    <h3 class="why-block__title">Lorenzo sempre con te</h3>
    <p class="why-block__text">Non un tour leader qualsiasi — il fondatore in persona, ogni giorno del viaggio.</p>
  </div>
  <div class="why-block">
    <div class="why-block__icon"><i class="fa-solid fa-clock-rotate-left fa-2x"></i></div>
    <h3 class="why-block__title">40 anni di esperienza</h3>
    <p class="why-block__text">Dal 1986, Y86 Travel porta italiani nel mondo con passione autentica.</p>
  </div>
  <div class="why-block">
    <div class="why-block__icon"><i class="fa-solid fa-bag-shopping fa-2x"></i></div>
    <h3 class="why-block__title">Tutto incluso, davvero</h3>
    <p class="why-block__text">Voli, hotel selezionati, trasporti, ingressi — zero sorprese sul conto finale.</p>
  </div>
  <div class="why-block">
    <div class="why-block__icon"><i class="fa-solid fa-headset fa-2x"></i></div>
    <h3 class="why-block__title">Assistenza H24</h3>
    <p class="why-block__text">In viaggio, Lorenzo e il team sono sempre raggiungibili, ovunque nel mondo.</p>
  </div>
</div>
```

```css
.why-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
}

@media (min-width: 600px) {
  .why-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (min-width: 1024px) {
  .why-grid { grid-template-columns: repeat(4, 1fr); }
}

.why-block {
  text-align: center;
  padding: 1.5rem;
}

.why-block__icon {
  color: var(--gold); /* red/gold accent */
  margin-bottom: 1rem;
}

.why-block__title {
  font-family: var(--font-heading);
  font-size: 1.1rem;
  margin: 0 0 0.5rem;
}

.why-block__text {
  color: var(--grey);
  font-size: 0.9rem;
  margin: 0;
  line-height: 1.6;
}
```

### Pattern 8: Chi è il Baffo — Two-Column Layout

```css
.founder-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 3rem;
  align-items: center;
}

@media (min-width: 768px) {
  .founder-grid {
    grid-template-columns: 1fr 1.5fr;
  }
}

.founder-portrait {
  width: 100%;
  border-radius: var(--radius);
  object-fit: cover;
  aspect-ratio: 3/4;
}

.founder-stats {
  display: flex;
  gap: 1.5rem;
  margin: 1.5rem 0;
  flex-wrap: wrap;
}

.founder-stat {
  text-align: center;
}

.founder-stat__number {
  font-family: var(--font-heading);
  font-size: 2rem;
  color: var(--gold);
  display: block;
}

.founder-stat__label {
  font-size: 0.8rem;
  color: var(--grey);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
```

### Pattern 9: Testimonial Cards

```html
<div class="testimonials-grid">
  <div class="testimonial-card">
    <div class="testimonial-card__stars">
      <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
      <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
    </div>
    <p class="testimonial-card__text">"Un'esperienza che ha cambiato il mio modo di vedere i viaggi..."</p>
    <div class="testimonial-card__author">
      <strong>Maria Rossi</strong>
      <span>West America Aprile 2026</span>
    </div>
  </div>
</div>
```

```css
.testimonials-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
}

@media (min-width: 768px) {
  .testimonials-grid { grid-template-columns: repeat(3, 1fr); }
}

.testimonial-card {
  background: var(--dark-card);
  border-radius: var(--radius);
  padding: 1.75rem;
  border: 1px solid rgba(255,255,255,0.06);
}

.testimonial-card__stars {
  color: var(--gold);
  margin-bottom: 1rem;
  font-size: 0.9rem;
}

.testimonial-card__text {
  font-style: italic;
  color: rgba(255,255,255,0.85);
  margin: 0 0 1.25rem;
  line-height: 1.7;
}

.testimonial-card__author strong {
  display: block;
  font-weight: 600;
}

.testimonial-card__author span {
  font-size: 0.8rem;
  color: var(--grey);
}
```

### Pattern 10: B2B Banner

```html
<section class="b2b-banner">
  <div class="container b2b-banner__inner">
    <div>
      <h2 class="b2b-banner__title">Sei un'agenzia di viaggi?</h2>
      <p class="b2b-banner__sub">Commissioni fino al 12%, catalogo pronto da vendere, garanzia scritta che i tuoi clienti restano tuoi.</p>
    </div>
    <a href="/agenzie" class="btn btn--gold">Scopri il programma</a>
  </div>
</section>
```

```css
.b2b-banner {
  background: var(--dark-card);
  border: 1px solid rgba(204, 0, 49, 0.4); /* accent/gold border */
  border-radius: var(--radius);
  margin: 0 1.5rem;
}

.b2b-banner__inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 2rem;
  padding: 3rem 1.5rem;
  flex-wrap: wrap;
}

.b2b-banner__title {
  font-family: var(--font-heading);
  font-size: clamp(1.5rem, 3vw, 2rem);
  margin: 0 0 0.5rem;
}

.b2b-banner__sub {
  color: var(--grey);
  margin: 0;
  max-width: 500px;
}
```

### Pattern 11: Production Footer

```html
<footer class="site-footer">
  <div class="container site-footer__grid">
    <div class="site-footer__col site-footer__col--brand">
      <a href="/"><img src="[logo url]" alt="Viaggia col Baffo" class="site-footer__logo"></a>
      <p class="site-footer__tagline">Piccoli gruppi, grandi emozioni.<br>Lorenzo con te, ogni giorno.</p>
    </div>
    <div class="site-footer__col">
      <h4 class="site-footer__heading">Naviga</h4>
      <ul class="site-footer__links">
        <li><a href="/viaggi">Viaggi</a></li>
        <li><a href="/destinazioni">Destinazioni</a></li>
        <li><a href="/agenzie">Agenzie</a></li>
        <li><a href="#">Contatti</a></li>
      </ul>
    </div>
    <div class="site-footer__col">
      <h4 class="site-footer__heading">Contatti</h4>
      <p><a href="tel:+39XXXXXXXXX"><i class="fa-solid fa-phone"></i> +39 XXX XXXXXXX</a></p>
      <p><a href="https://wa.me/39XXXXXXXXX" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a></p>
      <p><a href="mailto:info@viaggiacolbaffo.com"><i class="fa-solid fa-envelope"></i> info@viaggiacolbaffo.com</a></p>
      <div class="site-footer__social">
        <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
        <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
      </div>
      <p class="site-footer__iata"><i class="fa-solid fa-certificate"></i> IATA Accredited Agency</p>
    </div>
  </div>
  <div class="site-footer__bottom">
    <div class="container">
      <p>P.IVA placeholder &mdash; &copy; 2026 Viaggia col Baffo - Y86 Travel. Tutti i diritti riservati.</p>
    </div>
  </div>
</footer>

<script src="/assets/js/main.js"></script>
</body>
</html>
```

```css
.site-footer {
  background: var(--dark); /* #111827 */
  color: rgba(255,255,255,0.75);
  padding-top: 4rem;
  font-size: 0.9rem;
}

.site-footer__grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2.5rem;
  padding-bottom: 3rem;
}

@media (min-width: 768px) {
  .site-footer__grid {
    grid-template-columns: 1.5fr 1fr 1fr;
  }
}

.site-footer__logo {
  max-height: 45px;
  width: auto;
  margin-bottom: 1rem;
}

.site-footer__tagline {
  color: var(--grey);
  line-height: 1.6;
  margin: 0;
}

.site-footer__heading {
  font-family: var(--font-heading);
  font-size: 1rem;
  color: var(--white);
  margin: 0 0 1rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.site-footer__links {
  list-style: none;
  padding: 0;
  margin: 0;
}

.site-footer__links li {
  margin-bottom: 0.5rem;
}

.site-footer__links a,
.site-footer__col a {
  color: rgba(255,255,255,0.65);
  transition: color 0.2s ease;
}

.site-footer__links a:hover,
.site-footer__col a:hover {
  color: var(--white);
}

.site-footer__col p {
  margin: 0 0 0.6rem;
}

.site-footer__col i {
  margin-right: 0.4rem;
  color: var(--gold);
  width: 16px;
  text-align: center;
}

.site-footer__social {
  display: flex;
  gap: 1rem;
  margin: 1rem 0;
  font-size: 1.3rem;
}

.site-footer__social a {
  color: rgba(255,255,255,0.65);
  transition: color 0.2s ease;
}

.site-footer__social a:hover {
  color: var(--white);
}

.site-footer__iata {
  font-size: 0.8rem;
  color: var(--grey);
  margin: 0;
}

.site-footer__bottom {
  background: var(--primary); /* #000744 navy */
  padding: 1rem 0;
  text-align: center;
  font-size: 0.8rem;
  color: rgba(255,255,255,0.5);
  margin-top: 0;
}

.site-footer__bottom p {
  margin: 0;
}
```

### Anti-Patterns to Avoid

- **Adding `overflow:hidden` to `.hero`:** The header is `position:sticky`. If the hero has `overflow:hidden`, the sticky header may stop working in some browsers. Keep the hero `overflow` at default.
- **Using `height:100vh` without `min-height`:** On small landscape mobile screens, 100vh can be very short. Always pair with `min-height: 600px`.
- **Repeating the logo inside the hero:** The decision log explicitly forbids this — the sticky header logo is sufficient.
- **Using `.btn--gold` on a white background without testing:** `.btn--gold` is navy fill (#000744), which works on dark but also on white — verify visually. For the B2B section (dark background) it is correct.
- **Hard-coding hex values in PHP:** Use CSS variables (`var(--gold)`, `var(--dark)`) in CSS. Never embed hex values in inline PHP echo'd HTML unless for overlay gradients where CSS variables cannot be interpolated.
- **Not closing `<main>` before footer:** footer.php currently opens with `</main>` — the replacement must preserve this closing tag before the `<footer>` element.

---

## Don't Hand-Roll

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Social icons | SVG sprites or custom icons | Font Awesome 6.5.0 (already loaded) | Already a CDN dependency; consistent sizing |
| Star ratings | JS rating widget | `fa-solid fa-star` repeated 5 times in HTML | Static testimonials need no interactivity |
| Scroll snap carousel | JS carousel with event listeners | CSS `scroll-snap-type` + `overflow-x: auto` | Zero JS, native browser behavior, better mobile performance |
| Image lazy loading | JS IntersectionObserver | `loading="lazy"` attribute on `<img>` | Native HTML attribute, supported in all modern browsers |
| Smooth scroll for CTAs | JS scroll library | `scroll-behavior: smooth` on `html` element | CSS-only, no library needed |

**Key insight:** This entire phase is HTML/CSS/PHP composition. Every pattern is achievable with existing tools. Any temptation to reach for JavaScript should be resisted until the CSS-only approach is proven insufficient.

---

## Common Pitfalls

### Pitfall 1: Header Transparency Breaking When Scrolled to Top

**What goes wrong:** After the user scrolls down and back up, the header remains in `.scrolled` state (solid white) on a hero page because the JS only checks `window.scrollY > 10` — but if the page refreshes mid-scroll, state is consistent. The real issue is the CSS specificity battle between `body.has-hero #site-header` and `body.has-hero #site-header.scrolled`.

**Why it happens:** CSS specificity ties between the two selectors; order in stylesheet decides.

**How to avoid:** Ensure `body.has-hero #site-header.scrolled` rules appear AFTER `body.has-hero #site-header` rules in style.css. The `.scrolled` state must override the transparent state.

**Warning signs:** Header stays white when page loads at top with a hero photo behind it.

### Pitfall 2: Snap-Scroll Carousel Invisible Scrollbar Gap

**What goes wrong:** When `scrollbar-width: none` is not applied, the horizontal scrollbar appears at the bottom of the carousel on Windows Chrome, eating 12-15px of card height.

**Why it happens:** Windows Chrome shows scrollbars by default; macOS hides them.

**How to avoid:** Apply both `scrollbar-width: none` (Firefox) and `::-webkit-scrollbar { display: none }` (Chrome/Safari).

### Pitfall 3: Destination Card Image Overflows on Safari

**What goes wrong:** `transform: scale(1.05)` on the image causes it to overflow the card borders on Safari, even with `overflow: hidden` on the parent, when the parent uses `border-radius`.

**Why it happens:** Safari has a historical bug with `overflow: hidden` + `border-radius` + CSS transforms on children.

**How to avoid:** Add `will-change: transform` to `.dest-card__img` OR add `transform: translateZ(0)` to `.dest-card` (forces GPU compositing, which fixes the Safari bug).

**Warning signs:** Scaled image corners visible outside rounded card corners on iOS.

### Pitfall 4: footer.php Replacement Breaking All Existing Pages

**What goes wrong:** The placeholder `footer.php` closes `</main>`, loads `main.js`, closes `</body>` and `</html>`. The replacement must do the same — missing any of these breaks the entire HTML structure on every page.

**Why it happens:** footer.php is a structural include, not just content.

**How to avoid:** The footer.php replacement must end with:
```html
<script src="/assets/js/main.js"></script>
</body>
</html>
```
Never move `main.js` loading to the new footer structure — keep it at the very end of `</body>`.

### Pitfall 5: `load_trips()` Returns All Trips Including Sold-Out

**What goes wrong:** Displaying all trips from `load_trips()` without filtering shows sold-out Japan trip in the active trips section.

**Why it happens:** `load_trips()` returns everything — `published` field is the filter flag, not `status`.

**How to avoid:**
```php
$active_trips = array_values(array_filter(load_trips(), fn($t) => $t['published'] === true));
```
Note: Both West America (published=true, ultimi-posti) AND Japan (published=true, sold-out) will appear. If the intent is only non-sold-out trips, add: `&& $t['status'] !== 'sold-out'`. Based on the requirement text ("active trips"), showing sold-out as published=true is intentional — the card status pill will show "Sold Out" clearly.

### Pitfall 6: Hero `height: 100vh` Under Sticky Header

**What goes wrong:** With a `position:sticky` header (~70px tall), a `height:100vh` hero will fill the full viewport but the header sits ON TOP of it, so the visual bottom of the hero is off-screen by 70px.

**Why it happens:** `100vh` is the full viewport height including the area behind the sticky header.

**How to avoid:** This is actually DESIRED behavior — the transparent header overlays the hero, and the hero photo fills the full screen including behind the header. The content inside the hero uses `align-items: center` which centers in the full 100vh box. No fix needed; but ensure hero content is not clipped at the bottom (no `overflow:hidden`).

---

## Code Examples

### PHP Active Trips Loop
```php
<?php
$all_trips = load_trips();
$active_trips = array_values(array_filter($all_trips, fn($t) => $t['published'] === true));
?>
<div class="trips-carousel">
  <?php foreach ($active_trips as $trip): ?>
    <div class="trips-carousel__item">
      <div class="trip-card">
        <img class="trip-card__image"
             src="<?= htmlspecialchars($trip['hero_image']) ?>"
             alt="<?= htmlspecialchars($trip['title']) ?>"
             loading="lazy">
        <div class="trip-card__overlay"></div>
        <span class="trip-card__continent"><?= htmlspecialchars(ucfirst($trip['continent'])) ?></span>
        <span class="trip-card__status status--<?= htmlspecialchars($trip['status']) ?>">
          <?= htmlspecialchars(match($trip['status']) {
            'confermata'   => 'Confermata',
            'ultimi-posti' => 'Ultimi Posti',
            'sold-out'     => 'Sold Out',
            'programmata'  => 'Programmata',
            default        => ucfirst($trip['status'])
          }) ?>
        </span>
        <div class="trip-card__content">
          <h3 class="trip-card__title"><?= htmlspecialchars($trip['title']) ?></h3>
          <p class="trip-card__dates">
            <?= date('j M', strtotime($trip['date_start'])) ?> &ndash;
            <?= date('j M Y', strtotime($trip['date_end'])) ?>
          </p>
          <p class="trip-card__price">Da <?= number_format($trip['price_from'], 0, ',', '.') ?> &euro;</p>
          <a class="trip-card__cta btn btn--gold"
             href="/viaggio/<?= htmlspecialchars($trip['slug']) ?>">Scopri il viaggio</a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
```

### index.php Bootstrap Pattern
```php
<?php
require_once __DIR__ . '/includes/config.php';
require_once ROOT . '/includes/functions.php';
$page_title = 'Viaggia col Baffo — Esperienze che cambiano la vita';
$hero_page = true;
require_once ROOT . '/includes/header.php';
?>
<!-- hero, sections, etc. -->
<?php require_once ROOT . '/includes/footer.php'; ?>
```

### Recommended Unsplash URLs (Claude's Discretion)

Suggested cinematic photos matching the dark/atmospheric brand:

| Use | URL |
|-----|-----|
| Hero background | `https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1920` (mountain road, dark) |
| America | `https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=800` (canyon) |
| Asia | `https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=800` (Japan temple) |
| Europa | `https://images.unsplash.com/photo-1467269204594-9661b134dd2b?w=800` (Paris rooftops) |
| Africa | `https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?w=800` (savanna) |
| Oceania | `https://images.unsplash.com/photo-1523482580672-f109ba8cb9be?w=800` (Sydney Opera House) |
| Medio Oriente | `https://images.unsplash.com/photo-1548991879-4099e6f5b1f8?w=800` (desert dunes) |
| Lorenzo portrait | `https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800` (man in travel context) |

All Unsplash URLs are direct CDN links — no API key needed for display. The `?w=` parameter controls delivery size.

### Suggested Italian Testimonials (Claude's Discretion)

```
Maria R. — West America Aprile 2026:
"Non avevo mai fatto un viaggio così. Lorenzo conosce ogni angolo come casa sua.
Tornerò sicuramente con lui. Indimenticabile."

Gianluca P. — Giappone Classico 2025:
"40 anni di esperienza si sentono ad ogni tappa. Lorenzo ha anticipato ogni nostra domanda
e risolto ogni piccolo intoppo prima che ce ne accorgessimo."

Francesca e Luca D. — West America Aprile 2026:
"Partiti come turisti, tornati come viaggiatori. La differenza è Lorenzo: una persona vera,
non una guida di professione."
```

---

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| JS-driven carousel (Swiper, Slick) | CSS `scroll-snap-type` | ~2019 (wide browser support ~2022) | Zero JS dependency for mobile carousels |
| `loading="lazy"` polyfill via JS | Native HTML attribute | 2019+ (all modern browsers) | No JS needed for below-fold image deferral |
| `position: fixed` header with scroll detection | `position: sticky` | CSS3 (stable ~2018) | No JS needed for sticky header positioning |
| Multiple CSS files per page | Single `style.css` + CSS variables | Project decision (Phase 1) | Consistent token system; no specificity conflicts |

---

## Open Questions

1. **Destination slugs for `.htaccess` clean URLs**
   - What we know: `.htaccess` from Phase 1 handles `/viaggio/slug` and `/destinazione/slug` rewrites
   - What's unclear: Whether `/destinazioni` (plural) or `/destinazione` (singular) is the canonical URL pattern for the destination index page (no destination index exists yet — Phase 5)
   - Recommendation: Use `destinazione.php?slug=america` for links from homepage (as specified in decisions). The clean URL rewrite will handle this when Phase 5 builds destinazione.php.

2. **WhatsApp number in footer**
   - What we know: `WHATSAPP_NUMBER` constant is defined in `config.php` as `'+39 XXX XXXXXXX'` (placeholder)
   - What's unclear: The actual number for the `wa.me/` link format (must be digits only, no spaces)
   - Recommendation: Use `define('WHATSAPP_NUMBER', '+39 XXX XXXXXXX')` as-is and construct the wa.me URL with `str_replace([' ', '+'], ['', ''], WHATSAPP_NUMBER)` in the footer template.

---

## Validation Architecture

### Test Framework

| Property | Value |
|----------|-------|
| Framework | None (PHP/HTML/CSS project — no test framework installed) |
| Config file | None |
| Quick run command | Manual browser load of index.php via local server or SiteGround staging |
| Full suite command | Visual review checklist + PHP lint (`php -l index.php`) |

### Phase Requirements → Test Map

| Req ID | Behavior | Test Type | Automated Command | File Exists? |
|--------|----------|-----------|-------------------|-------------|
| HOME-01 | Hero fills 100vh with photo, tagline, 2 CTAs | Visual (browser) | `php -l index.php` (syntax) | ❌ Wave 0 |
| HOME-02 | Urgency bar appears below hero with correct text | Visual (browser) | `php -l index.php` | ❌ Wave 0 |
| HOME-03 | Active trips grid reads published=true from trips.json | PHP output check | `php -r "require 'includes/config.php'; require 'includes/functions.php'; $t=array_filter(load_trips(),fn($x)=>$x['published']===true); echo count($t).' active trips\n';"` | ❌ Wave 0 |
| HOME-04 | Destination cards grid renders 6 destinations with hover | Visual (browser) | `php -l index.php` | ❌ Wave 0 |
| HOME-05 | 4 why-Baffo blocks render with icons | Visual (browser) | `php -l index.php` | ❌ Wave 0 |
| HOME-06 | Founder section renders with portrait and story | Visual (browser) | `php -l index.php` | ❌ Wave 0 |
| HOME-07 | 3 testimonial cards with 5 stars each | Visual (browser) | `php -l index.php` | ❌ Wave 0 |
| HOME-08 | B2B banner renders with CTA to agenzie.php | Visual (browser) | `php -l index.php` | ❌ Wave 0 |
| HOME-09 | Footer: all columns, social icons, IATA, P.IVA | Visual (browser) | `php -l index.php` + `php -l includes/footer.php` | ❌ Wave 0 |

### Sampling Rate
- **Per task commit:** `php -l index.php && php -l includes/footer.php && php -l includes/header.php`
- **Per wave merge:** PHP lint on all modified files + visual browser review
- **Phase gate:** All HOME-0X requirements visually confirmed before `/gsd:verify-work`

### Wave 0 Gaps
- [ ] `index.php` — main deliverable, does not exist yet
- [ ] `includes/footer.php` — requires replacement (currently placeholder)
- [ ] `includes/header.php` — requires `$hero_page` flag addition
- [ ] `assets/css/style.css` — requires hero, carousel, destination card, why-grid, testimonial, footer CSS additions

*(No test framework installation needed — PHP lint is sufficient for syntax validation; visual review covers behavior)*

---

## Sources

### Primary (HIGH confidence)
- Direct inspection of `includes/header.php`, `includes/footer.php`, `includes/functions.php`, `includes/config.php` — Phase 1 output
- Direct inspection of `assets/css/style.css` — confirmed all existing class names, CSS variables, and button variants
- Direct inspection of `data/trips.json` — confirmed data structure, `published` field semantics, `status` values
- Direct inspection of `.planning/phases/02-homepage/02-CONTEXT.md` — all locked decisions
- Direct inspection of `.planning/REQUIREMENTS.md` — HOME-01 through HOME-09 specifications

### Secondary (MEDIUM confidence)
- CSS `scroll-snap-type` specification: supported in all modern browsers since 2019 (MDN-level knowledge, not re-verified via WebSearch for this known-stable feature)
- Safari `overflow:hidden` + `border-radius` + CSS transform bug: well-documented community pattern; fix via `will-change: transform` or `transform: translateZ(0)` is standard

### Tertiary (LOW confidence)
- Unsplash URL suggestions: based on training knowledge of Unsplash photo IDs — should be visually verified before commit

---

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH — fully verified by reading Phase 1 files; no new dependencies
- Architecture: HIGH — all patterns derive from existing classes and locked decisions
- Pitfalls: HIGH (PHP/CSS) / MEDIUM (Safari transform bug) — Safari bug is well-known, not re-verified against current Safari release notes
- Validation: HIGH — PHP lint command is deterministic; visual review criteria derived from requirements

**Research date:** 2026-03-06
**Valid until:** 2026-09-06 (stable tech — PHP, CSS, HTML; no framework versions to track)
