# Phase 1: Foundation - Research

**Researched:** 2026-03-06
**Domain:** PHP shared hosting deployment, CSS design system, JSON data layer
**Confidence:** HIGH

---

<user_constraints>
## User Constraints (from CONTEXT.md)

### Locked Decisions

- **GitHub Actions Deploy:** FTP host `ftp.viaggiacolbaffo.com`, secrets `FTP_SERVER` / `FTP_USERNAME` / `FTP_PASSWORD` already created. Target path `/nuovo.viaggiacolbaffo.com/public_html/`. Trigger: push to `main`. Exclude: `.git/`, `.claude/`, `.planning/`, `README.md`.
- **Directory Structure:** Public PHP pages in root; includes in `includes/`; admin in `admin/`; API in `api/`; data in `data/`; CSS at `assets/css/style.css`; JS at `assets/js/main.js`; images at `assets/img/` with `.gitkeep`.
- **Design Preview:** Standalone `design-preview.php` in root (deletable after Phase 1 sign-off).
- **Sample Data:** Real West America Aprile 2026 content (note: live site was unreachable from this environment — planner must pull content manually or use known values); second placeholder trip (Japan/Asia, sold-out). West America: status `ultimi-posti`, price_from `3490`, published `true`, tags `[america, road-trip, aprile, coppia, famiglia, avventura, parchi-naturali]`.
- **config.php values:** Admin password `Admin2025!` (plain text, change comment for Lorenzo); WhatsApp `+39 XXX XXXXXXX` placeholder; Tally URLs empty string placeholders; OpenAI API key empty string.
- **Stack:** PHP + vanilla JS only. No npm, no build step, no external CSS libraries. Font Awesome 6 CDN, Google Fonts CDN (Playfair Display + Inter), Unsplash direct URLs.

### Claude's Discretion

- Exact section organization within style.css
- .htaccess gzip and caching headers implementation detail
- Exact structure of the design-preview.php page (as long as it demonstrates all tokens)
- How to handle the Japan placeholder trip content (invent plausible data)
- form_config structure design (infer from the actual trip requirements)

### Deferred Ideas (OUT OF SCOPE)

None — discussion stayed within phase scope.
</user_constraints>

---

<phase_requirements>
## Phase Requirements

| ID | Description | Research Support |
|----|-------------|-----------------|
| INFRA-01 | Project deploys to SiteGround via GitHub Actions FTP on push to main | SamKirkland/FTP-Deploy-Action@v4.3.6 workflow pattern; exclude glob syntax |
| INFRA-02 | .htaccess: clean URLs, force HTTPS, protect /data/, enable gzip + browser caching | Apache mod_rewrite + mod_deflate confirmed on SiteGround; Apache 2.4 Require syntax |
| INFRA-03 | /data/ directory with trips.json and tags.json, not HTTP-accessible | .htaccess `Require all denied` in /data/.htaccess |
| INFRA-04 | /assets/img/ directory with .gitkeep placeholder | Git cannot track empty directories; .gitkeep is the standard workaround |
| INFRA-05 | README.md documenting setup, trip management, webhook config, OpenAI key setup | Documentation only — no technical research needed |
| DESIGN-01 | CSS variables: full token set as specified | CSS custom properties on :root; all values specified in requirements |
| DESIGN-02 | Google Fonts (Playfair Display + Inter) + Font Awesome 6 CDN | preconnect + stylesheet link pattern; WOFF2 format standard |
| DESIGN-03 | Mobile-first responsive design: 1-col / 2-col / 3-col | CSS Grid with min-width media queries; breakpoints 768px / 1024px |
| DESIGN-04 | Trip cards: full-bleed photo, gradient overlay, badges, pills, Playfair title, price, CTA | CSS position:relative + gradient overlay pattern |
| DESIGN-05 | Cinematic section headers in Playfair Display with gold underlines; 0.3s transitions | CSS ::after pseudo-element for accent underline; transition: all 0.3s ease |
| DATA-01 | trips.json schema with all specified fields | Schema fully defined in REQUIREMENTS.md; no external library needed |
| DATA-02 | tags.json with slug + display label | Simple flat JSON array |
| DATA-03 | functions.php: load_trips(), get_trip_by_slug(), get_trips_by_continent(), get_trips_by_tag(), save_trips(), load_tags() | PHP file_get_contents + json_decode; flock for save_trips() |
| DATA-04 | Sample data: West America trip + Japan placeholder | Content from live site unavailable in research env; use known values from CONTEXT.md + invented Japan data |
</phase_requirements>

---

## Summary

Phase 1 builds the entire technical foundation on which all subsequent phases depend. It has three distinct subdomains: (1) deployment infrastructure via GitHub Actions FTP to SiteGround shared hosting, (2) a CSS design system expressed entirely through custom properties and component classes with no external CSS library, and (3) a JSON data layer with PHP utility functions.

The stack is intentionally minimal — PHP 8.2 on Apache, vanilla CSS and JS, no build tools — driven by the SiteGround shared-hosting constraint. Every technical decision in this phase must work within those constraints and must not be reworked by later phases. The FTP deploy workflow, the .htaccess rules, and the JSON schema established here are permanent fixtures.

The highest-risk items are: (1) the .htaccess configuration, because SiteGround has documented quirks with some rewrite rules and testing requires an actual deployment, and (2) the trips.json schema, because all future phases consume it directly — a schema change later breaks everything. Both must be designed defensively on the first pass.

**Primary recommendation:** Build and verify the .htaccess and deploy workflow first (Wave 1), then the data schema and PHP functions (Wave 2), then the CSS design system and design-preview.php (Wave 3). This ordering de-risks the infrastructure before building on top of it.

---

## Standard Stack

### Core

| Library / Tool | Version | Purpose | Why Standard |
|----------------|---------|---------|--------------|
| SamKirkland/FTP-Deploy-Action | v4.3.6 | GitHub Actions FTP sync to SiteGround | Standard action for FTP deploys; tracks file state to sync only changed files |
| actions/checkout | v4 | Checkout code in CI | Current standard GitHub Actions checkout action |
| PHP | 8.2 (SiteGround default) | Server-side rendering and data access | SiteGround default; no install needed |
| Apache mod_rewrite | bundled | Clean URLs, HTTPS redirect | Enabled on SiteGround shared hosting |
| Apache mod_deflate | bundled | Gzip compression | Enabled on SiteGround shared hosting |
| Google Fonts CDN | current | Playfair Display + Inter typefaces | Free CDN; preconnect pattern for performance |
| Font Awesome 6 | 6.x CDN | Icon set | Decided in context |

### No Supporting Libraries

Per project decisions: no npm packages, no CSS frameworks, no JS libraries. Everything is hand-written PHP, CSS, and JS.

**Installation:** None required — no package manager. Deploy only ships PHP/CSS/JS files via FTP.

---

## Architecture Patterns

### Recommended Project Structure

```
/ (repo root — also public_html target)
├── index.php                  # Homepage (Phase 2)
├── viaggi.php                 # Trip catalog (Phase 3)
├── viaggio.php                # Single trip (Phase 4)
├── destinazione.php           # Destination page (Phase 5)
├── agenzie.php                # B2B page (Phase 5)
├── design-preview.php         # Phase 1 design validation — delete after
├── .htaccess                  # Rewrite rules, HTTPS, protection, gzip
│
├── includes/
│   ├── config.php             # Constants: admin pass, WhatsApp, Tally, OpenAI
│   ├── functions.php          # load_trips(), get_trip_by_slug(), save_trips(), etc.
│   ├── header.php             # Shared HTML head + nav
│   └── footer.php             # Shared footer + scripts
│
├── admin/
│   └── (Phase 6)
│
├── api/
│   └── (Phase 4+)
│
├── data/
│   ├── .htaccess              # Deny all HTTP access
│   ├── trips.json
│   └── tags.json
│
├── assets/
│   ├── css/
│   │   └── style.css          # All CSS: variables, base, components, layout, pages
│   ├── js/
│   │   └── main.js            # Shared JS behaviors
│   └── img/
│       └── .gitkeep           # Keeps directory tracked by git
│
└── .github/
    └── workflows/
        └── deploy.yml         # GitHub Actions FTP deploy
```

### Pattern 1: GitHub Actions FTP Deploy

**What:** On push to `main`, sync only changed files to SiteGround via FTP.
**When to use:** Every push to `main` branch.

```yaml
# Source: https://github.com/SamKirkland/FTP-Deploy-Action
name: Deploy to SiteGround
on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Deploy via FTP
        uses: SamKirkland/FTP-Deploy-Action@v4.3.6
        with:
          server: ${{ secrets.FTP_SERVER }}
          username: ${{ secrets.FTP_USERNAME }}
          password: ${{ secrets.FTP_PASSWORD }}
          server-dir: /nuovo.viaggiacolbaffo.com/public_html/
          exclude: |
            **/.git*
            **/.git*/**
            .claude/**
            .planning/**
            README.md
```

**Critical note:** The default exclude patterns cover `.git*` but NOT `.claude/` or `.planning/`. These MUST be added explicitly. When you add custom exclude patterns you must re-add the defaults (`**/.git*`, `**/.git*/**`) or git state files will be uploaded.

### Pattern 2: .htaccess Configuration

**What:** Single root .htaccess handles clean URLs, HTTPS redirect, gzip, and browser caching. A second .htaccess inside /data/ blocks HTTP access.

```apache
# Source: Apache documentation + SiteGround KB
RewriteEngine On

# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Clean URLs — /viaggio/west-america maps to viaggio.php?slug=west-america
RewriteRule ^viaggio/([^/]+)/?$ viaggio.php?slug=$1 [L,QSA]
RewriteRule ^destinazione/([^/]+)/?$ destinazione.php?slug=$1 [L,QSA]

# Remove .php extension from other pages
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME}\.php -f
RewriteRule ^([^\.]+)$ $1.php [NC,L]

# Gzip compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/css
    AddOutputFilterByType DEFLATE application/javascript application/json
</IfModule>

# Browser caching
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
</IfModule>
```

For /data/.htaccess (Apache 2.4 syntax — confirmed on SiteGround):
```apache
# Block all HTTP access to /data/
Require all denied
```

### Pattern 3: PHP Data Layer Functions

**What:** functions.php provides the complete data access API. All future pages call these functions; they never read JSON directly.

```php
<?php
// Source: PHP.net manual — file_get_contents, json_decode, flock
define('DATA_DIR', __DIR__ . '/../data/');

function load_trips(): array {
    $file = DATA_DIR . 'trips.json';
    if (!file_exists($file)) return [];
    $json = file_get_contents($file);
    return json_decode($json, true) ?? [];
}

function get_trip_by_slug(string $slug): ?array {
    $trips = load_trips();
    foreach ($trips as $trip) {
        if ($trip['slug'] === $slug) return $trip;
    }
    return null;
}

function get_trips_by_continent(string $continent): array {
    return array_filter(load_trips(), fn($t) => $t['continent'] === $continent);
}

function get_trips_by_tag(string $tag): array {
    return array_filter(load_trips(), fn($t) => in_array($tag, $t['tags'] ?? []));
}

function save_trips(array $trips): bool {
    $file = DATA_DIR . 'trips.json';
    $json = json_encode(array_values($trips), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $fp = fopen($file, 'w');
    if (!$fp) return false;
    flock($fp, LOCK_EX);
    fwrite($fp, $json);
    flock($fp, LOCK_UN);
    fclose($fp);
    return true;
}

function load_tags(): array {
    $file = DATA_DIR . 'tags.json';
    if (!file_exists($file)) return [];
    return json_decode(file_get_contents($file), true) ?? [];
}
```

### Pattern 4: CSS Design System Variables

**What:** All design tokens defined on `:root` in style.css section 1. All other CSS references only variables, never raw values.

```css
/* Source: REQUIREMENTS.md DESIGN-01 spec */
:root {
  /* Colors */
  --black: #0D0D0D;
  --gold: #C9A84C;
  --gold-light: #e8c76a;
  --white: #FFFFFF;
  --dark: #1a1a1a;
  --dark-card: #222222;
  --grey: #888888;

  /* Status colors */
  --status-green: #2ecc71;
  --status-orange: #e67e22;
  --status-red: #e74c3c;

  /* Typography */
  --font-heading: 'Playfair Display', serif;
  --font-body: 'Inter', sans-serif;

  /* UI */
  --radius: 12px;
  --shadow: 0 4px 24px rgba(0,0,0,0.4);
  --transition: all 0.3s ease;
}
```

### Pattern 5: Google Fonts Load Pattern

**What:** Preconnect hints before the stylesheet link reduces latency.

```html
<!-- Source: Google Fonts official embed + CDNPlanet preconnect guide -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
```

**Note:** `crossorigin` is required on the `fonts.gstatic.com` preconnect — without it the preconnect is ignored for font assets.

### Pattern 6: Mobile-First Responsive Grid

**What:** CSS Grid with progressive enhancement via `min-width` media queries.

```css
/* Mobile-first: 1 column base */
.trip-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
}

/* Tablet: 2 columns */
@media (min-width: 768px) {
  .trip-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* Desktop: 3 columns */
@media (min-width: 1024px) {
  .trip-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}
```

### Pattern 7: Trip Card Component

**What:** Full-bleed image card with gradient overlay, positioned badges and status pill.

```css
.trip-card {
  position: relative;
  border-radius: var(--radius);
  overflow: hidden;
  background: var(--dark-card);
  box-shadow: var(--shadow);
  transition: var(--transition);
}

.trip-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 32px rgba(0,0,0,0.6);
}

.trip-card__image {
  width: 100%;
  aspect-ratio: 4/3;
  object-fit: cover;
  display: block;
}

.trip-card__overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 60%;
  background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
}

.trip-card__continent {
  position: absolute;
  top: 1rem;
  left: 1rem;
  background: var(--gold);
  color: var(--black);
  border-radius: 999px;
  padding: 0.25rem 0.75rem;
  font-size: 0.75rem;
  font-weight: 600;
}

.trip-card__status {
  position: absolute;
  top: 1rem;
  right: 1rem;
  border-radius: 999px;
  padding: 0.25rem 0.75rem;
  font-size: 0.75rem;
  font-weight: 600;
}

.status--confermata  { background: var(--status-green); color: var(--white); }
.status--ultimi-posti { background: var(--status-orange); color: var(--white); }
.status--sold-out    { background: var(--status-red); color: var(--white); }
.status--programmata { background: var(--grey); color: var(--white); }
```

### Anti-Patterns to Avoid

- **Reading JSON on every function call without caching:** For a low-traffic site this is fine, but load_trips() is called per-request. Don't add static caching in Phase 1 — it complicates save_trips() and is premature. Keep it simple.
- **Using `ORDER allow,deny` / `Deny from all` syntax:** This is Apache 2.2 syntax. SiteGround uses Apache 2.4. Use `Require all denied` instead.
- **Including config.php from paths relative to the calling file:** Always use `__DIR__` or a defined constant, never relative paths like `../../includes/config.php`.
- **Storing .ftp-deploy-sync-state.json in the repo:** The FTP-Deploy-Action creates this state file on the server (not in the repo). Do not commit it.
- **Not including default exclude patterns when customizing `exclude`:** The action docs explicitly warn this — if you add custom patterns you must re-add `**/.git*` and `**/.git*/**` yourself.

---

## Don't Hand-Roll

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| File sync with change detection | Custom FTP script | SamKirkland/FTP-Deploy-Action@v4.3.6 | Handles state tracking, deletion, retries |
| URL routing | Custom PHP front controller | .htaccess RewriteRule per page type | Apache handles it before PHP is invoked — simpler, faster |
| File upload to server | rsync/SCP workflow | FTP-Deploy-Action (already decided) | Secrets are already configured for FTP |
| Icon set | SVG sprites | Font Awesome 6 CDN | Already decided; consistent icon vocabulary |
| Typography | System fonts | Google Fonts CDN | Brand requires Playfair Display which is not a system font |

**Key insight:** On shared hosting without SSH access, .htaccess is the only mechanism for URL routing. Don't try to route in PHP — the request must reach the right .php file first.

---

## Common Pitfalls

### Pitfall 1: Apache 2.2 vs 2.4 Access Control Syntax

**What goes wrong:** `Order deny,allow` / `Deny from all` returns a 500 error on SiteGround (Apache 2.4), not the expected 403.
**Why it happens:** Apache 2.4 deprecated the old `mod_access_compat` syntax in favor of `mod_authz_core`.
**How to avoid:** Always use `Require all denied` in /data/.htaccess. Test after first deploy by visiting `https://yourdomain.com/data/trips.json` — should see 403, not 200 or 500.
**Warning signs:** 500 Internal Server Error immediately after deploy.

### Pitfall 2: FTP Exclude Patterns Silently Failing

**What goes wrong:** `.claude/` or `.planning/` directories get uploaded to the server.
**Why it happens:** When you add ANY custom exclude patterns, the action's defaults are wiped. Users often add their custom exclusions without re-adding the git defaults.
**How to avoid:** Always include `**/.git*` and `**/.git*/**` in your custom exclude block along with `.claude/**` and `.planning/**`.
**Warning signs:** Seeing `.claude/` or `.planning/` directories in the FTP file listing after deploy.

### Pitfall 3: trips.json Schema Rigidity

**What goes wrong:** Phase 2, 3, or 4 needs a field that isn't in Phase 1's schema — requires a data migration and breaks PHP functions.
**Why it happens:** Schema designed with only Phase 1 use cases in mind.
**How to avoid:** Design trips.json schema to include ALL fields from ALL phases upfront (DATA-01 spec is comprehensive — use it fully). Even if Phase 1 doesn't render `itinerary`, the sample data should have a realistic `itinerary` array.
**Warning signs:** Phase 2 planner requests schema additions.

### Pitfall 4: PHP Path Resolution Errors

**What goes wrong:** `include 'includes/header.php'` works on some pages but fails on others depending on working directory.
**Why it happens:** PHP's `include` with a relative path is relative to the calling script's location, which changes when directory structure varies.
**How to avoid:** Define a root constant in config.php: `define('ROOT', __DIR__);` and use `require_once ROOT . '/includes/header.php';` everywhere. Or use `dirname(__DIR__)` for files inside subdirectories.
**Warning signs:** "Failed to open stream: No such file or directory" errors for included files.

### Pitfall 5: SiteGround RewriteRule and Trailing Slashes

**What goes wrong:** `/viaggio/west-america/` (with trailing slash) doesn't match the RewriteRule while `/viaggio/west-america` does, or vice versa.
**Why it happens:** Inconsistent URL patterns in links vs. RewriteRule pattern.
**How to avoid:** Add `/?` at end of regex pattern to accept both: `^viaggio/([^/]+)/?$`. All internal links should use the non-trailing-slash form consistently.
**Warning signs:** 404 on trip pages when accessed from external links that add a trailing slash.

### Pitfall 6: flock() Reliability on SiteGround NFS

**What goes wrong:** flock() may not work reliably if SiteGround uses NFS-backed storage for shared hosting.
**Why it happens:** flock() is not reliable on NFS mounts — processes on different nodes may ignore each other's locks.
**How to avoid:** For Phase 1, flock() is acceptable because trips.json is only written from admin (Phase 6) — concurrent writes are not a real risk yet. Document the limitation. If contention becomes an issue in Phase 6, use atomic write-then-rename (`file_put_contents($file . '.tmp', $json); rename($file . '.tmp', $file)`).
**Warning signs:** Corrupt trips.json after concurrent admin saves (only becomes relevant in Phase 6).

### Pitfall 7: Uploading data/trips.json Gets Overwritten on Deploy

**What goes wrong:** The FTP deploy overwrites `data/trips.json` on the server with the repo's copy, wiping any data added via admin.
**Why it happens:** FTP-Deploy-Action syncs ALL tracked files by default.
**How to avoid:** In Phase 1 this is fine — the sample data IS the correct data. In Phase 6 (admin writes trips), trips.json must be excluded from deploy. Add to exclude list in deploy.yml at that time. Document this in README.md now as a known future concern.
**Warning signs:** Admin-created trips disappear after deploy.

---

## Code Examples

### trips.json Schema (Full — all fields, all phases)

```json
[
  {
    "slug": "west-america-aprile-2026",
    "title": "West America Aprile 2026",
    "continent": "america",
    "status": "ultimi-posti",
    "published": true,
    "date_start": "2026-04-05",
    "date_end": "2026-04-19",
    "duration": "15 giorni",
    "price_from": 3490,
    "hero_image": "https://images.unsplash.com/photo-1549880338-65ddcdfd017b?w=1600",
    "gallery": [
      "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800",
      "https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=800"
    ],
    "short_description": "Un viaggio epico attraverso i parchi nazionali e le città iconiche dell'Ovest americano. Lorenzo ti accompagna personalmente.",
    "full_description": "Sedici giorni di avventura autentica...",
    "itinerary": [
      {
        "day": 1,
        "title": "Arrivo a Los Angeles",
        "description": "Trasferimento in hotel, cena di benvenuto."
      }
    ],
    "included": [
      "Voli internazionali A/R",
      "15 notti in hotel selezionati",
      "Lorenzo con voi ogni giorno",
      "Trasporti interni con van privato",
      "Ingressi ai parchi nazionali"
    ],
    "excluded": [
      "Assicurazione viaggio",
      "Pasti non menzionati nel programma",
      "Spese personali"
    ],
    "tags": ["america", "road-trip", "aprile", "coppia", "famiglia", "avventura", "parchi-naturali"],
    "form_config": {
      "room_types": [
        {"id": "doppia", "label": "Camera Doppia", "price_supplement": 0},
        {"id": "singola", "label": "Camera Singola", "price_supplement": 650},
        {"id": "tripla", "label": "Camera Tripla (per 3)", "price_supplement": -200}
      ],
      "addons": [
        {"id": "assicurazione", "label": "Assicurazione Viaggio Completa", "price": 180},
        {"id": "volo-premium", "label": "Upgrade Volo Premium Economy", "price": 450}
      ],
      "fields": ["nome", "cognome", "email", "telefono", "tipo_cliente", "numero_partecipanti", "room_type", "note"]
    },
    "webhook_url": ""
  }
]
```

### tags.json Schema

```json
[
  {"slug": "america", "label": "America"},
  {"slug": "asia", "label": "Asia"},
  {"slug": "europa", "label": "Europa"},
  {"slug": "africa", "label": "Africa"},
  {"slug": "oceania", "label": "Oceania"},
  {"slug": "medio-oriente", "label": "Medio Oriente"},
  {"slug": "road-trip", "label": "Road Trip"},
  {"slug": "aprile", "label": "Aprile"},
  {"slug": "coppia", "label": "Coppia"},
  {"slug": "famiglia", "label": "Famiglia"},
  {"slug": "avventura", "label": "Avventura"},
  {"slug": "parchi-naturali", "label": "Parchi Naturali"},
  {"slug": "cultura", "label": "Cultura"},
  {"slug": "gastronomia", "label": "Gastronomia"}
]
```

### config.php Structure

```php
<?php
// IMPORTANT: Change ADMIN_PASSWORD before going live!
define('ADMIN_PASSWORD', 'Admin2025!');
define('WHATSAPP_NUMBER', '+39 XXX XXXXXXX'); // Lorenzo: fill in your number
define('TALLY_CATALOG_URL', '');   // Lorenzo: fill in after creating Tally form
define('TALLY_B2B_URL', '');       // Lorenzo: fill in after creating Tally form
define('OPENAI_API_KEY', '');      // Optional — fill in to enable AI form generator (Phase 4)
define('DEFAULT_WEBHOOK_URL', ''); // Default webhook for form submissions
define('ROOT', __DIR__ . '/..');   // Absolute path to project root
define('DATA_DIR', ROOT . '/data/');
```

### design-preview.php Structure

The design preview page should demonstrate (at minimum):
1. All CSS variables rendered as colored swatches with hex values labeled
2. Typography scale: h1–h4 in Playfair Display, body text in Inter
3. A gold accent underline section header
4. One complete trip card (hardcoded, not from JSON) with continent badge (gold), status pill (orange for ultimi-posti), title, dates, price, CTA
5. The 3-column grid at desktop width (use 3 hardcoded cards)
6. All Font Awesome icon examples used in the project (check, times, whatsapp, map-marker, calendar, etc.)

---

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| `Order deny,allow` / `Deny from all` | `Require all denied` | Apache 2.4 (2012, standard by 2020) | Using old syntax causes 500 on SiteGround |
| HTTP-only deploy | Force HTTPS via .htaccess redirect | Standard since Let's Encrypt (2016) | SiteGround includes Let's Encrypt; .htaccess redirect is still needed |
| `@import url()` for Google Fonts | `<link>` + preconnect | Ongoing best practice | CSS @import blocks rendering; `<link>` is non-blocking |
| `font-display: auto` | `font-display: swap` (via `?display=swap` param) | ~2018 | Prevents invisible text during font load |
| `json_encode($data)` | `json_encode($data, JSON_PRETTY_PRINT \| JSON_UNESCAPED_UNICODE \| JSON_UNESCAPED_SLASHES)` | PHP 5.4+ | Human-readable JSON files; Italian characters stored as literal UTF-8 not escape sequences |

**Deprecated/outdated:**
- Apache 2.2 access control directives: Do not use `Order allow,deny` / `Deny from all` on SiteGround.
- `@font-face` self-hosted without WOFF2: Not needed since Google Fonts CDN serves WOFF2 automatically.

---

## Open Questions

1. **Live trip content for West America**
   - What we know: CONTEXT.md specifies real content from viaggiacolbaffo.com/west-america-aprile-2026/; known values: status `ultimi-posti`, price_from `3490`, tags `[america, road-trip, aprile, coppia, famiglia, avventura, parchi-naturali]`
   - What's unclear: Exact itinerary (day-by-day titles and descriptions), included/excluded list, full description text, actual dates, gallery image URLs — the live site was unreachable from the research environment.
   - Recommendation: The implementer must open the live WordPress page in a browser and extract content manually for the itinerary array. The schema is correct; the content values are the gap. Use reasonable placeholder content that matches the Italy-based luxury travel product if the live site remains inaccessible.

2. **SiteGround server-dir path trailing slash**
   - What we know: FTP-Deploy-Action requires server-dir to end with `/`
   - What's unclear: Whether `/nuovo.viaggiacolbaffo.com/public_html/` is the exact path as seen by the FTP user, or whether the FTP home is already set to `public_html/`
   - Recommendation: The GitHub Secrets are already configured; use the exact path from CONTEXT.md. If deploy fails with "directory not found", the FTP root may already be at `public_html/` and the path should be adjusted to `nuovo.viaggiacolbaffo.com/public_html/` (without leading slash).

3. **SiteGround mod_expires availability**
   - What we know: mod_deflate is confirmed available; mod_rewrite is confirmed available
   - What's unclear: Whether mod_expires is enabled for shared hosting on SiteGround (it usually is on cPanel hosts, but not confirmed)
   - Recommendation: Wrap caching directives in `<IfModule mod_expires.c>` — if the module is absent, the block is silently ignored rather than causing a 500. This is the safe default.

---

## Validation Architecture

> nyquist_validation is enabled in config.json.

### Test Framework

| Property | Value |
|----------|-------|
| Framework | None detected — PHP project with no test runner configured |
| Config file | None — see Wave 0 gap below |
| Quick run command | `php -l <file>` (syntax check only — no unit test runner) |
| Full suite command | Manual browser verification via design-preview.php |

**Note:** This is a pure PHP + vanilla CSS/JS project with no package.json and no test framework. The appropriate validation is:
1. PHP syntax checks (`php -l`) for all .php files
2. Browser-based smoke tests against the deployed or locally-served site
3. The `design-preview.php` page IS the automated design validation artifact

### Phase Requirements to Test Map

| Req ID | Behavior | Test Type | Automated Command | File Exists? |
|--------|----------|-----------|-------------------|-------------|
| INFRA-01 | Push to main triggers deploy; files appear on SiteGround | smoke | Deploy workflow completes without error (check GitHub Actions run status) | ❌ Wave 0 (create deploy.yml) |
| INFRA-02 | Clean URL /viaggio/west-america works; HTTPS forced; /data/ returns 403 | smoke/manual | `curl -I https://nuovo.viaggiacolbaffo.com/viaggio/test` | ❌ Wave 0 (create .htaccess) |
| INFRA-03 | /data/trips.json returns HTTP 403 | smoke/manual | `curl -I https://nuovo.viaggiacolbaffo.com/data/trips.json` (expect 403) | ❌ Wave 0 |
| INFRA-04 | /assets/img/ exists with .gitkeep | file-existence | `ls assets/img/.gitkeep` | ❌ Wave 0 |
| INFRA-05 | README.md exists and covers required topics | manual | Open README.md in browser/editor | ❌ Wave 0 |
| DESIGN-01 | CSS variables defined and rendering | manual/visual | design-preview.php color swatches | ❌ Wave 0 (create design-preview.php) |
| DESIGN-02 | Google Fonts + Font Awesome loading | manual/visual | Check Network tab in devtools on design-preview.php | ❌ Wave 0 |
| DESIGN-03 | 1/2/3 column grid at correct breakpoints | manual/visual | Resize browser on design-preview.php | ❌ Wave 0 |
| DESIGN-04 | Trip card renders with all elements | manual/visual | design-preview.php card section | ❌ Wave 0 |
| DESIGN-05 | Section headers + transitions | manual/visual | design-preview.php typography section | ❌ Wave 0 |
| DATA-01 | trips.json contains all required fields | php-lint + manual | `php -r "json_decode(file_get_contents('data/trips.json'), true);"` | ❌ Wave 0 |
| DATA-02 | tags.json contains all tag slugs/labels | php-lint + manual | `php -r "print_r(json_decode(file_get_contents('data/tags.json'), true));"` | ❌ Wave 0 |
| DATA-03 | PHP functions return correct data | manual | Create test-data.php, call each function, dump output | ❌ Wave 0 |
| DATA-04 | West America + Japan trips present and correct | manual | `php -r "require 'includes/functions.php'; print_r(get_trip_by_slug('west-america-aprile-2026'));"` | ❌ Wave 0 |

### Sampling Rate

- **Per task commit:** `php -l <file>` on each modified PHP file (catches syntax errors instantly)
- **Per wave merge:** Deploy to SiteGround and run curl checks for INFRA-02, INFRA-03
- **Phase gate:** design-preview.php renders correctly in browser at mobile/tablet/desktop widths; all curl checks pass; PHP function output verified before `/gsd:verify-work`

### Wave 0 Gaps (all files are new — this is phase 1 from scratch)

- [ ] `.github/workflows/deploy.yml` — INFRA-01
- [ ] `.htaccess` (root) — INFRA-02
- [ ] `data/.htaccess` — INFRA-03
- [ ] `assets/img/.gitkeep` — INFRA-04
- [ ] `README.md` — INFRA-05
- [ ] `assets/css/style.css` — DESIGN-01 through DESIGN-05
- [ ] `design-preview.php` — DESIGN-01 through DESIGN-05 visual validation
- [ ] `includes/config.php` — referenced by all future phases
- [ ] `includes/functions.php` — DATA-03
- [ ] `data/trips.json` — DATA-01, DATA-04
- [ ] `data/tags.json` — DATA-02
- [ ] `includes/header.php` — shared by all pages (stub only in Phase 1)
- [ ] `includes/footer.php` — shared by all pages (stub only in Phase 1)
- [ ] `assets/js/main.js` — empty or minimal stub

---

## Sources

### Primary (HIGH confidence)

- [SamKirkland/FTP-Deploy-Action GitHub](https://github.com/SamKirkland/FTP-Deploy-Action) — workflow YAML syntax, all config options, version v4.3.6, exclude pattern behavior
- [PHP Manual: flock](https://www.php.net/manual/en/function.flock.php) — file locking behavior and NFS caveat
- [PHP Manual: json_encode](https://www.php.net/manual/en/function.json-encode.php) — flag constants and behavior
- [Apache mod_deflate docs](https://httpd.apache.org/docs/current/mod/mod_deflate.html) — AddOutputFilterByType syntax
- [SiteGround KB: mod_rewrite](https://www.siteground.com/kb/how_can_i_enable_modrewrite_module/) — confirmed enabled on shared hosting
- [SiteGround Blog: PHP 8.2 default](https://www.siteground.com/blog/php-8-2-becomes-default-version/) — PHP version confirmation
- [Google Fonts: Playfair Display](https://fonts.google.com/specimen/Playfair+Display) — font availability and embed code

### Secondary (MEDIUM confidence)

- [CDNPlanet: Faster Google Webfonts with Preconnect](https://www.cdnplanet.com/blog/faster-google-webfonts-preconnect) — preconnect + crossorigin pattern, verified against Google Fonts official embed behavior
- [SiteGround KB: Apache AllowOverride](https://www.siteground.com/kb/is_it_possible_to_set_apaches_allowoverride_directive_to_all/) — AllowOverride All confirmed for shared hosting
- [ReviewPlan: Gzip on SiteGround](https://www.reviewplan.com/siteground-enable-gzip-compression/) — gzip enabled by default; manual .htaccess approach also valid
- [GitHub Actions Marketplace: FTP Deploy](https://github.com/marketplace/actions/ftp-deploy) — version and config options cross-reference

### Tertiary (LOW confidence — flag for validation)

- SiteGround mod_expires availability on shared hosting: not explicitly confirmed in official KB; assumed available based on cPanel shared hosting norms. Mitigation: `<IfModule>` wrapper makes it safe to include regardless.
- NFS-based flock() unreliability on SiteGround specifically: documented as general PHP/NFS issue, not SiteGround-specific. Treat as LOW risk for Phase 1 (no concurrent admin writes yet).

---

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH — FTP-Deploy-Action version, PHP 8.2, Apache modules all confirmed via official sources
- Architecture: HIGH — directory structure, .htaccess patterns, PHP function signatures all specified in CONTEXT.md and REQUIREMENTS.md with no ambiguity
- Pitfalls: HIGH for Apache 2.4 syntax and FTP exclude pattern issues (confirmed in official docs and action issues); MEDIUM for flock() NFS reliability (general PHP issue, not SiteGround-confirmed)
- Data schema: HIGH — DATA-01 spec is fully detailed in REQUIREMENTS.md; schema design is deterministic from requirements
- Live trip content: LOW — viaggiacolbaffo.com unreachable from research environment; manual extraction required

**Research date:** 2026-03-06
**Valid until:** 2026-09-06 (6 months — stable Apache/PHP/FTP tooling; Google Fonts CDN URLs stable; re-verify FTP-Deploy-Action version before use if more than 3 months pass)
